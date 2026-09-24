<?php
/** Sitemap für Google – wird per .htaccess unter /sitemap.xml ausgeliefert. */
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/layout.php';

$o = canonical_origin();
$d = content_load();
$last = date('Y-m-d', (int)@filemtime(CONTENT_FILE) ?: time());
$urls = [['/', '1.0', $last], ['/projekte/', '0.8', $last]];
foreach (array_keys(services()) as $k) {
    $urls[] = ['/' . $k . '/', '0.9', date('Y-m-d', (int)@filemtime(__DIR__ . '/inc/services.php'))];
}
foreach (visible_projects($d) as $p) {
    $urls[] = ['/projekte/' . $p['slug'] . '/', '0.7', $last];
}
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$path, $prio, $mod]) {
    echo '  <url><loc>' . e($o . $path) . '</loc><lastmod>' . $mod . '</lastmod><priority>' . $prio . '</priority></url>' . "\n";
}
echo '</urlset>' . "\n";
