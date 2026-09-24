<?php
/**
 * Verwaltungsbereich Terra & Garten
 * Projekte & Bilder, Rechtstexte, Kontaktdaten, Sicherungen, Passwort.
 */
declare(strict_types=1);
require dirname(__DIR__) . '/inc/bootstrap.php';
require dirname(__DIR__) . '/inc/images.php';
require dirname(__DIR__) . '/inc/auth.php';

header('X-Frame-Options: DENY');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:; style-src 'self' 'unsafe-inline'; script-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");

admin_session_start();

$page = (string)($_GET['p'] ?? 'start');
$post = $_SERVER['REQUEST_METHOD'] === 'POST';

function go(string $query = '', ?string $type = null, string $msg = ''): void
{
    if ($type) {
        $_SESSION['flash'] = [$type, $msg];
    }
    header('Location: ./' . ($query !== '' ? '?' . $query : ''), true, 303);
    exit;
}

function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function str_in(string $key, int $max = 2000): string
{
    $v = trim((string)($_POST[$key] ?? ''));
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v) ?? '';
    return mb_substr($v, 0, $max);
}

function &find_project(array &$d, string $id)
{
    foreach ($d['projects'] as $i => &$p) {
        if ($p['id'] === $id) {
            return $p;
        }
    }
    $null = null;
    return $null;
}

function project_index(array $d, string $id): int
{
    foreach ($d['projects'] as $i => $p) {
        if ($p['id'] === $id) {
            return $i;
        }
    }
    return -1;
}

function upload_error_text(int $code): string
{
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'Die Datei ist zu groß für den Server (max. ' . ini_get('upload_max_filesize') . ').';
        case UPLOAD_ERR_PARTIAL:
            return 'Die Datei wurde nur teilweise übertragen. Bitte erneut versuchen.';
        case UPLOAD_ERR_NO_FILE:
            return 'Es wurde keine Datei ausgewählt.';
        default:
            return 'Upload fehlgeschlagen (Fehler ' . $code . ').';
    }
}

/* =====================================================================
 * Ersteinrichtung & Anmeldung
 * ===================================================================== */
if (!admin_is_setup()) {
    $error = '';
    if ($post && ($_POST['action'] ?? '') === 'setup') {
        csrf_check();
        $pw = (string)($_POST['pw'] ?? '');
        if (mb_strlen($pw) < PASSWORD_MIN_LEN) {
            $error = 'Das Passwort muss mindestens ' . PASSWORD_MIN_LEN . ' Zeichen lang sein.';
        } elseif ($pw !== (string)($_POST['pw2'] ?? '')) {
            $error = 'Die beiden Passwörter stimmen nicht überein.';
        } else {
            admin_set_password($pw);
            session_regenerate_id(true);
            $_SESSION['auth'] = true;
            go('', 'ok', 'Passwort gespeichert – willkommen im Verwaltungsbereich!');
        }
    }
    view_auth('Ersteinrichtung', 'setup', $error);
    exit;
}

if (!admin_logged_in()) {
    $error = '';
    if ($post && ($_POST['action'] ?? '') === 'login') {
        csrf_check();
        if (admin_login((string)($_POST['pw'] ?? ''))) {
            go();
        }
        $wait = login_locked_for();
        $error = $wait > 0
            ? 'Zu viele Fehlversuche. Bitte in ' . ceil($wait / 60) . ' Minuten erneut versuchen.'
            : 'Das Passwort ist nicht korrekt.';
    }
    if ($page === 'upload') {
        json_out(['ok' => false, 'error' => 'Sie sind nicht mehr angemeldet. Bitte Seite neu laden.'], 401);
    }
    view_auth('Anmelden', 'login', $error);
    exit;
}

/* =====================================================================
 * Aktionen (POST)
 * ===================================================================== */
