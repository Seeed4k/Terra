<?php
/** Nimmt anonyme Klick-Zählungen der Webseite entgegen (Anruf, WhatsApp, E-Mail). */
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex');
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    stats_event((string)($_POST['e'] ?? ''));
}
http_response_code(204);
