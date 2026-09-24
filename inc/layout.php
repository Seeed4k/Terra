<?php
/**
 * Gemeinsame Seitenteile (Kopf, Navigation, Fußzeile) für Startseite und Rechtstexte.
 * $base ist der relative Weg zum Hauptordner: '' auf der Startseite, '../' in Unterordnern.
 */
declare(strict_types=1);

const ICON_WA = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.3c0-.1-.2-.2-.5-.3z"/></svg>';
const ICON_PHONE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>';
const EDGE_SVG = '<svg class="edge" viewBox="0 0 1200 34" preserveAspectRatio="none" aria-hidden="true"><path fill="currentColor" d="M0 34V20l40-6 30 8 55-12 48 9 36-7 60 10 44-14 52 12 38-5 70 9 40-11 56 8 30-6 64 10 42-13 58 11 34-4 66 7 44-12 50 10 38-6 60 9 46-10 40 7 30-5 V34z"/></svg>';

/** Standard-Text für WhatsApp-Links */
function wa_intro(array $c): string
{
    return trim($c['greeting'] . ' ich interessiere mich für ein Projekt.');
}

function render_head(array $d, string $title, string $desc, string $base, string $path = '/', array $schema = [], bool $index = true, string $image = ''): void
{
    $c = $d['contact'];
    $origin = canonical_origin();
    $image = $image !== '' ? $origin . '/' . ltrim($image, '/') : $origin . '/assets/img/logo.webp';
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<link rel="canonical" href="<?= e($origin . $path) ?>">
<meta name="theme-color" content="#1d1a15">
<meta property="og:type" content="website">
<meta property="og:locale" content="de_DE">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:url" content="<?= e($origin . $path) ?>">
<meta property="og:image" content="<?= e($image) ?>">
<?php if (!$index): ?><meta name="robots" content="noindex, follow">
<?php endif; ?>
<link rel="icon" href="<?= e($base) ?>favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="<?= e($base) ?>assets/img/apple-touch-icon.png">
<link rel="preload" href="<?= e($base) ?>assets/fonts/big-shoulders-display-latin-800-normal.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= e($base) ?>assets/fonts/barlow-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('assets/css/site.css', $base)) ?>">
<?php foreach ($schema as $sc): ?>
<script type="application/ld+json"><?= json_encode($sc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php endforeach; ?>
<?php if ($path === '/'): ?>
<link rel="preload" href="<?= e($base) ?>assets/img/logo.webp" as="image" type="image/webp" fetchpriority="high">
<script type="application/ld+json"><?= json_encode([
        '@context'  => 'https://schema.org',
        '@type'     => 'HomeAndConstructionBusiness',
        'name'      => $c['company'] . ($c['owner'] !== '' ? ' – ' . $c['owner'] : ''),
        'url'       => $origin . '/',
        'image'     => $origin . '/assets/img/logo.webp',
        'logo'      => $origin . '/assets/img/logo.webp',
        'geo'       => ['@type' => 'GeoCoordinates', 'latitude' => 52.79, 'longitude' => 7.24],
        'telephone' => $c['phone'] !== '' ? '+' . phone_intl($c['phone']) : null,
        'email'     => $c['email'] ?: null,
        'address'   => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $c['street'],
            'postalCode'      => $c['zip'],
            'addressLocality' => $c['city'],
            'addressCountry'  => 'DE',
        ],
        'areaServed' => ['Haren (Ems)', 'Meppen', 'Lathen', 'Twist', 'Dörpen', 'Sögel', 'Papenburg', 'Lingen', 'Emsland'],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php endif; ?>
</head>
<body>
<?php
}

function render_header(array $d, string $base, bool $hasProjects, bool $solid = false): void
{
    $c = $d['contact'];
    ?>
<header class="top<?= $solid ? ' solid solid-always' : '' ?>" id="top">
  <div class="wrap">
    <a class="brand" href="<?= e($base) ?>#start" aria-label="Terra &amp; Garten Hübers, zum Seitenanfang"><b>TERRA &amp; GARTEN</b><i>Hübers</i></a>
    <button class="burger" id="burger" aria-label="Menü öffnen" aria-expanded="false" aria-controls="nav">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path id="bIcon" d="M4 7h16M4 12h16M4 17h10"/></svg>
    </button>
    <nav class="nav" id="nav">
      <a href="<?= e($base) ?>#leistungen">Leistungen</a>
<?php if ($hasProjects): ?>
      <a href="<?= e($base) ?>#projekte">Projekte</a>
<?php endif; ?>
      <a href="<?= e($base) ?>#ablauf">Ablauf</a>
      <a href="<?= e($base) ?>#ueber">Über mich</a>
      <a href="<?= e($base) ?>#gebiet">Einsatzgebiet</a>
      <a href="<?= e($base) ?>#kontakt">Kontakt</a>
<?php if ($c['phone'] !== ''): ?>
      <a class="call" href="tel:+<?= e(phone_intl($c['phone'])) ?>"><?= e($c['phone']) ?></a>
<?php endif; ?>
    </nav>
  </div>
</header>
<?php
}

function render_footer(array $d, string $base): void
{
    $c = $d['contact'];
    ?>
<?php if ($c['phone'] !== ''): ?>
<a class="wa-float" href="<?= e(wa_link($c, wa_intro($c))) ?>" target="_blank" rel="noopener" aria-label="Per WhatsApp schreiben"><?= ICON_WA ?></a>
<?php endif; ?>

<footer>
  <?= EDGE_SVG ?>
  <div class="wrap">
    <div class="foot">
      <a href="<?= e($base) ?>#start" aria-label="Zum Seitenanfang"><img class="foot-logo" src="<?= e($base) ?>assets/img/logo.webp" alt="Terra &amp; Garten Hübers" width="170" height="148" loading="lazy"></a>
      <nav><a href="<?= e($base) ?>#leistungen">Leistungen</a><a href="<?= e($base) ?>projekte/">Projekte</a><a href="<?= e($base) ?>#kontakt">Kontakt</a><a href="<?= e($base) ?>impressum/">Impressum</a><a href="<?= e($base) ?>datenschutz/">Datenschutz</a></nav>
    </div>
    <nav class="foot-svc" aria-label="Leistungen"><?php foreach (services() as $k => $sv): ?><a href="<?= e($base . $k) ?>/"><?= e($sv['nav']) ?> in Haren &amp; Emsland</a><?php endforeach; ?></nav>
    <p style="margin-top:1.5rem;font-size:.85rem">© <?= date('Y') ?> <?= e($c['company']) ?><?= $c['owner'] !== '' ? ' ' . e(preg_replace('/^.*\s/u', '', $c['owner'])) : '' ?></p>
  </div>
</footer>
<script src="<?= e(asset('assets/js/site.js', $base)) ?>" defer></script>
</body>
</html>
<?php
}

/** Seite für Impressum bzw. Datenschutz */
function render_legal_page(string $key, string $heading): void
{
    $d = content_load();
    $hasProjects = (bool)visible_projects($d);
    render_head($d, $heading . ' – Terra & Garten Hübers', $heading . ' von Terra & Garten Hübers, Haren (Ems).', '../', '/' . $key . '/', [], false);
    render_header($d, '../', $hasProjects, true);
    ?>
<main class="legal-page">
  <div class="wrap">
    <h1><?= e($heading) ?></h1>
    <div class="ds"><?= sanitize_html($d['legal'][$key]) ?></div>
    <a class="back" href="../">← Zurück zur Startseite</a>
  </div>
</main>
<?php
    render_footer($d, '../');
}

/** Nur sichtbare Projekte mit mindestens einem vorhandenen Bild */
function visible_projects(array $d): array
{
    $out = [];
    foreach ($d['projects'] as $p) {
        if (empty($p['visible'])) {
            continue;
        }
        $p['images'] = array_values(array_filter($p['images'], 'media_ok'));
        $p['has_ba'] = media_ok($p['before']) && media_ok($p['after']);
        if ($p['has_ba'] || $p['images']) {
            $out[] = $p;
        }
    }
    return $out;
}

require __DIR__ . '/services.php';