if ($post) {
    csrf_check();
    $action = (string)($_POST['action'] ?? '');
    $d = content_load();
    try {
        switch ($action) {
            case 'logout':
                admin_logout();
                header('Location: ./', true, 303);
                exit;

            case 'upload':
                $id = (string)($_POST['id'] ?? '');
                $slot = (string)($_POST['slot'] ?? 'gallery');
                $p = &find_project($d, $id);
                if ($p === null) {
                    json_out(['ok' => false, 'error' => 'Projekt nicht gefunden.'], 404);
                }
                $f = $_FILES['file'] ?? null;
                if (!$f || !is_array($f) || is_array($f['error'])) {
                    json_out(['ok' => false, 'error' => upload_error_text(UPLOAD_ERR_NO_FILE)], 400);
                }
                if ($f['error'] !== UPLOAD_ERR_OK) {
                    json_out(['ok' => false, 'error' => upload_error_text((int)$f['error'])], 400);
                }
                if (!is_uploaded_file($f['tmp_name'])) {
                    json_out(['ok' => false, 'error' => 'Ungültiger Upload.'], 400);
                }
                $img = image_store($f['tmp_name']);
                if ($slot === 'before' || $slot === 'after') {
                    $alt = is_array($p[$slot]) ? ($p[$slot]['alt'] ?? '') : '';
                    image_delete($p[$slot]);
                    unset($img['thumb']);
                    $p[$slot] = $img + ['alt' => $alt !== '' ? $alt : ($slot === 'before' ? 'Vorher' : 'Nachher')];
                } else {
                    $p['images'][] = $img + ['caption' => ''];
                }
                unset($p);
                content_save($d);
                json_out(['ok' => true]);

            case 'project_create':
                $title = str_in('title', 120);
                if ($title === '') {
                    go('p=projekte', 'error', 'Bitte einen Titel für das Projekt eingeben.');
                }
                $id = 'p' . bin2hex(random_bytes(4));
                array_unshift($d['projects'], ['id' => $id, 'title' => $title, 'intro' => '', 'visible' => false, 'before' => null, 'after' => null, 'images' => []]);
                content_save($d);
                go('p=projekt&id=' . $id, 'ok', 'Projekt angelegt. Laden Sie jetzt Bilder hoch und schalten Sie es danach sichtbar.');

            case 'project_move':
                $i = project_index($d, (string)($_POST['id'] ?? ''));
                $j = $i + (($_POST['dir'] ?? '') === 'up' ? -1 : 1);
                if ($i >= 0 && $j >= 0 && $j < count($d['projects'])) {
                    [$d['projects'][$i], $d['projects'][$j]] = [$d['projects'][$j], $d['projects'][$i]];
                    content_save($d);
                }
                go('p=projekte');

            case 'project_delete':
                $i = project_index($d, (string)($_POST['id'] ?? ''));
                if ($i >= 0) {
                    $p = $d['projects'][$i];
                    array_splice($d['projects'], $i, 1);
                    content_save($d);
                    image_delete($p['before']);
                    image_delete($p['after']);
                    foreach ($p['images'] as $img) {
                        image_delete($img);
                    }
                    go('p=projekte', 'ok', 'Projekt „' . $p['title'] . '“ wurde gelöscht.');
                }
                go('p=projekte');

            case 'project_save':
                $id = (string)($_POST['id'] ?? '');
                $p = &find_project($d, $id);
                if ($p === null) {
                    go('p=projekte', 'error', 'Projekt nicht gefunden.');
                }
                $p['title'] = str_in('title', 120) ?: $p['title'];
                $p['intro'] = str_in('intro', 600);
                $p['visible'] = !empty($_POST['visible']);
                $caps = (array)($_POST['caption'] ?? []);
                foreach ($p['images'] as $k => &$img) {
                    if (isset($caps[$k])) {
                        $img['caption'] = mb_substr(trim((string)$caps[$k]), 0, 200);
                    }
                }
                unset($img);
                foreach (['before', 'after'] as $slot) {
                    if (is_array($p[$slot]) && isset($_POST['alt_' . $slot])) {
                        $p[$slot]['alt'] = str_in('alt_' . $slot, 200);
                    }
                }
                $op = (string)($_POST['op'] ?? 'save');
                $up = (int)($_POST['uploaded'] ?? 0);
                $msg = $up > 0 ? ($up === 1 ? '1 Bild hochgeladen.' : $up . ' Bilder hochgeladen.') : 'Änderungen gespeichert.';
                if (preg_match('/^img_(up|down|del):(\d+)$/', $op, $m)) {
                    $k = (int)$m[2];
                    $n = count($p['images']);
                    if ($k < $n) {
                        if ($m[1] === 'del') {
                            $gone = $p['images'][$k];
                            array_splice($p['images'], $k, 1);
                            $msg = 'Bild gelöscht.';
                        } else {
                            $j = $k + ($m[1] === 'up' ? -1 : 1);
                            if ($j >= 0 && $j < $n) {
                                [$p['images'][$k], $p['images'][$j]] = [$p['images'][$j], $p['images'][$k]];
                            }
                            $msg = 'Reihenfolge geändert.';
                        }
                    }
                } elseif ($op === 'before_del' || $op === 'after_del') {
                    $slot = substr($op, 0, -4);
                    $gone = $p[$slot];
                    $p[$slot] = null;
                    $msg = ($slot === 'before' ? 'Vorher' : 'Nachher') . '-Bild entfernt.';
                }
                if ($p['visible'] && !media_ok($p['before']) && !$p['images']) {
                    $msg .= ' Hinweis: Das Projekt hat noch keine Bilder und erscheint deshalb noch nicht auf der Webseite.';
                }
                unset($p);
                content_save($d);
                if (isset($gone)) {
                    image_delete($gone);
                }
                go('p=projekt&id=' . rawurlencode($id) . (isset($m[2]) ? '#bild-' . (int)$m[2] : ''), 'ok', $msg);

            case 'legal_save':
                $doc = (string)($_POST['doc'] ?? '');
                if (!isset($d['legal'][$doc])) {
                    go('p=recht', 'error', 'Unbekannter Text.');
                }
                $d['legal'][$doc] = sanitize_html((string)($_POST['html'] ?? ''));
                content_save($d);
                go('p=recht&doc=' . $doc, 'ok', ($doc === 'impressum' ? 'Impressum' : 'Datenschutzerklärung') . ' gespeichert.');

            case 'contact_save':
                foreach (['company' => 80, 'owner' => 80, 'street' => 120, 'zip' => 10, 'city' => 80, 'phone' => 40, 'email' => 120, 'greeting' => 80] as $k => $max) {
                    $d['contact'][$k] = str_in($k, $max);
                }
                if ($d['contact']['email'] !== '' && !filter_var($d['contact']['email'], FILTER_VALIDATE_EMAIL)) {
                    go('p=kontakt', 'error', 'Die E-Mail-Adresse ist ungültig – nichts gespeichert.');
                }
                if ($d['contact']['phone'] !== '' && strlen(phone_intl($d['contact']['phone'])) < 8) {
                    go('p=kontakt', 'error', 'Die Telefonnummer ist ungültig – nichts gespeichert.');
                }
                content_save($d);
                go('p=kontakt', 'ok', 'Kontaktdaten gespeichert. Denken Sie daran, bei Adressänderungen auch Impressum und Datenschutzerklärung anzupassen.');

            case 'backup_restore':
                $file = basename((string)($_POST['file'] ?? ''));
                $restored = preg_match('/^content-[\w\-]+\.json$/', $file) ? json_file_read(BACKUP_DIR . '/' . $file) : null;
                if ($restored === null) {
                    go('p=sicherungen', 'error', 'Die Sicherung konnte nicht gelesen werden.');
                }
                content_save(content_normalize($restored));
                go('p=sicherungen', 'ok', 'Sicherung wiederhergestellt. Der vorherige Stand wurde ebenfalls gesichert.');

            case 'password_change':
                $cur = (string)($_POST['current'] ?? '');
                $pw = (string)($_POST['pw'] ?? '');
                if (!password_verify($cur, admin_hash())) {
                    go('p=passwort', 'error', 'Das aktuelle Passwort ist nicht korrekt.');
                }
                if (mb_strlen($pw) < PASSWORD_MIN_LEN) {
                    go('p=passwort', 'error', 'Das neue Passwort muss mindestens ' . PASSWORD_MIN_LEN . ' Zeichen lang sein.');
                }
                if ($pw !== (string)($_POST['pw2'] ?? '')) {
                    go('p=passwort', 'error', 'Die beiden neuen Passwörter stimmen nicht überein.');
                }
                admin_set_password($pw);
                session_regenerate_id(true);
                go('p=passwort', 'ok', 'Passwort geändert.');
        }
    } catch (Throwable $ex) {
        if ($action === 'upload') {
            json_out(['ok' => false, 'error' => $ex->getMessage()], 400);
        }
        go('p=' . rawurlencode($page), 'error', $ex->getMessage());
    }
    go();
}

