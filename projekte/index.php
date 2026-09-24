<?php
/**
 * Projektseiten: /projekte/ (Übersicht) und /projekte/<name>/ (einzelnes Projekt).
 * Die schöne Adresse wird per .htaccess auf diese Datei umgeleitet.
 */
declare(strict_types=1);
require dirname(__DIR__) . '/inc/bootstrap.php';
require dirname(__DIR__) . '/inc/layout.php';

$d = content_load();
$c = $d['contact'];
$projects = visible_projects($d);
$slug = preg_replace('/[^a-z0-9-]/', '', (string)($_GET['p'] ?? ''));
$project = null;
foreach ($projects as $p) {
    if ($p['slug'] === $slug) {
        $project = $p;
    }
}
if ($slug !== '' && !$project) {
    http_response_code(404);
}
$origin = canonical_origin();

if (!$project) {
    // ---------- Übersicht ----------
    $base = $slug !== '' ? '../../' : '../';
    render_head($d, 'Projekte: Garten- & Erdbau im Emsland | Terra & Garten Hübers', 'Referenzen von Terra & Garten Hübers aus Haren (Ems): Gartenumgestaltung, Erdbau, Rasen und Zaunbau im Emsland – mit Vorher-/Nachher-Bildern.', $base, '/projekte/', [[
        '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => $origin . '/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Projekte', 'item' => $origin . '/projekte/'],
        ]]], $slug === '');
    render_header($d, $base, (bool)$projects, true);
    ?>
<main class="subpage">
  <section class="page-hero">
    <div class="wrap">
      <nav class="crumbs" aria-label="Brotkrümelnavigation"><a href="<?= e($base) ?>">Startseite</a> › <span>Projekte</span></nav>
      <h1><?= $slug !== '' ? 'Projekt nicht gefunden' : 'Unsere Projekte im Emsland' ?></h1>
      <p class="lede"><?= $slug !== '' ? 'Dieses Projekt gibt es nicht (mehr). Hier finden Sie alle aktuellen Projekte.' : 'Ein Auszug aus unserer Arbeit in Haren und Umgebung – vom Aushub bis zum fertigen Garten.' ?></p>
      <?php render_cta($c, $base); ?>
    </div>
  </section>
  <section class="page-projects">
    <div class="wrap">
      <?php if ($projects): render_project_cards($projects, $base); else: ?><p>Bald finden Sie hier unsere Projekte.</p><?php endif; ?>
    </div>
  </section>
</main>
<?php
    render_footer($d, $base);
    exit;
}

// ---------- Einzelnes Projekt ----------
$p = $project;
$base = '../../';
$desc = $p['intro'] !== '' ? $p['intro'] : $p['title'] . ' – ein Projekt von Terra & Garten Hübers aus Haren (Ems). Bilder und Ablauf.';
$cover = $p['has_ba'] ? $p['after']['src'] : ($p['images'][0]['src'] ?? '');
render_head($d, $p['title'] . ' | Projekt von Terra & Garten Hübers', mb_substr($desc, 0, 160), $base, '/projekte/' . $p['slug'] . '/', [[
    '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => $origin . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Projekte', 'item' => $origin . '/projekte/'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $p['title'], 'item' => $origin . '/projekte/' . $p['slug'] . '/'],
    ]]], true, $cover);
render_header($d, $base, true, true);
$others = array_values(array_filter($projects, function ($o) use ($p) { return $o['slug'] !== $p['slug']; }));
?>
<main class="subpage">
  <section class="page-hero">
    <div class="wrap">
      <nav class="crumbs" aria-label="Brotkrümelnavigation"><a href="<?= e($base) ?>">Startseite</a> › <a href="../">Projekte</a> › <span><?= e($p['title']) ?></span></nav>
      <h1><?= e($p['title']) ?></h1>
      <?php if ($p['intro'] !== ''): ?><p class="lede"><?= e($p['intro']) ?></p><?php endif; ?>
    </div>
  </section>
  <section class="proj page-body">
    <div class="proj-item">
      <?php if ($p['has_ba']): $b = $p['before']; $a = $p['after']; ?>
      <div class="wrap">
        <div class="ba" style="--ar:<?= (int)$b['w'] ?>/<?= (int)$b['h'] ?>">
          <img src="<?= e($base . $b['src']) ?>" alt="<?= e($b['alt'] ?? 'Vorher') ?>" width="<?= (int)$b['w'] ?>" height="<?= (int)$b['h'] ?>">
          <img class="after" src="<?= e($base . $a['src']) ?>" alt="<?= e($a['alt'] ?? 'Nachher') ?>" width="<?= (int)$a['w'] ?>" height="<?= (int)$a['h'] ?>">
          <span class="tag l">Vorher</span><span class="tag r">Nachher</span>
          <input type="range" min="0" max="100" value="50" aria-label="Vorher-Nachher-Vergleich">
          <div class="handle"><div class="knob"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 6l-6 6 6 6M15 6l6 6-6 6"/></svg></div></div>
        </div>
      </div>
      <?php endif; ?>
      <?php if ($p['images']): ?>
      <div class="wrap"><h2 class="gal-h">So ist das Projekt entstanden</h2></div>
      <div class="wrap strip grid">
        <?php foreach ($p['images'] as $i => $img): ?>
        <button type="button" class="shot" data-full="<?= e($base . $img['src']) ?>"><div class="ph"><img src="<?= e($base . ($img['thumb'] ?? $img['src'])) ?>" alt="<?= e($img['caption'] ?: $p['title'] . ' – Bild ' . ($i + 1)) ?>" loading="lazy" decoding="async"></div><div class="cap"><b><?= $i + 1 ?></b><span><?= e($img['caption'] ?? '') ?></span></div></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="wrap proj-cta">
      <h2>Sie planen etwas Ähnliches?</h2>
      <p>Rufen Sie an oder schreiben Sie per WhatsApp – gern mit ein paar Fotos von Ihrem Grundstück.</p>
      <?php render_cta($c, $base); ?>
    </div>
  </section>
  <div class="lb" id="lb" role="dialog" aria-modal="true" aria-label="Bildansicht"><img alt=""><p></p>
    <button class="x" type="button" aria-label="Schließen"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
    <button class="pv" type="button" aria-label="Vorheriges Bild"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button><button class="nx" type="button" aria-label="Nächstes Bild"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
  </div>
  <?php if ($others): ?>
  <section class="page-projects">
    <div class="wrap"><h2>Weitere Projekte</h2><?php render_project_cards($others, $base); ?></div>
  </section>
  <?php endif; ?>
  <section class="page-more">
    <div class="wrap">
      <h2>Unsere Leistungen</h2>
      <ul class="more-links"><?php foreach (services() as $k => $o): ?><li><a href="<?= e($base . $k) ?>/"><?= e($o['nav']) ?> →</a></li><?php endforeach; ?></ul>
    </div>
  </section>
</main>
<?php
render_footer($d, $base);
