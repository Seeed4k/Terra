#!/bin/sh
# Baut das Installationspaket für den Server:
#   dist/terra.zip          – die Webseite (ohne README, Werkzeuge usw.)
#   dist/installieren.php   – Installations-Helfer
# Beide Dateien in das Hauptverzeichnis der Domain hochladen und
# https://ihre-domain.de/installieren.php aufrufen.
set -e
cd "$(dirname "$0")/.."
mkdir -p dist
rm -f dist/terra.zip
git archive --format=zip -o dist/terra.zip HEAD -- . ':!README.md' ':!.gitignore' ':!tools' ':!dist'
cp tools/installieren.php dist/installieren.php
echo "Fertig: dist/terra.zip und dist/installieren.php"