/* =====================================================================
 * Seiten (GET)
 * ===================================================================== */
$d = content_load();

switch ($page) {
    case 'projekte':
        view_start('Projekte', 'projekte');
        view_projects($d);
        break;
    case 'projekt':
        $p = null;
        foreach ($d['projects'] as $x) {
            if ($x['id'] === ($_GET['id'] ?? '')) {
                $p = $x;
            }
        }
        if (!$p) {
            go('p=projekte', 'error', 'Projekt nicht gefunden.');
        }
        view_start('Projekt bearbeiten', 'projekte');
        view_project($p);
        break;
    case 'recht':
        $doc = ($_GET['doc'] ?? '') === 'datenschutz' ? 'datenschutz' : 'impressum';
        view_start('Rechtstexte', 'recht');
        view_legal($d, $doc);
        break;
    case 'kontakt':
        view_start('Kontaktdaten', 'kontakt');
        view_contact($d['contact']);
        break;
    case 'sicherungen':
        view_start('Sicherungen', 'sicherungen');
        view_backups();
        break;
    case 'passwort':
        view_start('Passwort ändern', 'passwort');
        view_password();
        break;
    default:
        view_start('Übersicht', 'start');
        view_dashboard($d);
}
view_end();

/* =====================================================================
 * Ansichten
 * ===================================================================== */

