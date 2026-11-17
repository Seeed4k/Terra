# Terra & Garten Hübers – Änderungen Logo-Zusatz & Webhafen-Footer

**Website:** https://terra-garten-huebers.de
**Repo:** `Seeed4k/Terra`, Branch `claude/terra-garten-changes-40hi85`
**Zeitraum:** 01.11.2026 – 17.11.2026

## Auftrag

Zwei kleine Änderungen an der Website:

1. **Kopfzeile:** Unter dem Schriftzug „TERRA & GARTEN Hübers“ soll zusätzlich **„Ihr Dienstleister.“** stehen.
2. **Footer:** Ganz unten auf jeder Seite soll stehen, dass die Seite von Webhafen gestaltet wurde:
   **„Design & Entwicklung: Webhafen · web-hafen.de“**

## Ablauf

### 1. Das richtige Repo finden
- In der Sitzung waren zuerst nur `Shop` und `Muttertag-2026` eingebunden. Keins von beiden enthält die Terra-Seite.
- Über die Repo-Liste wurde `Seeed4k/Terra` gefunden, eingebunden und geklont.

### 2. Änderungen im Repo (erledigt)
Beide Änderungen sind in der `index.html` umgesetzt und gepusht:

| Commit | Inhalt |
|---|---|
| `6ead208` | Header: „Ihr Dienstleister.“ unter dem Logo (klein, Großbuchstaben, dezent grau) |
| `68838dd` | Footer: „Design & Entwicklung: Webhafen · web-hafen.de“. Beide Teile verlinken auf https://web-hafen.de. Unter dem Footer ist zusätzlich Platz, damit der WhatsApp-Button den Hinweis nicht verdeckt. |

### 3. Problem: Repo ≠ Live-Seite
Der Vergleich mit der Live-Seite hat gezeigt, dass das Repo eine **ältere Fassung** ist:

- **Repo:** eine einzige `index.html`, die Bilder sind direkt eingebettet.
- **Live:** mehrere Seiten (`projekte/`, `impressum/`, `gartenbau-haren/` …) mit eigenen Ordnern für Bilder, CSS und JavaScript, gehostet auf einem Apache-Server bei IONOS.

Ein Merge in `main` bringt die Änderungen deshalb **nicht** live.

**Fehler auf der Live-Seite:** Im Footer steht dort
„© 2026 Terra & Garten ihr Dienstleister Hübers Hübers“. Diese Zeile wird beim Einspielen mit repariert.

### 4. SSH-Zugang
- Es gibt inzwischen einen SSH-Zugang zum Server.
- Aus der Claude-Cloud-Sitzung ist SSH aber nicht möglich, weil Port 22 gesperrt ist und nur HTTPS erlaubt ist.
- Der Rechner „Webhafen“ ist per Remote Control eingetragen, gibt aber keinen Ordner für Cloud-Sitzungen frei.
- **Lösung:** Ein Skript, das man selbst per SSH auf dem Server ausführt: `deploy/terra-aenderungen.sh`

## Live stellen mit dem Skript

Das Skript wurde auf einer Kopie der aktuellen Live-Seite (Startseite + Impressum) getestet. Es lässt sich gefahrlos mehrfach ausführen.

```sh
# 1. Skript auf den Server kopieren (Pfad/Host anpassen)
scp deploy/terra-aenderungen.sh BENUTZER@HOST:PFAD-ZUM-WEBORDNER/

# 2. Per SSH einloggen und in den Web-Ordner wechseln (dort, wo assets/ liegt)
ssh BENUTZER@HOST
cd PFAD-ZUM-WEBORDNER

# 3. Probelauf – zeigt nur, welche Dateien betroffen sind
sh terra-aenderungen.sh

# 4. Wirklich ändern (legt vorher ein Backup an)
sh terra-aenderungen.sh --apply
```

**Was das Skript macht:**
- Es sucht alle HTML-, PHP-, Vorlagen- und JSON-Dateien mit Kopfzeile oder Footer.
- Es legt ein Backup `backup-terra-<Datum>-<PID>.tar.gz` an.
- Es ergänzt `<small>Ihr Dienstleister.</small>` im Logo-Link.
- Es repariert die Zeile „ihr Dienstleister Hübers Hübers“ zu „Terra & Garten Hübers“.
- Es fügt den Webhafen-Hinweis nach der ©-Zeile ein.
- Es hängt die nötigen Styles an `assets/css/site.css` an.

**Rückgängig machen:** `tar -xzf backup-terra-….tar.gz` im Web-Ordner.

**Danach prüfen:** Seite am Handy und am Desktop neu laden, falls nötig den Cache leeren. Kontrollieren:
- Kopfzeile mit „IHR DIENSTLEISTER.“
- Footer mit Webhafen-Hinweis auf Startseite, Impressum, Projekten und den Leistungsseiten

## Offene Punkte
- [x] Skript per SSH auf dem Server ausgeführt (17.11.2026)
- [x] Live-Seite kontrolliert: Kopfzeile und Footer-Hinweis auf allen Seiten sichtbar
- [ ] ©-Zeile lautet live noch „Terra & Garten ihr Dienstleister Hübers“ – ggf. auf „Terra & Garten Hübers“ kürzen
- [ ] Empfehlung: Den echten, aktuellen Quellcode der Live-Seite ins Repo `Seeed4k/Terra` übernehmen. Dann passen Repo und Server wieder zusammen, und künftige Änderungen können direkt im Repo gemacht werden.

## Nachtrag: Kopfzeile als Firmenname (17.11.2026)

Der exakte Firmenname lautet **„Terra & Garten - Ihr Dienstleister Hübers“**. Die Kopfzeile ist jetzt wie das Rundlogo aufgebaut:

```
TERRA & GARTEN
— IHR DIENSTLEISTER —  Hübers
```

- Der Punkt hinter „Ihr Dienstleister“ entfällt.
- Die ©-Zeile im Footer lautet „© 2026 Terra & Garten - Ihr Dienstleister Hübers“.
- Einspielen per SSH im Web-Ordner:

```sh
curl -fsSLO https://raw.githubusercontent.com/Seeed4k/Terra/claude/terra-garten-changes-40hi85/deploy/terra-firmenname.sh
sh terra-firmenname.sh            # Probelauf
sh terra-firmenname.sh --apply    # Backup + Änderung
```

## Nachtrag: Footer mit Webhafen-Logo (Variante B)

Gewählt: **„Webdesign & Entwicklung von 〈~〉 Webhafen“**
- Das Logo ist das Original von web-hafen.de (zwei Klammern, Welle in `#6F9BE0`), als SVG, so hoch wie die Schrift.
- Logo und Name sind zusammen ein Link auf https://web-hafen.de.
- Die Domain wird nicht mehr zusätzlich ausgeschrieben.
- Steckt mit im Skript `deploy/terra-firmenname.sh`, zusammen mit der neuen Kopfzeile. Mockups liegen in `mockup/`.
