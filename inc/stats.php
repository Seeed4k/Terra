<?php
/**
 * Besucherstatistik ohne Cookies und ohne Drittanbieter.
 *
 * - Seitenaufrufe und Klicks (Anruf, WhatsApp, E-Mail) werden nur als Zähler gespeichert.
 * - „Besucher“ werden über einen Tages-Fingerabdruck (Hash aus IP + Browser + täglich
 *   wechselndem Zufallswert) gezählt. Die IP-Adresse selbst wird nie gespeichert, der
 *   Fingerabdruck wird nach dem Tag gelöscht.
 * - Am Monatsanfang geht automatisch ein Kurzbericht per E-Mail raus (beim ersten
 *   Seitenaufruf des neuen Monats – ein Cronjob ist nicht nötig).
 */
declare(strict_types=1);

define('STATS_DIR', DATA_DIR . '/stats');
const STATS_EVENTS = ['call' => 'Anrufen', 'wa' => 'WhatsApp', 'mail' => 'E-Mail', 'form_wa' => 'Anfrage per WhatsApp', 'form_mail' => 'Anfrage per E-Mail'];
const MONTHS_DE = [1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];

function stats_config(): array
{
    $c = json_file_read(STATS_DIR . '/config.json') ?? [];
    return array_merge(['enabled' => true, 'report' => true, 'email' => '', 'last_report' => ''], $c);
}