function view_head(string $title): void
{
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> – Verwaltung Terra &amp; Garten</title>
<link rel="icon" href="../favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= e(asset('admin/admin.css', '../')) ?>">
</head>
<?php
}

function view_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        [$type, $msg] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="flash flash-' . e($type) . '" role="status">' . e($msg) . '</div>';
    }
}

function view_auth(string $title, string $mode, string $error): void
{
    view_head($title);
    ?>
<body class="auth">
<main class="auth-box">
  <img src="../assets/img/logo.webp" alt="Terra &amp; Garten" width="180" height="156">
  <h1><?= e($title) ?></h1>
  <?php view_flash(); ?>
  <?php if (!is_https() && !in_array(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''), ['localhost', '127.0.0.1'], true)): ?>
  <div class="flash flash-error">Achtung: Die Verbindung ist nicht verschlüsselt (kein https). Bitte das SSL-Zertifikat in IONOS aktivieren, bevor Sie ein Passwort eingeben.</div>
  <?php endif; ?>
  <?php if ($error): ?><div class="flash flash-error" role="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="./">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= e($mode) ?>">
    <?php if ($mode === 'setup'): ?>
      <p>Legen Sie jetzt das Passwort für den Verwaltungsbereich fest (mindestens <?= PASSWORD_MIN_LEN ?> Zeichen).</p>
      <label>Neues Passwort<input type="password" name="pw" required minlength="<?= PASSWORD_MIN_LEN ?>" autocomplete="new-password" autofocus></label>
      <label>Passwort wiederholen<input type="password" name="pw2" required minlength="<?= PASSWORD_MIN_LEN ?>" autocomplete="new-password"></label>
      <button class="btn">Passwort speichern</button>
    <?php else: ?>
      <label>Passwort<input type="password" name="pw" required autocomplete="current-password" autofocus></label>
      <button class="btn">Anmelden</button>
    <?php endif; ?>
  </form>
  <p class="muted"><a href="../">← Zur Webseite</a></p>
</main>
</body>
</html>
<?php
}

