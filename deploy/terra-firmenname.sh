#!/bin/sh
# Terra & Garten Hübers – Kopfzeile als vollständiger Firmenname
#   „Terra & Garten - Ihr Dienstleister Hübers“
# Kopfzeile wird zu:
#   TERRA & GARTEN
#   — IHR DIENSTLEISTER — Hübers
# Außerdem: ©-Zeile im Footer mit dem exakten Firmennamen und
# Footer-Hinweis „Webdesign & Entwicklung von <Webhafen-Logo> Webhafen“.
#
# Aufruf im Web-Ordner der Seite (dort, wo assets/ liegt):
#   sh terra-firmenname.sh          -> Probelauf
#   sh terra-firmenname.sh --apply  -> Backup + Änderung
# Mehrfaches Ausführen ist unschädlich.

set -eu
CREDIT='<p class="site-credit">Webdesign &amp; Entwicklung von <a class="wh" href="https://web-hafen.de" target="_blank" rel="noopener"><svg class="wh-mk" focusable="false" viewBox="0 0 64 48" aria-hidden="true"><path class="bf" d="M15.42 7.28 L4.36 23.02 L6.64 24.98 L20.58 11.72Z M4.36 24.98 L17.32 39.09 L18.68 37.91 L6.64 23.02Z M4.0 24a1.5 1.5 0 1 0 3.0 0a1.5 1.5 0 1 0 -3.0 0Z M43.42 11.72 L57.36 24.98 L59.64 23.02 L48.58 7.28Z M57.36 23.02 L45.32 37.91 L46.68 39.09 L59.64 24.98Z M57.0 24a1.5 1.5 0 1 0 3.0 0a1.5 1.5 0 1 0 -3.0 0Z"/><path class="wf" d="M22.18 25.18 L22.75 24.91 L23.29 24.64 L23.82 24.39 L24.33 24.16 L24.81 23.97 L25.27 23.82 L25.69 23.72 L26.06 23.67 L26.38 23.68 L26.64 23.73 L26.84 23.81 L27.00 23.92 L27.13 24.03 L27.25 24.15 L27.38 24.29 L27.55 24.45 L27.75 24.65 L27.98 24.88 L28.25 25.15 L28.55 25.45 L28.87 25.79 L29.23 26.17 L29.61 26.56 L30.01 26.97 L30.44 27.40 L30.90 27.82 L31.37 28.24 L31.88 28.65 L32.41 29.04 L32.98 29.40 L33.58 29.72 L34.21 30.00 L34.88 30.21 L35.58 30.34 L36.29 30.38 L37.00 30.32 L37.68 30.16 L38.31 29.91 L38.88 29.59 L39.39 29.22 L39.85 28.80 L40.27 28.34 L40.65 27.86 L40.99 27.36 L41.32 26.84 L41.63 26.30 L41.91 25.75 L42.18 25.18 L41.82 24.82 L41.25 25.09 L40.71 25.36 L40.18 25.61 L39.67 25.84 L39.19 26.03 L38.73 26.18 L38.31 26.28 L37.94 26.33 L37.62 26.32 L37.36 26.27 L37.16 26.19 L37.00 26.08 L36.87 25.97 L36.75 25.85 L36.62 25.71 L36.45 25.55 L36.25 25.35 L36.02 25.12 L35.75 24.85 L35.45 24.55 L35.13 24.21 L34.77 23.83 L34.39 23.44 L33.99 23.03 L33.56 22.60 L33.10 22.18 L32.63 21.76 L32.12 21.35 L31.59 20.96 L31.02 20.60 L30.42 20.28 L29.79 20.00 L29.12 19.79 L28.42 19.66 L27.71 19.62 L27.00 19.68 L26.32 19.84 L25.69 20.09 L25.12 20.41 L24.61 20.78 L24.15 21.20 L23.73 21.66 L23.35 22.14 L23.01 22.64 L22.68 23.16 L22.37 23.70 L22.09 24.25 L21.82 24.82Z"/></svg>Webhafen</a></p>'
export CREDIT
APPLY=0; [ "${1:-}" = "--apply" ] && APPLY=1
command -v perl >/dev/null || { echo "perl fehlt auf dem Server"; exit 1; }

# Sicherheitsprüfung: nur direkt im Web-Ordner der Terra-Seite laufen
[ "$(pwd)" != "$HOME" ] || { echo "ABBRUCH: Nicht im Home-Ordner starten. Erst per SSH auf den Server und in den Web-Ordner wechseln."; exit 1; }
CSS=./assets/css/site.css
[ -f "$CSS" ] || { echo "ABBRUCH: ./assets/css/site.css fehlt – das ist nicht der Web-Ordner der Terra-Seite."; exit 1; }
grep -rqsI 'Hübers' --include='*.php' --include='*.html' . || { echo "ABBRUCH: Keine Terra-Hübers-Dateien in diesem Ordner."; exit 1; }

FILES=$(grep -rlI --exclude-dir='backup*' --exclude-dir='.?*' --exclude-dir=uploads --exclude-dir='*Sicherung*' \
  -e 'class="brand"' -e 'margin-top:1.5rem;font-size:.85rem">©' \
  --include='*.html' --include='*.htm' --include='*.php' --include='*.phtml' --include='*.tpl' \
  --include='*.inc' --include='*.json' --include='*.twig' . | xargs -r grep -lI 'Hübers' || true)
N=$(printf '%s\n' $FILES | grep -c . || true)
[ "$N" -le 80 ] || { echo "ABBRUCH: $N Dateien gefunden – das ist zu viel für die Terra-Seite. Falscher Ordner?"; exit 1; }

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
    # Footer-Hinweis: Variante B mit Webhafen-Logo
    s{<p class="site-credit">.*?</p>}{$ENV{CREDIT}}gs unless /class="wh-mk"/;
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
/* Webhafen-Logo im Footer */
.site-credit a.wh{white-space:nowrap}
.wh-mk{display:inline-block;height:1.1em;width:auto;vertical-align:-.22em;margin-right:.3em;overflow:visible}
.wh-mk path{stroke:none}
.wh-mk .bf{fill:currentColor}
.wh-mk .wf{fill:#6F9BE0}
EOF
  echo "CSS ergänzt: $CSS"
fi

echo; echo "Fertig. Rückgängig machen:  tar -xzf $BK"
