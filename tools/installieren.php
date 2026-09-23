<?php
/**
 * Installations-Helfer für Terra & Garten
 *
 * Zusammen mit terra.zip in das Hauptverzeichnis der Domain hochladen und
 * https://ihre-domain.de/installieren.php im Browser öffnen.
 * Entpackt die Webseite, prüft den Server und löscht sich danach selbst.
 *
 * Bei einem Update (Seite ist schon installiert) bleiben die Ordner data/ und
 * uploads/ unangetastet – Projekte, Bilder und Passwort des Kunden bleiben erhalten.
 */
declare(strict_types=1);

const ZIP_NAME = 'terra.zip';
const KEEP_ON_UPDATE = ['data/', 'uploads/'];

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex');

$dir = __DIR__;
$zipFile = $dir . '/' . ZIP_NAME;
$isUpdate = is_file($dir . '/data/content.json');
$step = $_POST['step'] ?? '';

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/** Prüfungen vor dem Entpacken */
function checks(string $dir, string $zipFile): array
{
    return [
        ['PHP-Version ' . PHP_VERSION . ' (mind. 7.4, empfohlen 8.2+)', version_compare(PHP_VERSION, '7.4', '>=')],
        ['ZIP-Unterstützung auf dem Server', class_exists('ZipArchive')],
        ['Datei ' . ZIP_NAME . ' gefunden', is_file($zipFile)],
        ['Ordner ist beschreibbar', is_writable($dir)],
        ['Bildbearbeitung (GD) verfügbar', function_exists('imagecreatetruecolor')],
    ];
}

/** Entpacken mit Schutz vor manipulierten Pfaden */
function extract_site(string $zipFile, string $dir, bool $isUpdate): array
{
    $zip = new ZipArchive();
    if ($zip->open($zipFile) !== true) {
        throw new RuntimeException('Die Datei ' . ZIP_NAME . ' ist beschädigt. Bitte erneut hochladen.');
    }
    $written = 0;
    $kept = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = str_replace('\\', '/', (string)$zip->getNameIndex($i));
        if ($name === '' || $name[0] === '/' || strpos($name, '..') !== false || strpos($name, ':') !== false) {
            continue;
        }
        if ($isUpdate) {
            foreach (KEEP_ON_UPDATE as $keep) {
                if (strpos($name, $keep) === 0 && $name !== $keep . '.htaccess') {
                    $kept++;
                    continue 2;
                }
            }
        }
        $target = $dir . '/' . $name;
        if (substr($name, -1) === '/') {
            if (!is_dir($target) && !mkdir($target, 0755, true)) {
                throw new RuntimeException('Ordner konnte nicht angelegt werden: ' . $name);
            }
            continue;
        }
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }
        $data = $zip->getFromIndex($i);
        if ($data === false || file_put_contents($target, $data) === false) {
            throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $name);
        }
        $written++;
    }
    $zip->close();
    return [$written, $kept];
}