function view_start(string $title, string $active): void
{
    view_head($title);
    $nav = ['start' => 'Übersicht', 'projekte' => 'Projekte', 'recht' => 'Rechtstexte', 'kontakt' => 'Kontaktdaten', 'sicherungen' => 'Sicherungen', 'passwort' => 'Passwort'];
    ?>
<body data-csrf="<?= e(csrf_token()) ?>">
<header class="bar">
  <a class="brand" href="./"><b>TERRA &amp; GARTEN</b> <span>Verwaltung</span></a>
  <nav>
    <?php foreach ($nav as $k => $label): ?>
      <a href="./<?= $k === 'start' ? '' : '?p=' . $k ?>"<?= $k === $active ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="bar-right">
    <a href="../" target="_blank" rel="noopener">Webseite ansehen ↗</a>
    <form method="post" action="./"><?= csrf_field() ?><input type="hidden" name="action" value="logout"><button class="link">Abmelden</button></form>
  </div>
</header>
<main class="main">
  <h1><?= e($title) ?></h1>
  <?php view_flash(); ?>
<?php
}

function view_end(): void
{
    ?>
</main>
<script src="<?= e(asset('admin/admin.js', '../')) ?>"></script>
</body>
</html>
<?php
}

function thumb_url($img): string
{
    if (!media_ok($img)) {
        return '';
    }
    $t = !empty($img['thumb']) && is_file(ROOT . '/' . $img['thumb']) ? $img['thumb'] : $img['src'];
    return '../' . $t;
}

function view_dashboard(array $d): void
{
    $count = count($d['projects']);
    $vis = count(array_filter($d['projects'], function ($p) { return !empty($p['visible']); }));
    $imgs = array_sum(array_map(function ($p) { return count($p['images']); }, $d['projects']));
    ?>
  <p class="lead">Hier pflegen Sie die Inhalte Ihrer Webseite. Änderungen sind nach dem Speichern sofort online.</p>
  <div class="cards">
    <a class="card" href="?p=projekte"><b>Projekte &amp; Bilder</b><span><?= $count ?> Projekt<?= $count === 1 ? '' : 'e' ?> (<?= $vis ?> sichtbar), <?= $imgs ?> Galeriebilder</span></a>
    <a class="card" href="?p=recht&amp;doc=impressum"><b>Impressum</b><span>Pflichtangaben zum Unternehmen</span></a>
    <a class="card" href="?p=recht&amp;doc=datenschutz"><b>Datenschutzerklärung</b><span>Informationen zur Datenverarbeitung</span></a>
    <a class="card" href="?p=kontakt"><b>Kontaktdaten</b><span>Telefon, WhatsApp, E-Mail, Adresse</span></a>
    <a class="card" href="?p=sicherungen"><b>Sicherungen</b><span>Frühere Stände wiederherstellen</span></a>
    <a class="card" href="?p=passwort"><b>Passwort ändern</b><span>Zugang zum Verwaltungsbereich</span></a>
  </div>
<?php
}

