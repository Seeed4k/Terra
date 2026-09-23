<?php
/**
 * Bild-Upload: prüft den Dateityp, entfernt Metadaten (z. B. GPS-Standort aus
 * Handyfotos), verkleinert auf Webgröße und erzeugt ein Vorschaubild.
 */
declare(strict_types=1);

const IMG_MAX_EDGE   = 1920; // lange Kante des großen Bildes
const IMG_THUMB_W    = 720;  // Breite des Vorschaubildes in der Galerie
const IMG_QUALITY    = 80;
const IMG_THUMB_Q    = 72;
const IMG_MAX_PIXELS = 40000000; // Schutz vor riesigen Dateien (40 Megapixel)

/**
 * @return array{src:string,thumb:string,w:int,h:int}
 */
function image_store(string $tmpPath, string $subdir = 'projekte'): array
{
    $info = @getimagesize($tmpPath);
    if ($info === false) {
        throw new RuntimeException('Die Datei ist kein gültiges Bild.');
    }
    [$w, $h, $type] = $info;
    if (!in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
        throw new RuntimeException('Bitte nur JPG-, PNG- oder WebP-Bilder hochladen.');
    }
    if ($w * $h > IMG_MAX_PIXELS) {
        throw new RuntimeException('Das Bild ist zu groß (max. 40 Megapixel).');
    }

    $dir = UPLOAD_DIR . '/' . $subdir;
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Upload-Ordner konnte nicht angelegt werden.');
    }
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6));
    $rel  = 'uploads/' . $subdir . '/';

    if (!function_exists('imagecreatetruecolor')) {
        // Ohne GD-Erweiterung: nur JPG unverändert übernehmen
        if ($type !== IMAGETYPE_JPEG) {
            throw new RuntimeException('Auf diesem Server können nur JPG-Bilder hochgeladen werden.');
        }
        if (!copy($tmpPath, $dir . '/' . $name . '.jpg')) {
            throw new RuntimeException('Bild konnte nicht gespeichert werden.');
        }
        return ['src' => $rel . $name . '.jpg', 'thumb' => $rel . $name . '.jpg', 'w' => $w, 'h' => $h];
    }

    switch ($type) {
        case IMAGETYPE_JPEG:
            $im = @imagecreatefromjpeg($tmpPath);
            break;
        case IMAGETYPE_PNG:
            $im = @imagecreatefrompng($tmpPath);
            break;
        default:
            $im = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmpPath) : false;
    }
    if (!$im) {
        throw new RuntimeException('Das Bild konnte nicht gelesen werden.');
    }

    // Handyfotos: Ausrichtung aus EXIF übernehmen, bevor die Metadaten wegfallen
    if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $exif = @exif_read_data($tmpPath);
        $rot = [3 => 180, 6 => -90, 8 => 90][(int)($exif['Orientation'] ?? 1)] ?? 0;
        if ($rot) {
            $r = imagerotate($im, $rot, 0);
            if ($r) {
                $im = $r;
            }
        }
    }

    $w = imagesx($im);
    $h = imagesy($im);
    $big = image_resize($im, $w, $h, IMG_MAX_EDGE, IMG_MAX_EDGE);
    $bw = imagesx($big);
    $bh = imagesy($big);
    $thumb = image_resize($big, $bw, $bh, IMG_THUMB_W, IMG_THUMB_W * 3);

    imageinterlace($big, true);
    imageinterlace($thumb, true);
    $ok = imagejpeg($big, $dir . '/' . $name . '.jpg', IMG_QUALITY)
       && imagejpeg($thumb, $dir . '/' . $name . '-klein.jpg', IMG_THUMB_Q);
    unset($im, $big, $thumb); // Speicher sofort freigeben (imagedestroy ist ab PHP 8 überflüssig)
    if (!$ok) {
        throw new RuntimeException('Bild konnte nicht gespeichert werden.');
    }
    return ['src' => $rel . $name . '.jpg', 'thumb' => $rel . $name . '-klein.jpg', 'w' => $bw, 'h' => $bh];
}

/** Proportional verkleinern (nie vergrößern); Transparenz wird weiß. */
function image_resize($im, int $w, int $h, int $maxW, int $maxH)
{
    $scale = min(1, $maxW / $w, $maxH / $h);
    $nw = max(1, (int)round($w * $scale));
    $nh = max(1, (int)round($h * $scale));
    $out = imagecreatetruecolor($nw, $nh);
    imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
    imagecopyresampled($out, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
    return $out;
}

/** Bilddateien eines Eintrags löschen (nur innerhalb von uploads/). */
function image_delete($img): void
{
    if (!is_array($img)) {
        return;
    }
    foreach (['src', 'thumb'] as $k) {
        $rel = (string)($img[$k] ?? '');
        if ($rel === '' || strpos($rel, 'uploads/') !== 0 || strpos($rel, '..') !== false) {
            continue;
        }
        $path = ROOT . '/' . $rel;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