function stats_config_save(array $c): void
{
    stats_ensure_dir();
    file_put_contents(STATS_DIR . '/config.json', json_encode($c, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function stats_ensure_dir(): void
{
    if (!is_dir(STATS_DIR)) {
        @mkdir(STATS_DIR, 0755, true);
    }
}

/** Suchmaschinen, Vorschau-Bots und Link-Vorschauen (WhatsApp etc.) nicht mitzählen */
function stats_is_bot(): bool
{
    $ua = strtolower((string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
    if ($ua === '' || preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp|telegram|curl|wget|python|headless|lighthouse|monitor/', $ua)) {
        return true;
    }
    $purpose = strtolower(($_SERVER['HTTP_SEC_PURPOSE'] ?? '') . ($_SERVER['HTTP_PURPOSE'] ?? ''));
    return strpos($purpose, 'prefetch') !== false;
}

/** Eigene Besuche des Betreibers (nach Admin-Anmeldung markiert) nicht zählen */
function stats_is_owner(): bool
{
    return !empty($_COOKIE['tg_nostat']);
}

/** Daten eines Monats sperren, ändern, speichern */
function stats_update(string $ym, callable $fn): void
{
    stats_ensure_dir();
    $file = STATS_DIR . '/' . $ym . '.json';
    $h = @fopen($file, 'c+');
    if (!$h || !flock($h, LOCK_EX)) {
        return;
    }
    $raw = stream_get_contents($h);
    $data = json_decode($raw ?: '[]', true);
    $data = is_array($data) ? $data : [];
    $data = $fn($data);
    ftruncate($h, 0);
    rewind($h);
    fwrite($h, json_encode($data, JSON_UNESCAPED_UNICODE));
    fflush($h);
    flock($h, LOCK_UN);
    fclose($h);
}

function stats_referrer_source(): string
{
    $ref = (string)($_SERVER['HTTP_REFERER'] ?? '');
    $host = strtolower((string)parse_url($ref, PHP_URL_HOST));
    $own = strtolower(preg_replace('/^www\./', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
    if ($host === '' ) {
        return 'Direkt / QR-Code / Lesezeichen';
    }
    if ($own !== '' && preg_replace('/^www\./', '', $host) === $own) {
        return '';
    }
    foreach (['google' => 'Google', 'bing' => 'Bing', 'duckduckgo' => 'DuckDuckGo', 'ecosia' => 'Ecosia', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'whatsapp' => 'WhatsApp'] as $k => $label) {
        if (strpos($host, $k) !== false) {
            return $label;
        }
    }
    return 'Andere Webseiten';
}

/** Tages-Besucherkennung: kein Cookie, keine gespeicherte IP */
function stats_visitor_is_new(string $day): bool
{
    stats_ensure_dir();
    $saltFile = STATS_DIR . '/salt-' . $day . '.txt';
    if (!is_file($saltFile)) {
        foreach (glob(STATS_DIR . '/salt-*.txt') ?: [] as $old) {
            @unlink($old);
        }
        foreach (glob(STATS_DIR . '/seen-*.txt') ?: [] as $old) {
            @unlink($old);
        }
        @file_put_contents($saltFile, bin2hex(random_bytes(16)));
    }
    $salt = (string)@file_get_contents($saltFile);
    $id = substr(hash('sha256', $salt . '|' . ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? '')), 0, 16);
    $seenFile = STATS_DIR . '/seen-' . $day . '.txt';
    $seen = (string)@file_get_contents($seenFile);
    if (strpos($seen, $id) !== false) {
        return false;
    }
    @file_put_contents($seenFile, $id . "\n", FILE_APPEND | LOCK_EX);
    return true;
}

/** Seitenaufruf zählen (wird von jeder öffentlichen Seite aufgerufen) */
function stats_hit(string $path): void
{
    try {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET' || stats_is_bot() || stats_is_owner()) {
            return;
        }
        $cfg = stats_config();
        if (empty($cfg['enabled'])) {
            return;
        }
        $day = date('Y-m-d');
        $new = stats_visitor_is_new($day);
        $src = $new ? stats_referrer_source() : '';
        $path = substr($path, 0, 80);
        stats_update(date('Y-m'), function ($d) use ($day, $new, $src, $path) {
            $d['days'][$day]['pv'] = ($d['days'][$day]['pv'] ?? 0) + 1;
            if ($new) {
                $d['days'][$day]['uv'] = ($d['days'][$day]['uv'] ?? 0) + 1;
            }
            $d['pages'][$path] = ($d['pages'][$path] ?? 0) + 1;
            if ($src !== '') {
                $d['sources'][$src] = ($d['sources'][$src] ?? 0) + 1;
            }
            return $d;
        });
        stats_maybe_send_report($cfg);
    } catch (Throwable $e) {
        // Statistik darf die Webseite nie stören
    }
}

/** Klick zählen (Anruf, WhatsApp, …) – aufgerufen von stats.php */
function stats_event(string $name): void
{
    if (!isset(STATS_EVENTS[$name]) || stats_is_bot() || stats_is_owner() || empty(stats_config()['enabled'])) {
        return;
    }
    $day = date('Y-m-d');
    stats_update(date('Y-m'), function ($d) use ($day, $name) {
        $d['days'][$day]['ev'][$name] = ($d['days'][$day]['ev'][$name] ?? 0) + 1;
        return $d;
    });
}

/** Zusammenfassung eines Monats */
function stats_summary(string $ym): array
{
    $d = json_file_read(STATS_DIR . '/' . $ym . '.json') ?? [];
    $s = ['ym' => $ym, 'uv' => 0, 'pv' => 0, 'ev' => array_fill_keys(array_keys(STATS_EVENTS), 0), 'days' => [], 'pages' => $d['pages'] ?? [], 'sources' => $d['sources'] ?? []];
    foreach ($d['days'] ?? [] as $day => $v) {
        $s['uv'] += (int)($v['uv'] ?? 0);
        $s['pv'] += (int)($v['pv'] ?? 0);
        foreach ($v['ev'] ?? [] as $k => $n) {
            if (isset($s['ev'][$k])) {
                $s['ev'][$k] += (int)$n;
            }
        }
        $s['days'][$day] = (int)($v['uv'] ?? 0);
    }
    arsort($s['pages']);
    arsort($s['sources']);
    $s['contacts'] = array_sum($s['ev']);
    return $s;
}

function stats_month_label(string $ym): string
{
    [$y, $m] = array_map('intval', explode('-', $ym));
    return MONTHS_DE[$m] . ' ' . $y;
}

function stats_prev_month(string $ym): string
{
    return date('Y-m', strtotime($ym . '-01 -1 month'));
}

function stats_page_label(string $path): string
{
    static $names = null;
    if ($names === null) {
        $names = ['/' => 'Startseite', '/projekte/' => 'Projekte (Übersicht)', '/impressum/' => 'Impressum', '/datenschutz/' => 'Datenschutz'];
        if (function_exists('services')) {
            foreach (services() as $k => $s) {
                $names['/' . $k . '/'] = $s['nav'];
            }
        }
    }
    if (isset($names[$path])) {
        return $names[$path];
    }
    if (preg_match('#^/projekte/([a-z0-9-]+)/$#', $path, $m)) {
        foreach (content_load()['projects'] as $p) {
            if ($p['slug'] === $m[1]) {
                return 'Projekt: ' . $p['title'];
            }
        }
        return 'Projekt: ' . ucfirst(str_replace('-', ' ', $m[1]));
    }
    return $path;
}

/** Monatsbericht als HTML-Mail */
function stats_report_html(string $ym): string
{
    $s = stats_summary($ym);
    $p = stats_summary(stats_prev_month($ym));
    $trend = function (int $now, int $before): string {
        if ($before === 0) {
            return '';
        }
        $pct = (int)round(($now - $before) / $before * 100);
        return ' <span style="color:' . ($pct >= 0 ? '#3f6b3a' : '#a23b2a') . ';font-size:13px">(' . ($pct >= 0 ? '+' : '') . $pct . ' % zum Vormonat)</span>';
    };
    $row = function (string $label, string $value) {
        return '<tr><td style="padding:6px 0;color:#5c564b">' . $label . '</td><td style="padding:6px 0;text-align:right;font-weight:600">' . $value . '</td></tr>';
    };
    $h = '<div style="font-family:Arial,sans-serif;max-width:520px;color:#1d1a15">';
    $h .= '<h2 style="margin:0 0 4px">Ihre Webseite im ' . e(stats_month_label($ym)) . '</h2>';
    $h .= '<p style="margin:0 0 18px;color:#5c564b">Monatsbericht für terra-garten-huebers.de</p>';
    $h .= '<p style="font-size:26px;margin:0"><b>' . $s['uv'] . '</b> Besucher' . $trend($s['uv'], $p['uv']) . '</p>';
    $h .= '<p style="font-size:20px;margin:6px 0 18px"><b>' . $s['contacts'] . '</b> Kontakt-Klicks' . $trend($s['contacts'], $p['contacts']) . '</p>';
    $h .= '<table style="width:100%;border-collapse:collapse;border-top:1px solid #ddd6c8">';
    foreach (STATS_EVENTS as $k => $label) {
        $h .= $row(e($label), (string)$s['ev'][$k]);
    }
    $h .= $row('Seitenaufrufe', (string)$s['pv']) . '</table>';
    if ($s['sources']) {
        $h .= '<h3 style="margin:22px 0 6px;font-size:15px">So wurden Sie gefunden</h3><table style="width:100%;border-collapse:collapse">';
        foreach (array_slice($s['sources'], 0, 5, true) as $k => $n) {
            $h .= $row(e($k), (string)$n);
        }
        $h .= '</table>';
    }
    if ($s['pages']) {
        $h .= '<h3 style="margin:22px 0 6px;font-size:15px">Beliebteste Seiten</h3><table style="width:100%;border-collapse:collapse">';
        foreach (array_slice($s['pages'], 0, 5, true) as $k => $n) {
            $h .= $row(e(stats_page_label((string)$k)), (string)$n);
        }
        $h .= '</table>';
    }
    $h .= '<p style="margin:24px 0 0;font-size:12px;color:#8d877b">Gezählt ohne Cookies und ohne Google Analytics. Ihre eigenen Besuche nach der Anmeldung im Verwaltungsbereich sind nicht enthalten. Einstellungen: Verwaltung → Statistik.</p></div>';
    return $h;
}

function stats_send_report(string $ym, string $to): bool
{
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $d = content_load();
    $from = filter_var($d['contact']['email'], FILTER_VALIDATE_EMAIL) ? $d['contact']['email'] : 'noreply@' . preg_replace('/^www\./', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $subject = '=?UTF-8?B?' . base64_encode('Webseiten-Bericht ' . stats_month_label($ym)) . '?=';
    $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nFrom: Webseite Terra & Garten <" . $from . ">\r\n";
    return @mail($to, $subject, stats_report_html($ym), $headers, '-f' . $from);
}

/** Am Monatsanfang einmal den Bericht für den Vormonat verschicken */
function stats_maybe_send_report(array $cfg): void
{
    $prev = stats_prev_month(date('Y-m'));
    if (empty($cfg['report']) || $cfg['email'] === '' || $cfg['last_report'] >= $prev) {
        return;
    }
    $lock = @fopen(STATS_DIR . '/.report-lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
        return;
    }
    $cfg = stats_config(); // erneut lesen – ein paralleler Aufruf könnte schneller gewesen sein
    if ($cfg['last_report'] < $prev) {
        $cfg['last_report'] = $prev;
        stats_config_save($cfg);
        stats_send_report($prev, $cfg['email']);
    }
    flock($lock, LOCK_UN);
    fclose($lock);
}
