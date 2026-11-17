#!/bin/sh
# Terra & Garten Hübers – zwei Änderungen auf dem Live-Server einspielen
#   1. „Ihr Dienstleister.“ unter dem Schriftzug in der Kopfzeile
#   2. Footer: „Design & Entwicklung: Webhafen · web-hafen.de“
#      (und die kaputte Zeile „Terra & Garten ihr Dienstleister Hübers Hübers“ reparieren)
#
# Aufruf im Web-Ordner der Seite (dort, wo index.html bzw. assets/ liegt):
#   sh terra-aenderungen.sh          -> Probelauf, zeigt nur, was geändert würde
#   sh terra-aenderungen.sh --apply  -> macht ein Backup und ändert die Dateien
# Das Skript kann mehrfach laufen, es ändert nichts doppelt.

set -eu
APPLY=0; [ "${1:-}" = "--apply" ] && APPLY=1
command -v perl >/dev/null || { echo "perl fehlt auf dem Server"; exit 1; }

CSS=$(find . -path '*/assets/css/site.css' -not -path './backup-*' | head -n1)
[ -n "$CSS" ] || { echo "assets/css/site.css nicht gefunden – bitte im Web-Ordner starten"; exit 1; }

# Alle Text-Dateien, in denen Kopfzeile oder Footer vorkommen (HTML, PHP, Vorlagen, JSON)
FILES=$(grep -rlI --exclude-dir='backup-*' --exclude-dir=uploads \
  -e 'class="brand"' -e 'ihr Dienstleister Hübers Hübers' -e 'margin-top:1.5rem;font-size:.85rem">©' \
  --include='*.html' --include='*.htm' --include='*.php' --include='*.phtml' --include='*.tpl' \
  --include='*.inc' --include='*.json' --include='*.twig' . || true)

echo "CSS-Datei: $CSS"
echo "Gefundene Dateien:"; for f in $FILES; do echo "  $f"; done
[ -n "$FILES" ] || { echo "Keine passenden Dateien gefunden – nichts geändert."; exit 1; }

if [ $APPLY -eq 0 ]; then
  echo; echo "Probelauf. Mit --apply ausführen, um die Dateien zu ändern."; exit 0
fi

STAMP=$(date +%Y%m%d-%H%M%S)
BK="backup-terra-$STAMP-$$.tar.gz"
tar -czf "$BK" "$CSS" $FILES
echo "Backup: $BK"

for f in $FILES; do
  perl -0pi -e '
    # 1) Zusatz unter dem Schriftzug (nur einmal)
    s{(<i>Hübers</i>)(</a>)}{$1<small>Ihr Dienstleister.</small>$2}g unless /Ihr Dienstleister\.<\/small>/;
    # Kaputte Copyright-Zeile reparieren
    s{Terra (&amp;|&) Garten ihr Dienstleister Hübers Hübers}{Terra $1 Garten Hübers}g;
    # 2) Webhafen-Hinweis direkt nach der Copyright-Zeile (nur einmal)
    s{(<p style="margin-top:1\.5rem;font-size:\.85rem">©.*?</p>)}{$1\n    <p class="site-credit">Design &amp; Entwicklung: <a href="https://web-hafen.de" target="_blank" rel="noopener">Webhafen</a> · <a href="https://web-hafen.de" target="_blank" rel="noopener">web-hafen.de</a></p>}g unless /class="site-credit"/;
  ' "$f"
  echo "geändert: $f"
done

if ! grep -q 'Terra-Änderungen Webhafen' "$CSS"; then
  cat >> "$CSS" <<'EOF'

/* Terra-Änderungen Webhafen: Zusatz unter dem Logo + Footer-Hinweis */
.brand{flex-wrap:wrap;column-gap:.4rem;row-gap:0}
.brand small{flex-basis:100%;margin-top:.2rem;font-family:var(--body);font-size:.7rem;font-weight:500;letter-spacing:.18em;text-transform:uppercase;color:#b3ac9e;line-height:1}
@media (max-width:820px){.top .brand small{font-size:.62rem}}
footer{padding-bottom:calc(6rem + env(safe-area-inset-bottom,0px))}
.site-credit{margin:.4rem 0 0;font-size:.85rem}
.site-credit a{color:#d8d2c5;text-decoration:none}
.site-credit a:hover{color:var(--sand)}
EOF
  echo "CSS ergänzt: $CSS"
fi

echo; echo "Fertig. Rückgängig machen:  tar -xzf $BK"
echo "Hinweis: Browser-Cache leeren bzw. ?v= an site.css hochzählen, falls die alte CSS noch geladen wird."
