<?php
/**
 * Anmeldung für den Verwaltungsbereich: ein Passwort (bcrypt-Hash), Sitzungs-Cookie,
 * CSRF-Schutz und Sperre nach zu vielen Fehlversuchen.
 */
declare(strict_types=1);

define('ADMIN_FILE', DATA_DIR . '/admin.php');       // enthält nur den Passwort-Hash
define('ATTEMPTS_FILE', DATA_DIR . '/login-attempts.json');
const LOGIN_MAX_FAILS  = 5;
const LOGIN_LOCK_SECS  = 900;  // 15 Minuten
const SESSION_IDLE_MAX = 7200; // nach 2 Stunden Inaktivität abmelden
const PASSWORD_MIN_LEN = 10;

function admin_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/') . '/';
    session_name('tg_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => $path,
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    ini_set('session.use_strict_mode', '1');
    session_start();

    if (!empty($_SESSION['auth']) && (time() - (int)($_SESSION['seen'] ?? 0)) > SESSION_IDLE_MAX) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['flash'] = ['info', 'Sie wurden nach längerer Inaktivität abgemeldet.'];
    }
    $_SESSION['seen'] = time();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
}

function admin_is_setup(): bool
{
    return is_file(ADMIN_FILE);
}

function admin_hash(): string
{
    $cfg = include ADMIN_FILE;
    return is_array($cfg) ? (string)($cfg['hash'] ?? '') : '';
}

function admin_set_password(string $pw): void
{
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $php = "<?php\n// Automatisch erzeugt – Passwort-Hash für den Verwaltungsbereich.\n// Zum Zurücksetzen des Passworts diese Datei löschen und /admin/ neu aufrufen.\nreturn " . var_export(['hash' => $hash, 'changed' => date('c')], true) . ";\n";
    if (file_put_contents(ADMIN_FILE, $php, LOCK_EX) === false) {
        throw new RuntimeException('Passwort konnte nicht gespeichert werden (Schreibrechte für den Ordner „data“ prüfen).');
    }
    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate(ADMIN_FILE, true);
    }
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['auth']);
}

function csrf_token(): string
{
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $t = (string)($_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($t === '' || !hash_equals(csrf_token(), $t)) {
        http_response_code(400);
        exit('Sicherheitsprüfung fehlgeschlagen. Bitte die Seite neu laden und erneut versuchen.');
    }
}

/* ---------- Schutz vor Passwort-Raten ---------- */

function attempts_key(): string
{
    return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . __DIR__);
}

function attempts_load(): array
{
    $a = json_file_read(ATTEMPTS_FILE) ?? [];
    $now = time();
    return array_filter($a, function ($r) use ($now) {
        return is_array($r) && ($now - (int)($r['t'] ?? 0)) < LOGIN_LOCK_SECS;
    });
}

function login_locked_for(): int
{
    $r = attempts_load()[attempts_key()] ?? null;
    if (!$r || (int)$r['n'] < LOGIN_MAX_FAILS) {
        return 0;
    }
    return max(0, LOGIN_LOCK_SECS - (time() - (int)$r['t']));
}

function login_register_fail(): void
{
    $a = attempts_load();
    $k = attempts_key();
    $a[$k] = ['n' => (int)($a[$k]['n'] ?? 0) + 1, 't' => time()];
    @file_put_contents(ATTEMPTS_FILE, json_encode($a), LOCK_EX);
}

function login_clear_fails(): void
{
    $a = attempts_load();
    unset($a[attempts_key()]);
    @file_put_contents(ATTEMPTS_FILE, json_encode($a), LOCK_EX);
}

function admin_login(string $pw): bool
{
    if (login_locked_for() > 0) {
        return false;
    }
    if ($pw !== '' && password_verify($pw, admin_hash())) {
        login_clear_fails();
        session_regenerate_id(true);
        $_SESSION['auth'] = true;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return true;
    }
    login_register_fail();
    usleep(600000); // bremst automatisiertes Raten
    return false;
}

function admin_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $p['path'], 'secure' => $p['secure'], 'httponly' => true, 'samesite' => 'Strict']);
    }
    session_destroy();
}
