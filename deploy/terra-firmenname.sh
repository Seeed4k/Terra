#!/bin/sh
# Terra & Garten Hübers – Kopfzeile als vollständiger Firmenname
#   „Terra & Garten - Ihr Dienstleister Hübers“
# Kopfzeile wird zu:
#   TERRA & GARTEN
#   — IHR DIENSTLEISTER — Hübers
# Außerdem: ©-Zeile im Footer mit dem exakten Firmennamen.
#
# Aufruf im Web-Ordner der Seite (dort, wo assets/ liegt):
#   sh terra-firmenname.sh          -> Probelauf
#   sh terra-firmenname.sh --apply  -> Backup + Änderung
# Mehrfaches Ausführen ist unschädlich.

set -eu
APPLY=0; [ "${1:-}" = "--apply" ] && APPLY=1
command -v perl >/dev/null || { echo "perl fehlt auf dem Server"; exit 1; }

CSS=$(find . -path '*/assets/css/site.css' -not -path './backup-*' | head -n1)
[ -n "$CSS" ] || { echo "assets/css/site.css nicht gefunden – bitte im Web-Ordner starten"; exit 1; }

FILES=$(grep -rlI --exclude-dir='backup-*' --exclude-dir=uploads \
  -e 'class="brand"' -e 'margin-top:1.5rem;font-size:.85rem">©' \
  --include='*.html' --include='*.htm' --include='*.php' --include='*.phtml' --include='*.tpl' \
  --include='*.inc' --include='*.json' --include='*.twig' . || true)

echo "CSS-Datei: $CSS"
echo "Gefundene Dateien:"; for f in $FILES; do echo "  $f"; done
[ -n "$FILES" ] || { echo "Keine passenden Dateien gefunden – nichts geändert."; exit 1; }

if [ $APPLY -eq 0 ]; then
  echo; echo "Probelauf. Mit --apply ausführen, um die Dateien zu ändern."; exit 0
fi

BK="backup-terra-firmenname-$(date +%Y%m%d-%H%M%S)-$$.tar.gz"
tar -czf "$BK" "$CSS" $FILES
echo "Backup: $BK"

for f in $FILES; do
  perl -0pi -e '
    # Kopfzeile: Hübers + Zusatz -> Zeile „— IHR DIENSTLEISTER — Hübers“
    s{<i>Hübers</i>\s*<small>Ihr Dienstleister\.?</small>}{<span class="brand-sub"><small>Ihr Dienstleister</small><i>Hübers</i></span>}g;
    s{<i>Hübers</i>(</a>)}{<span class="brand-sub"><small>Ihr Dienstleister</small><i>Hübers</i></span>$1}g unless /class="brand-sub"/;
    # Screenreader-Text der Kopfzeile
    s{aria-label="Terra (&amp;|&) Garten Hübers, zum Seitenanfang"}{aria-label="Terra $1 Garten - Ihr Dienstleister Hübers, zum Seitenanfang"}g;
    # ©-Zeile mit exaktem Firmennamen
    s{(<p style="margin-top:1\.5rem;font-size:\.85rem">©\s*[^<]*?)Terra (&amp;|&) Garten[^<]*?Hübers(?:\s+Hübers)?(</p>)}{$1Terra $2 Garten - Ihr Dienstleister Hübers$3}g;
  ' "$f"
  echo "bearbeitet: $f"
done

if ! grep -q 'Webhafen Firmenname' "$CSS"; then
  cat >> "$CSS" <<'EOF'

/* Webhafen Firmenname: „Terra & Garten - Ihr Dienstleister Hübers“ in der Kopfzeile */
.brand{flex-direction:column;flex-wrap:nowrap;align-items:flex-start;gap:.32rem}
.brand .brand-sub{display:flex;align-items:center;gap:.5rem;line-height:1}
.brand .brand-sub small{flex-basis:auto;margin:0;display:flex;align-items:center;gap:.4rem;font-family:var(--body);font-size:.68rem;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:#d8d2c5;line-height:1}
.brand .brand-sub small::before,.brand .brand-sub small::after{content:"";display:block;width:.9rem;height:1px;background:currentColor;opacity:.6}
.brand .brand-sub i{line-height:.9}
@media (max-width:820px){.top .brand .brand-sub small{font-size:.58rem;letter-spacing:.17em}.top .brand .brand-sub i{font-size:.95rem}}
EOF
  echo "CSS ergänzt: $CSS"
fi

echo; echo "Fertig. Rückgängig machen:  tar -xzf $BK"
