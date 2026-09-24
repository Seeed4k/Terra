<?php
// Nur für lokale Tests: php -S localhost:8000 tools/dev-router.php
// (bildet die Umleitungen aus der .htaccess nach)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$root = dirname(__DIR__);
if ($uri === '/sitemap.xml') { chdir($root); require $root . '/sitemap.php'; return true; }
if (preg_match('#^/projekte/([a-z0-9-]+)/?$#', $uri, $m)) { $_GET['p'] = $m[1]; chdir($root . '/projekte'); require $root . '/projekte/index.php'; return true; }
if (preg_match('#^/(data|inc)(/|$)#', $uri)) { http_response_code(403); echo 'Forbidden'; return true; }
return false;