function view_projects(array $d): void
{
    ?>
  <form class="panel inline-form" method="post" action="./">
    <?= csrf_field() ?><input type="hidden" name="action" value="project_create">
    <label class="grow">Neues Projekt<input name="title" maxlength="120" placeholder="z. B. Pflasterarbeiten in Meppen" required></label>
    <button class="btn">Projekt anlegen</button>
  </form>
  <?php if (!$d['projects']): ?>
    <p class="muted">Noch keine Projekte vorhanden.</p>
  <?php endif; ?>
  <ul class="plist">
  <?php foreach ($d['projects'] as $i => $p):
      $cover = thumb_url($p['after']) ?: thumb_url($p['images'][0] ?? null) ?: thumb_url($p['before']); ?>
    <li class="panel prow">
      <div class="pthumb"><?php if ($cover): ?><img src="<?= e($cover) ?>" alt=""><?php endif; ?></div>
      <div class="pinfo">
        <b><?= e($p['title']) ?></b>
        <span class="muted"><?= count($p['images']) ?> Galeriebilder<?= media_ok($p['before']) && media_ok($p['after']) ? ' · Vorher/Nachher' : '' ?></span>
        <span class="pill <?= !empty($p['visible']) ? 'on' : 'off' ?>"><?= !empty($p['visible']) ? 'sichtbar' : 'ausgeblendet' ?></span>
      </div>
      <div class="pactions">
        <a class="btn" href="?p=projekt&amp;id=<?= e(rawurlencode($p['id'])) ?>">Bearbeiten</a>
        <form method="post" action="./"><?= csrf_field() ?><input type="hidden" name="action" value="project_move"><input type="hidden" name="id" value="<?= e($p['id']) ?>">
          <button class="icon" name="dir" value="up" title="Nach oben" aria-label="Nach oben"<?= $i === 0 ? ' disabled' : '' ?>>↑</button><button class="icon" name="dir" value="down" title="Nach unten" aria-label="Nach unten"<?= $i === count($d['projects']) - 1 ? ' disabled' : '' ?>>↓</button>
        </form>
        <form method="post" action="./" data-confirm="Projekt „<?= e($p['title']) ?>“ mit allen Bildern endgültig löschen?"><?= csrf_field() ?><input type="hidden" name="action" value="project_delete"><input type="hidden" name="id" value="<?= e($p['id']) ?>"><button class="btn danger">Löschen</button></form>
      </div>
    </li>
  <?php endforeach; ?>
  </ul>
  <p class="muted">Die Reihenfolge hier entspricht der Reihenfolge auf der Webseite. Ausgeblendete Projekte und Projekte ohne Bilder werden nicht angezeigt.</p>
<?php
}

function view_slot(array $p, string $slot, string $label): void
{
    $img = $p[$slot];
    ?>
      <div class="slot">
        <b><?= e($label) ?></b>
        <?php if (media_ok($img)): ?>
          <img src="<?= e(thumb_url($img)) ?>" alt="">
          <label>Bildbeschreibung<input name="alt_<?= $slot ?>" value="<?= e($img['alt'] ?? '') ?>" maxlength="200"></label>
          <div class="row">
            <button type="button" class="btn" data-upload="<?= $slot ?>">Ersetzen</button>
            <button class="btn danger" name="op" value="<?= $slot ?>_del" data-confirm="<?= e($label) ?>-Bild entfernen?">Entfernen</button>
          </div>
        <?php else: ?>
          <div class="empty">Kein Bild</div>
          <button type="button" class="btn" data-upload="<?= $slot ?>">Bild hochladen</button>
        <?php endif; ?>
      </div>
<?php
}

