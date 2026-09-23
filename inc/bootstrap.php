<?php
/**
 * Gemeinsame Grundfunktionen für Webseite und Verwaltungsbereich.
 * Alle Inhalte liegen in data/content.json – keine Datenbank nötig.
 */
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('DATA_DIR', ROOT . '/data');
define('UPLOAD_DIR', ROOT . '/uploads');
define('CONTENT_FILE', DATA_DIR . '/content.json');
define('BACKUP_DIR', DATA_DIR . '/backups');
define('BACKUP_KEEP', 30);

mb_internal_encoding('UTF-8');

/** HTML-sicher ausgeben */
function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Inhalte laden; bei beschädigter Datei wird die neueste Sicherung genutzt. */
function content_load(): array
{
    $data = json_file_read(CONTENT_FILE);
    if ($data === null) {
        foreach (backup_list() as $b) {
            $data = json_file_read(BACKUP_DIR . '/' . $b['file']);
            if ($data !== null) {
                break;
            }
        }
    }
    return content_normalize($data ?? []);
}

function json_file_read(string $file): ?array
{
    if (!is_file($file)) {
        return null;
    }
    $raw = file_get_contents($file);
    if ($raw === false) {
        return null;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
}

/** Fehlende Felder mit Standardwerten auffüllen, damit Templates nie ins Leere greifen. */
function content_normalize(array $d): array
{
    $d['contact'] = array_merge([
        'company'  => 'Terra & Garten',
        'owner'    => '',
        'street'   => '',
        'zip'      => '',
        'city'     => '',
        'phone'    => '',
        'email'    => '',
        'greeting' => 'Hallo,',
    ], is_array($d['contact'] ?? null) ? $d['contact'] : []);
    $d['projects'] = array_values(array_filter(is_array($d['projects'] ?? null) ? $d['projects'] : [], 'is_array'));
    foreach ($d['projects'] as &$p) {
        $p = array_merge(['id' => '', 'title' => '', 'intro' => '', 'visible' => true, 'before' => null, 'after' => null, 'images' => []], $p);
        $p['images'] = array_values(array_filter(is_array($p['images']) ? $p['images'] : [], 'is_array'));
    }
    unset($p);
    $d['legal'] = array_merge(['impressum' => '', 'datenschutz' => ''], is_array($d['legal'] ?? null) ? $d['legal'] : []);
    return $d;
}

/**
 * Inhalte speichern: vorher Sicherung anlegen, dann atomar ersetzen
 * (erst temporäre Datei schreiben, dann umbenennen).
 */
function content_save(array $data): void
{
    $lock = fopen(DATA_DIR . '/.lock', 'c');
    if ($lock === false || !flock($lock, LOCK_EX)) {
        throw new RuntimeException('Inhalte konnten nicht gesperrt werden.');
    }
    try {
        if (!is_dir(BACKUP_DIR)) {
            mkdir(BACKUP_DIR, 0755, true);
        }
        if (is_file(CONTENT_FILE)) {
            copy(CONTENT_FILE, BACKUP_DIR . '/content-' . date('Y-m-d_H-i-s') . '-' . bin2hex(random_bytes(2)) . '.json');
            $old = backup_list();
            foreach (array_slice($old, BACKUP_KEEP) as $b) {
                @unlink(BACKUP_DIR . '/' . $b['file']);
            }
        }
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('Inhalte konnten nicht umgewandelt werden.');
        }
        $tmp = CONTENT_FILE . '.tmp';
        if (file_put_contents($tmp, $json) === false || !rename($tmp, CONTENT_FILE)) {
            throw new RuntimeException('Inhalte konnten nicht gespeichert werden (Schreibrechte für den Ordner „data“ prüfen).');
        }
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

/** Sicherungen, neueste zuerst */
function backup_list(): array
{
    $out = [];
    foreach (glob(BACKUP_DIR . '/content-*.json') ?: [] as $f) {
        $out[] = ['file' => basename($f), 'time' => filemtime($f), 'size' => filesize($f)];
    }
    usort($out, function ($a, $b) {
        return strcmp($b['file'], $a['file']);
    });
    return $out;
}

/** "01522 5700540" → "4915225700540" (für tel: und wa.me) */
function phone_intl(string $phone): string
{
    $d = preg_replace('/\D+/', '', $phone);
    if (strpos(ltrim($phone), '+') === 0) {
        return $d;
    }
    if (strpos($d, '00') === 0) {
        return substr($d, 2);
    }
    if (strpos($d, '0') === 0) {
        return '49' . substr($d, 1);
    }
    return $d;
}

function wa_link(array $c, string $text = ''): string
{
    $url = 'https://wa.me/' . phone_intl($c['phone']);
    return $text === '' ? $url : $url . '?text=' . rawurlencode($text);
}

/** Pfad eines hochgeladenen Bildes nur liefern, wenn die Datei wirklich existiert. */
function media_ok($img): bool
{
    return is_array($img) && !empty($img['src']) && is_file(ROOT . '/' . $img['src']);
}

/** Cache-Busting für CSS/JS: Datei-Änderungszeit anhängen */
function asset(string $path, string $base = ''): string
{
    $t = @filemtime(ROOT . '/' . $path);
    return $base . $path . ($t ? '?v=' . $t : '');
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
}

function site_origin(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (!preg_match('/^[a-z0-9.\-]+(:\d+)?$/i', $host)) {
        $host = 'localhost';
    }
    return (is_https() ? 'https://' : 'http://') . $host;
}

require __DIR__ . '/sanitize.php';
