# ClubCMS Roadmap

Dokument für Fortschritt, Versionierung und nächste Schritte.

Dieses Dokument ist bewusst so aufgebaut, dass wir es laufend erweitern können:

- neue Punkte einfach unten ergänzen
- erledigte Punkte mit einem grünen Haken markieren
- offene Punkte mit einem roten Kreuz markieren
- Versionen als Meilensteine pflegen
- den aktuellen Status oben aktuell halten

## Aktueller Stand

- Projektstatus: Inhaltsverwaltung begonnen
- Aktuelle Version: `v0.4.1`
- Nächster sinnvoller Meilenstein: `v0.5.0`

## Versionierungsmodell

Wir verwenden eine einfache semantische Versionierung für das Produkt:

- `v0.x.y` = Aufbauphase, Funktionen werden schrittweise ergänzt
- `v1.0.0` = erster stabiler, nutzbarer Funktionsumfang
- `x` = größere Ausbaustufe
- `y` = kleine Ergänzungen, Korrekturen, Detailverbesserungen

Beispiel:

- `v0.1.0` = Grundstruktur vorhanden
- `v0.2.0` = erste Inhaltslogik und Rollenlogik
- `v0.2.1` = kleine Nacharbeiten ohne neue Kernfunktion
- `v0.2.2` = Wartung und Tooling-Verbesserungen
- `v0.2.3` = Bugfixes und Robustheit
- `v0.3.0` = Inhaltsverwaltung beginnt
- `v0.3.1` = Shortcode-Feinsteuerung für Spalten
- `v0.3.2` = Einzelspalten-Shortcode
- `v0.4.0` = Card-Workflow im Backend
- `v0.4.1` = Frontend-Aktionen für Cards verbunden

## Roadmap nach Versionen

### v0.1.0 — Projektgrundlage

Ziel: Das Plugin ist strukturell sauber aufgesetzt und die Basis ist klar.

Erledigt:

- ✅ Plugin-Struktur prüfen und festziehen
- ✅ Hauptmodule und Namenskonventionen definieren
- ✅ Inhalte und Layout-Anforderungen aus README und Vorlage festhalten
- ✅ Basis-Dokumentation anlegen
- ✅ Landingpage-Grundstruktur als Shortcode bereitstellen
- ✅ technische Architektur für Cards, Shortcodes und Editoren definieren

Offen:

- ❌ Rollenmodell für Admin / Redakteur / eingeloggte Nutzer festlegen

### v0.2.0 — Card-Grundlage

Ziel: Inhalte können als Cards gespeichert und auf der Landingpage angezeigt werden.

Erledigt:

- ✅ Card-Datenmodell ergänzt
- ✅ Card-Persistenz in der WordPress-Optionsspeicherung ergänzt
- ✅ Landingpage rendert Kategorien als Spalten
- ✅ Beiträge werden innerhalb der Spalten angezeigt
- ✅ Basis-Tests für Card-Modell und Rendering ergänzt

Offen:

- ❌ echte Beitragsverwaltung mit Anlegen/Bearbeiten/Löschen
- ❌ Sortierung je Kategorie vollständig parametrierbar machen
- ❌ Sichtbarkeitslogik für eingeloggte Nutzer präzisieren

### v0.2.1 — Testseite verbessert

Ziel: Die ClubCMS-Testseite kann jeden Test einzeln starten.

Erledigt:

- ✅ Einzeltests auf der Diagnostics-Seite ergänzt
- ✅ Test-Auswahl auf erlaubte Tests begrenzt
- ✅ Übersichtlicher Start-Button für alle Tests beibehalten

Offen:

- ❌ Testergebnisse pro Einzeltest historisieren
- ❌ Statusanzeige für letzte Ausführung ergänzen

### v0.2.2 — WordPress-Stubs ergänzt

Ziel: IDE- und Analyse-Warnungen zu WordPress-Funktionen reduzieren.

Erledigt:

- ✅ WordPress-Stubs für globale Funktionen ergänzt
- ✅ Dev-Autoload um Stubs erweitert
- ✅ Stubs in den direkten Testlauf eingebunden

Offen:

- ❌ falls nötig weitere WP-spezifische Helper ergänzen
- ❌ Analysewarnungen im Editor gegenprüfen

### v0.2.3 — Testseite abgesichert

Ziel: Die Diagnostics-Seite darf nicht mehr fatale Fehler werfen, wenn Testklassen fehlen.

Erledigt:

- ✅ Testklassen nur nach dem Laden instanziiert
- ✅ fehlende Testdateien werden abgefangen
- ✅ Testseite bleibt auch bei unvollständiger Installation stabil

Offen:

- ❌ optional: Hinweise zur Testseite in der Admin-Doku ergänzen

### v0.3.0 — Inhaltsverwaltung

Ziel: Inhalte können gepflegt und strukturiert zugeordnet werden.

Erledigt:

- ✅ Kategorie-/Themen-Editor als CRUD-Funktion ergänzt
- ✅ Formularfelder je Kategorie definierbar gemacht
- ✅ Kategorien bearbeitbar und löschbar gemacht
- ✅ Felddefinitionen bearbeitbar und löschbar gemacht

Offen:

- ❌ Zuordnung von Themen zu Spalten per Shortcode
- ❌ Initiale Beitragslogik für Cards
- ❌ Datenmodell für Beiträge und Kategorien konsolidieren

### v0.3.1 — Spaltenzuordnung per Shortcode

Ziel: Die Themen in den 4 Spalten können im Shortcode direkt festgelegt werden.

Erledigt:

- ✅ Shortcode nimmt explizite Spaltenzuordnung an
- ✅ nicht gesetzte Spalten füllen sich automatisch mit verbleibenden Kategorien
- ✅ Shortcode-Dokumentation ergänzt
- ✅ Test für die Spaltenzuordnung ergänzt

