# Terra & Garten Hübers – Webseite

Webseite mit einfachem Verwaltungsbereich (ohne Datenbank). Läuft auf jedem
Webhosting mit **PHP 7.4 oder neuer** (empfohlen PHP 8.2+) und Apache – also z. B.
jedem IONOS-Webhosting-Paket.

## Ordner

| Ordner / Datei      | Inhalt |
|---------------------|--------|
| `index.php`         | Startseite |
| `impressum/`, `datenschutz/` | Rechtstexte als eigene Seiten |
| `admin/`            | Verwaltungsbereich (Projekte, Bilder, Rechtstexte, Kontaktdaten) |
| `assets/`           | CSS, JavaScript, Schriften (lokal, DSGVO-konform), Logo, Porträt |
| `uploads/projekte/` | Projektbilder (werden über den Verwaltungsbereich gepflegt) |
| `data/`             | `content.json` mit allen pflegbaren Inhalten, Sicherungen, Passwort-Hash – **per `.htaccess` gesperrt** |
| `inc/`              | PHP-Hilfsfunktionen – **gesperrt** |

## Veröffentlichen bei IONOS

1. Im IONOS-Kundenkonto prüfen, dass ein **Webhosting-Paket** zur Domain gehört
   (reine Domain ohne Webspace reicht nicht) und unter *Hosting → PHP-Version*
   PHP 8.2 oder neuer eingestellt ist.
2. Unter *Domains & SSL* das (kostenlose) **SSL-Zertifikat** der Domain zuweisen.
   Die `.htaccess` leitet automatisch auf `https://` um.
3. Per **SFTP** (Zugangsdaten unter *Hosting → SFTP & SSH*, z. B. mit FileZilla)
   den **gesamten Inhalt** dieses Ordners in das Zielverzeichnis der Domain
   hochladen – inklusive der versteckten `.htaccess`-Dateien (in FileZilla:
   *Server → Versteckte Dateien anzeigen*). Nicht nötig: `.git`, `.gitignore`, `README.md`.
4. Sicherstellen, dass die Ordner `data/` und `uploads/` beschreibbar sind
   (bei IONOS normalerweise automatisch der Fall).
5. **Sofort danach** `https://ihre-domain.de/admin/` aufrufen und das Passwort
   für den Verwaltungsbereich festlegen. Solange kein Passwort gesetzt ist, kann
   jeder, der diese Adresse aufruft, eines festlegen.
6. Kontrolle: `https://ihre-domain.de/data/content.json` muss **„403 Forbidden“** liefern.

## Spätere Updates am Code

Beim erneuten Hochladen die Ordner **`data/` und `uploads/` nicht überschreiben** –
dort liegen die vom Kunden gepflegten Inhalte, Bilder und das Passwort.

## Verwaltungsbereich (`/admin/`)

- **Projekte**: Projekte anlegen, sortieren, ein-/ausblenden, löschen. Pro Projekt
  optional ein Vorher/Nachher-Regler und eine Bildergalerie mit Bildunterschriften.
  Handyfotos werden automatisch verkleinert, Metadaten (z. B. GPS-Standort) entfernt.
- **Rechtstexte**: Impressum und Datenschutzerklärung mit einfachem Texteditor.
  Nur sichere Formatierungen werden gespeichert.
- **Kontaktdaten**: Telefon/WhatsApp, E-Mail, Adresse – wirken auf der ganzen Seite.
- **Sicherungen**: Bei jedem Speichern wird der vorherige Stand gesichert (die letzten 30).
- **Passwort vergessen**: per SFTP `data/admin.php` löschen und `/admin/` neu aufrufen.

Schutzmaßnahmen: bcrypt-Passwort-Hash, CSRF-Schutz, Sperre nach 5 Fehlversuchen
(15 Min.), Sitzungs-Cookie nur für `/admin/` (HttpOnly, SameSite=Strict, Secure bei HTTPS),
Upload nur für echte Bilder, keine Skriptausführung im Upload-Ordner.

## Lokal testen

```bash
php -S localhost:8000
```

Dann `http://localhost:8000` bzw. `http://localhost:8000/admin/` öffnen.