$error = '';
$done = null;
if ($step === 'install') {
    try {
        foreach (checks($dir, $zipFile) as [$label, $ok]) {
            if (!$ok && strpos($label, 'GD') === false) {
                throw new RuntimeException('Voraussetzung fehlt: ' . $label);
            }
        }
        $done = extract_site($zipFile, $dir, $isUpdate);
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
if ($step === 'cleanup') {
    @unlink($zipFile);
    @unlink(__FILE__);
    header('Location: ' . ($isUpdate && is_file($dir . '/data/admin.php') ? './' : './admin/'), true, 303);
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Installation – Terra &amp; Garten</title>
<style>
  body{margin:0;background:#1d1a15;color:#1d1a15;font:17px/1.5 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;padding:1rem}
  main{background:#fff;max-width:560px;margin:2rem auto;border-radius:10px;padding:1.5rem 1.4rem}
  h1{margin:0 0 .6rem;font-size:1.5rem}
  ul{list-style:none;padding:0;margin:1rem 0}
  li{padding:.55rem 0;border-bottom:1px solid #eee6d8;display:flex;gap:.6rem}
  .ok::before{content:"✔";color:#3f6b3a;font-weight:700}
  .bad::before{content:"✖";color:#a23b2a;font-weight:700}
  .warn::before{content:"!";color:#9a6b12;font-weight:700;width:1ch;text-align:center}
  .wait::before{content:"…";color:#6a6356}
  button{font:inherit;font-weight:600;width:100%;padding:.9rem;border:0;border-radius:6px;background:#1d1a15;color:#ece7dc;margin-top:.8rem}
  .msg{padding:.8rem 1rem;border-radius:6px;margin:1rem 0}
  .err{background:#f7e3df}.info{background:#efe9dc}.good{background:#e8f0e3}
  small{color:#6a6356}
</style>
</head>
<body>
<main>
<?php if ($done === null): ?>
  <h1><?= $isUpdate ? 'Webseite aktualisieren' : 'Webseite installieren' ?></h1>
  <?php if ($isUpdate): ?>
    <div class="msg info">Die Seite ist bereits installiert. Es werden nur die Programmdateien ersetzt – <b>Projekte, Bilder, Texte und Passwort bleiben erhalten</b>.</div>
  <?php endif; ?>
  <?php if ($error): ?><div class="msg err"><?= h($error) ?></div><?php endif; ?>
  <ul>
    <?php foreach (checks($dir, $zipFile) as [$label, $ok]):
        $cls = $ok ? 'ok' : (strpos($label, 'GD') !== false ? 'warn' : 'bad'); ?>
      <li class="<?= $cls ?>"><?= h($label) ?></li>
    <?php endforeach; ?>
  </ul>
  <form method="post"><input type="hidden" name="step" value="install"><button>Jetzt <?= $isUpdate ? 'aktualisieren' : 'installieren' ?></button></form>
  <p><small>Falls ein Punkt rot ist: In IONOS unter „Hosting“ die PHP-Version auf 8.2 oder neuer stellen bzw. die Datei <?= ZIP_NAME ?> in denselben Ordner wie diese Datei hochladen.</small></p>
<?php else: ?>
  <h1>Fertig entpackt</h1>
  <div class="msg good"><?= (int)$done[0] ?> Dateien geschrieben<?= $done[1] ? ', ' . (int)$done[1] . ' Kundendateien unverändert gelassen' : '' ?>.</div>
  <ul id="live">
    <li class="wait" data-url="./" data-expect="200">Startseite erreichbar</li>
    <li class="wait" data-url="impressum/" data-expect="200">Impressum erreichbar</li>
    <li class="wait" data-url="data/content.json" data-expect="403">Interne Daten geschützt (muss gesperrt sein)</li>
    <li class="wait" data-url="inc/bootstrap.php" data-expect="403">Programmdateien geschützt (muss gesperrt sein)</li>
    <li class="wait" id="https">Verbindung verschlüsselt (https)</li>
  </ul>
  <div class="msg err" id="warn" hidden>Mindestens eine Prüfung ist fehlgeschlagen. Bitte den Punkt oben beheben (oder mir einen Screenshot schicken), bevor Sie fortfahren.</div>
  <form method="post"><input type="hidden" name="step" value="cleanup"><button><?= $isUpdate ? 'Aufräumen und zur Webseite' : 'Aufräumen und Admin-Passwort festlegen' ?></button></form>
  <p><small>Der Knopf löscht diese Installationsdatei und <?= ZIP_NAME ?> vom Server<?= $isUpdate ? '' : ' und öffnet danach die Ersteinrichtung des Verwaltungsbereichs. <b>Das Passwort bitte sofort festlegen.</b>' ?></small></p>
  <script>
    var bad=false;
    document.querySelectorAll('#live li[data-url]').forEach(function(li){
      fetch(li.dataset.url,{cache:'no-store',redirect:'follow'}).then(function(r){
        var ok=String(r.status)===li.dataset.expect;li.className=ok?'ok':'bad';if(!ok){bad=true;li.textContent+=' – Status '+r.status;document.getElementById('warn').hidden=false}
      }).catch(function(){li.className='bad';document.getElementById('warn').hidden=false});
    });
    var h=document.getElementById('https');
    if(location.protocol==='https:'){h.className='ok'}else{h.className='warn';h.textContent='Noch kein https – in IONOS unter „Domains & SSL“ das SSL-Zertifikat zuweisen, dann diese Seite mit https:// neu laden.'}
  </script>
<?php endif; ?>
</main>
</body>
</html>