Offen:

- ❌ Auswahl im Backend-Editor noch nicht vorhanden
- ❌ Validierung nicht vorhandener Kategorien im Frontend sichtbarer machen

### v0.3.2 — Einzelspalte als Shortcode

Ziel: Jedes Thema kann als einzelne Spalte per Shortcode eingebunden werden.

Erledigt:

- ✅ Einzelspalten-Shortcode ergänzt
- ✅ Thema kann über ID oder Slug adressiert werden
- ✅ README um Einzelspalten-Beispiel ergänzt
- ✅ Test für Einzelspalten-Rendering ergänzt

Offen:

- ❌ Kurzcode-Generator im Backend fehlt noch
- ❌ sichtbare Fehlermeldung für unbekannte Themen fehlt noch

### v0.4.0 — Card-Workflow im Backend

Ziel: Cards können im Admin-Backend angelegt, bearbeitet und gelöscht werden.

Erledigt:

- ✅ Card-Adminseite ergänzt
- ✅ Card-Submission-Handler ergänzt
- ✅ Card-Repositories um getById/delete erweitert
- ✅ Card-Formular mit Kategorie-, Status- und Sichtbarkeitsfeldern ergänzt
- ✅ Testabdeckung für Card-Speicherung und -Löschung ergänzt

Offen:

- ❌ Rechtekonzept für Redakteure noch nicht umgesetzt
- ❌ Backend-Zugriff für Redakteure noch nicht gesperrt

### v0.4.1 — Frontend-Aktionen verbunden

Ziel: Die im Frontend sichtbaren Aktionen führen zu den Card-Editorseiten.

Erledigt:

- ✅ Neuer-Beitrag-Link pro Themenkarte ergänzt
- ✅ Bearbeiten-Link pro Card ergänzt
- ✅ Löschen per Frontend-Formular an den Card-Editor angebunden
- ✅ Kategorie kann beim neuen Card-Entwurf vorgewählt werden

Offen:

- ❌ Redakteursrechte und zentraler Editor noch nicht umgesetzt
- ❌ Backend-Zugriff für Redakteure noch nicht gesperrt

### v0.5.0 — Redaktions- und Rechtekonzept

Ziel: Das Produkt ist im redaktionellen Alltag kontrolliert nutzbar.

Erledigt:

- ❌ Rollen und Berechtigungen finalisieren
- ❌ Zugriffspfade zum Editor absichern

Offen:

- ❌ Admin darf WordPress-Backend nutzen
- ❌ Redakteure arbeiten nur über ClubCMS-Editor
- ❌ Fehler- und Berechtigungsfälle definieren

### v1.0.0 — Erste produktive Version

Ziel: Die erste belastbare Version für echten Einsatz.

Erledigt:

- ❌ Landingpage strukturell umgesetzt
- ❌ Themen/Spalten funktionieren
- ❌ Bearbeitungsworkflow vorhanden
- ❌ Rollenmodell umgesetzt
- ❌ Dokumentation vollständig genug für die Nutzung

Offen:

- ❌ Feinschliff
- ❌ weitere Sonderfälle
- ❌ gewünschte Erweiterungen aus dem Alltag

## Fortschrittspflege

Wenn wir weiterarbeiten, wird dieser Block jeweils aktualisiert:

- Version: `v0.4.1`
- Letzte Änderung: 2026-07-13
- Nächster Fokus: Rollenlogik und Backend-Sperre für Redakteure

## Änderungslog

### v0.3.0 — Inhaltsverwaltung gestartet

- Kategorie- und Felddefinitions-Editor erweitert
- Bearbeiten und Löschen für Kategorien und Felddefinitionen ergänzt
- Repositories um CRUD-Methoden erweitert
- Submission-Handler auf Update/Delete vorbereitet

### v0.3.1 — Spaltenzuordnung per Shortcode

- Shortcode um `spalte_1` bis `spalte_4` erweitert
- README um Beispiel ergänzt
- Landingpage-Shortcode-Test ergänzt

### v0.3.2 — Einzelspalten-Shortcode

- neuer Shortcode `clubcms_column`
- einzelne Themenkarte direkt einbindbar

### v0.4.0 — Card-Workflow im Backend

- Cards im Backend verwaltbar gemacht
- Card-Adminseite eingeführt
- Card-Handler und Tests ergänzt

### v0.4.1 — Frontend-Aktionen verbunden

- Frontend-Icons auf Cards an die Card-Verwaltung angebunden
- New/Edit/Delete-Linklogik ergänzt

### v0.2.3 — Testseite abgesichert

- Testseite gegen fehlende Testklassen abgesichert
- Diagnostics-Seite stabilisiert

### v0.2.2 — WordPress-Stubs ergänzt

- WordPress-Stubs für IDE und lokale Entwicklung ergänzt

### v0.2.1 — Testseite verbessert

- Testseite um Einzelstart pro Test erweitert

### v0.2.0 — Cards und Anzeige

- Roadmap angelegt
- Versionierungsmodell definiert
- Meilensteine für den Produktaufbau festgehalten
- README überarbeitet
- Landingpage-Grundstruktur als renderbarer Shortcode begonnen
- Testabdeckung für den ersten Frontend-Baustein ergänzt
- Card-Modell und Repository ergänzt
- Renderer liest Cards und Kategorien
- Tests für Card- und Landingpage-Ausgabe ergänzt

## Offene Ideen für spätere Ergänzungen

- Vorschau-Modus für Redakteure
- Drag-and-drop für Karten und Themen
- Archivbereich für alte Beiträge
- Medienverwaltung pro Card
- automatische Startseiten-Teaser aus Kategorien
- Protokollierung von Änderungen