function view_project(array $p): void
{
    $n = count($p['images']);
    ?>
  <p class="row"><a href="?p=projekte">← Alle Projekte</a><?php if (!empty($p['visible'])): ?><a href="../projekte/<?= e($p['slug']) ?>/" target="_blank" rel="noopener">Projektseite ansehen ↗</a><?php endif; ?></p>
  <form method="post" action="./" id="projectForm" data-id="<?= e($p['id']) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="project_save">
    <input type="hidden" name="id" value="<?= e($p['id']) ?>">
    <input type="hidden" name="uploaded" value="0">

    <section class="panel">
      <h2>Texte</h2>
      <label>Titel<input name="title" value="<?= e($p['title']) ?>" maxlength="120" required></label>
      <label>Kurzbeschreibung<textarea name="intro" rows="3" maxlength="600"><?= e($p['intro']) ?></textarea></label>
      <label class="check"><input type="checkbox" name="visible" value="1"<?= !empty($p['visible']) ? ' checked' : '' ?>> Auf der Webseite anzeigen</label>
      <button class="btn primary" name="op" value="save">Speichern</button>
    </section>

    <section class="panel">
      <h2>Vorher / Nachher <small class="muted">(optional – erscheint als Schieberegler)</small></h2>
      <div class="slots">
        <?php view_slot($p, 'before', 'Vorher'); ?>
        <?php view_slot($p, 'after', 'Nachher'); ?>
      </div>
      <p class="muted">Beide Bilder sollten aus derselben Perspektive aufgenommen sein. Der Regler erscheint nur, wenn beide Bilder vorhanden sind.</p>
    </section>

    <section class="panel">
      <h2>Galerie <small class="muted">(<?= $n ?> Bild<?= $n === 1 ? '' : 'er' ?>)</small></h2>
      <div class="drop" data-upload="gallery" tabindex="0" role="button">
        <b>Bilder hierher ziehen oder klicken zum Auswählen</b>
        <span>Mehrere Bilder gleichzeitig möglich · JPG, PNG, WebP · Fotos werden automatisch verkleinert, Standortdaten werden entfernt</span>
      </div>
      <div class="progress" id="progress" hidden><div class="pbar"><i></i></div><span></span></div>
      <ol class="gallery">
      <?php foreach ($p['images'] as $k => $img): ?>
        <li class="gitem" id="bild-<?= $k ?>">
          <span class="num"><?= $k + 1 ?></span>
          <?php if (media_ok($img)): ?><img src="<?= e(thumb_url($img)) ?>" alt="" loading="lazy"><?php else: ?><div class="empty">Datei fehlt</div><?php endif; ?>
          <label>Bildunterschrift<input name="caption[<?= $k ?>]" value="<?= e($img['caption'] ?? '') ?>" maxlength="200" placeholder="z. B. Aushub mit dem Minibagger"></label>
          <div class="row">
            <button class="icon" name="op" value="img_up:<?= $k ?>" title="Nach vorne" aria-label="Nach vorne"<?= $k === 0 ? ' disabled' : '' ?>>←</button>
            <button class="icon" name="op" value="img_down:<?= $k ?>" title="Nach hinten" aria-label="Nach hinten"<?= $k === $n - 1 ? ' disabled' : '' ?>>→</button>
            <button class="btn danger small" name="op" value="img_del:<?= $k ?>" data-confirm="Dieses Bild endgültig löschen?">Löschen</button>
          </div>
        </li>
      <?php endforeach; ?>
      </ol>
      <?php if ($n): ?><button class="btn primary" name="op" value="save">Bildunterschriften speichern</button><?php endif; ?>
    </section>
  </form>
  <input type="file" id="fileInput" accept="image/jpeg,image/png,image/webp" hidden>
<?php
}

function view_legal(array $d, string $doc): void
{
    $names = ['impressum' => 'Impressum', 'datenschutz' => 'Datenschutzerklärung'];
    ?>
  <div class="tabs">
    <?php foreach ($names as $k => $label): ?>
      <a href="?p=recht&amp;doc=<?= $k ?>"<?= $k === $doc ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
    <a class="right" href="../<?= $doc ?>/" target="_blank" rel="noopener">Ansehen ↗</a>
  </div>
  <form method="post" action="./" class="panel" id="legalForm">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="legal_save">
    <input type="hidden" name="doc" value="<?= e($doc) ?>">
    <div class="toolbar" role="toolbar" aria-label="Formatierung">
      <button type="button" data-cmd="formatBlock" data-val="h3" title="Überschrift">Überschrift</button>
      <button type="button" data-cmd="formatBlock" data-val="h4" title="Zwischenüberschrift">Zwischenüberschrift</button>
      <button type="button" data-cmd="formatBlock" data-val="p" title="Normaler Text">Text</button>
      <span class="sep"></span>
      <button type="button" data-cmd="bold" title="Fett"><b>F</b></button>
      <button type="button" data-cmd="italic" title="Kursiv"><i>K</i></button>
      <button type="button" data-cmd="insertUnorderedList" title="Aufzählung">• Liste</button>
      <button type="button" data-cmd="createLink" title="Link einfügen">Link</button>
      <button type="button" data-cmd="unlink" title="Link entfernen">Link entfernen</button>
      <button type="button" data-cmd="removeFormat" title="Formatierung entfernen">Format löschen</button>
      <span class="sep"></span>
      <button type="button" data-cmd="html" title="HTML-Quelltext bearbeiten">HTML</button>
    </div>
    <div class="editor ds" id="editor" contenteditable="true" spellcheck="true"><?= sanitize_html($d['legal'][$doc]) ?></div>
    <textarea name="html" id="html" class="html-src" rows="24" hidden><?= e($d['legal'][$doc]) ?></textarea>
    <div class="row">
      <button class="btn primary">Speichern</button>
      <span class="muted">Tipp: Texte aus einem Generator (z. B. eRecht24) können direkt hineinkopiert werden. Nicht erlaubte Formatierungen werden beim Speichern automatisch entfernt.</span>
    </div>
  </form>
<?php
}

