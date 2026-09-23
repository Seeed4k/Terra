<?php
/**
 * Bereinigt HTML aus dem Texteditor (Rechtstexte). Erlaubt sind nur einfache
 * Formatierungen – Skripte, Styles, iframes usw. werden entfernt.
 */
declare(strict_types=1);

function sanitize_html(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }
    $allowed = ['h2', 'h3', 'h4', 'p', 'br', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'a'];
    $drop    = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'noscript', 'template', 'svg', 'math', 'img', 'video', 'audio', 'link', 'meta', 'head', 'title'];

    $doc = new DOMDocument('1.0', 'UTF-8');
    $prev = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="root">' . $html . '</div></body></html>', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);

    $root = $doc->getElementById('root');
    if (!$root) {
        return '';
    }
    sanitize_node($root, $allowed, $drop);

    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }
    // Leere Absätze aus dem Editor entfernen
    $out = preg_replace('#<p>(\s|&nbsp;|<br>)*</p>#u', '', $out);
    return trim($out);
}

function sanitize_node(DOMNode $node, array $allowed, array $drop): void
{
    for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
        $child = $node->childNodes->item($i);
        if ($child instanceof DOMComment || $child instanceof DOMProcessingInstruction) {
            $node->removeChild($child);
            continue;
        }
        if (!($child instanceof DOMElement)) {
            continue;
        }
        $tag = strtolower($child->tagName);
        if (in_array($tag, $drop, true)) {
            $node->removeChild($child);
            continue;
        }
        sanitize_node($child, $allowed, $drop);

        if ($tag === 'div' || $tag === 'h1' || $tag === 'h5' || $tag === 'h6') {
            // Editor erzeugt teils <div> statt <p>; h1 ist der Seitentitel → umwandeln
            $map = ['div' => 'p', 'h1' => 'h2', 'h5' => 'h4', 'h6' => 'h4'];
            $repl = $node->ownerDocument->createElement($map[$tag]);
            while ($child->firstChild) {
                $repl->appendChild($child->firstChild);
            }
            $node->replaceChild($repl, $child);
            continue;
        }
        if (!in_array($tag, $allowed, true)) {
            // Unbekanntes Element: Inhalt behalten, Hülle entfernen
            while ($child->firstChild) {
                $node->insertBefore($child->firstChild, $child);
            }
            $node->removeChild($child);
            continue;
        }
        $href = $tag === 'a' ? trim($child->getAttribute('href')) : '';
        for ($a = $child->attributes->length - 1; $a >= 0; $a--) {
            $child->removeAttribute($child->attributes->item($a)->nodeName);
        }
        if ($tag === 'a') {
            if (!preg_match('#^(https?://|mailto:|tel:|\#)#i', $href)) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }
            $child->setAttribute('href', $href);
            if (preg_match('#^https?://#i', $href)) {
                $child->setAttribute('target', '_blank');
                $child->setAttribute('rel', 'noopener');
            }
        }
    }
}