function view_contact(array $c): void
{
    $f = function (string $k, string $label, string $type = 'text', string $hint = '') use ($c) {
        echo '<label>' . e($label) . '<input type="' . $type . '" name="' . $k . '" value="' . e($c[$k]) . '">' . ($hint ? '<small class="muted">' . e($hint) . '</small>' : '') . '</label>';
    };
    ?>
  <form method="post" action="./" class="panel">
    <?= csrf_field() ?><input type="hidden" name="action" value="contact_save">
    <div class="grid2">
      <?php $f('company', 'Firmenname'); $f('owner', 'Inhaber'); ?>
      <?php $f('street', 'Straße und Hausnummer'); ?>
      <div class="grid2 tight"><?php $f('zip', 'PLZ'); $f('city', 'Ort'); ?></div>
      <?php $f('phone', 'Telefon / WhatsApp', 'tel', 'Wird für Anruf-Buttons und WhatsApp verwendet, z. B. 01522 5700540'); ?>
      <?php $f('email', 'E-Mail', 'email'); ?>
      <?php $f('greeting', 'Anrede in vorbereiteten Nachrichten', 'text', 'z. B. „Hallo Herr Hübers,“'); ?>
    </div>
    <button class="btn primary">Speichern</button>
    <p class="muted">Diese Angaben werden im Kopfbereich, bei „Kontakt“ und in den WhatsApp-/E-Mail-Buttons verwendet. Impressum und Datenschutzerklärung werden separat unter „Rechtstexte“ gepflegt.</p>
  </form>
<?php
}

function view_backups(): void
{
    $list = backup_list();
    ?>
  <p class="lead">Bei jedem Speichern wird automatisch der vorherige Stand gesichert (die letzten <?= BACKUP_KEEP ?>). Texte, Reihenfolgen und Kontaktdaten lassen sich so zurückholen – bereits gelöschte Bilddateien allerdings nicht.</p>
  <?php if (!$list): ?><p class="muted">Noch keine Sicherungen vorhanden.</p><?php endif; ?>
  <ul class="blist">
  <?php foreach ($list as $b): ?>
    <li class="panel brow">
      <span><?= e(date('d.m.Y, H:i:s', $b['time'])) ?> Uhr <small class="muted">(<?= number_format($b['size'] / 1024, 1, ',', '.') ?> KB)</small></span>
      <form method="post" action="./" data-confirm="Diesen Stand wiederherstellen? Der aktuelle Stand wird vorher gesichert."><?= csrf_field() ?><input type="hidden" name="action" value="backup_restore"><input type="hidden" name="file" value="<?= e($b['file']) ?>"><button class="btn">Wiederherstellen</button></form>
    </li>
  <?php endforeach; ?>
  </ul>
<?php
}

function view_password(): void
{
    ?>
  <form method="post" action="./" class="panel narrow">
    <?= csrf_field() ?><input type="hidden" name="action" value="password_change">
    <label>Aktuelles Passwort<input type="password" name="current" required autocomplete="current-password"></label>
    <label>Neues Passwort <small class="muted">(mind. <?= PASSWORD_MIN_LEN ?> Zeichen)</small><input type="password" name="pw" required minlength="<?= PASSWORD_MIN_LEN ?>" autocomplete="new-password"></label>
    <label>Neues Passwort wiederholen<input type="password" name="pw2" required minlength="<?= PASSWORD_MIN_LEN ?>" autocomplete="new-password"></label>
    <button class="btn primary">Passwort ändern</button>
    <p class="muted">Passwort vergessen? Per FTP/SFTP die Datei <code>data/admin.php</code> löschen und danach <code>/admin/</code> aufrufen – dann kann ein neues Passwort festgelegt werden.</p>
  </form>
<?php
}
