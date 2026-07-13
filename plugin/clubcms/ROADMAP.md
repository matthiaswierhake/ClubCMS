# ClubCMS Roadmap

Dokument für Fortschritt, Versionierung und nächste Schritte.

Dieses Dokument ist bewusst so aufgebaut, dass wir es laufend erweitern können:

- neue Punkte einfach unten ergänzen
- erledigte Punkte auf `Done` setzen
- Versionen als Meilensteine pflegen
- den aktuellen Status oben aktuell halten

## Aktueller Stand

- Projektstatus: Card-Grundlage umgesetzt
- Aktuelle Version: `v0.2.1`
- Nächster sinnvoller Meilenstein: `v0.3.0`

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

## Roadmap nach Versionen

### v0.1.0 — Projektgrundlage

Ziel: Das Plugin ist strukturell sauber aufgesetzt und die Basis ist klar.

Erledigt:

- [x] Plugin-Struktur prüfen und festziehen
- [x] Hauptmodule und Namenskonventionen definieren
- [x] Inhalte und Layout-Anforderungen aus README und Vorlage festhalten
- [x] Basis-Dokumentation anlegen
- [x] Landingpage-Grundstruktur als Shortcode bereitstellen

Offen:

- [x] technische Architektur für Cards, Shortcodes und Editoren definieren
- [ ] Rollenmodell für Admin / Redakteur / eingeloggte Nutzer festlegen

### v0.2.0 — Card-Grundlage

Ziel: Inhalte können als Cards gespeichert und auf der Landingpage angezeigt werden.

Erledigt:

- [x] Card-Datenmodell ergänzt
- [x] Card-Persistenz in der WordPress-Optionsspeicherung ergänzt
- [x] Landingpage rendert Kategorien als Spalten
- [x] Beiträge werden innerhalb der Spalten angezeigt
- [x] Basis-Tests für Card-Modell und Rendering ergänzt

Offen:

- [ ] echte Beitragsverwaltung mit Anlegen/Bearbeiten/Löschen
- [ ] Sortierung je Kategorie vollständig parametrierbar machen
- [ ] Sichtbarkeitslogik für eingeloggte Nutzer präzisieren

### v0.2.1 — Testseite verbessert

Ziel: Die ClubCMS-Testseite kann jeden Test einzeln starten.

Erledigt:

- [x] Einzeltests auf der Diagnostics-Seite ergänzt
- [x] Test-Auswahl auf erlaubte Tests begrenzt
- [x] Übersichtlicher Start-Button für alle Tests beibehalten

Offen:

- [ ] Testergebnisse pro Einzeltest historisieren
- [ ] Statusanzeige für letzte Ausführung ergänzen

### v0.3.0 — Inhaltsverwaltung

Ziel: Inhalte können gepflegt und strukturiert zugeordnet werden.

Erledigt:

- [ ] Kategorie-/Themen-Editor konzipieren
- [ ] Formularfelder je Kategorie definierbar machen

Offen:

- [ ] Zuordnung von Themen zu Spalten per Shortcode
- [ ] Initiale Beitragslogik für Cards
- [ ] Datenmodell für Beiträge und Kategorien konsolidieren

### v0.4.0 — Bearbeiten direkt aus der Oberfläche

Ziel: Eingeloggte Nutzer bekommen die vorgesehenen Bearbeitungsaktionen.

Erledigt:

- [ ] Icons für Löschen, Bearbeiten und Neuer Beitrag definieren
- [ ] Verlinkung in einen zentralen Editor spezifizieren

Offen:

- [ ] UI nur für berechtigte Nutzer einblenden
- [ ] Backend-Zugriff für Redakteure sperren
- [ ] Admin-Ausnahme sauber abbilden

### v0.5.0 — Redaktions- und Rechtekonzept

Ziel: Das Produkt ist im redaktionellen Alltag kontrolliert nutzbar.

Erledigt:

- [ ] Rollen und Berechtigungen finalisieren
- [ ] Zugriffspfade zum Editor absichern

Offen:

- [ ] Admin darf WordPress-Backend nutzen
- [ ] Redakteure arbeiten nur über ClubCMS-Editor
- [ ] Fehler- und Berechtigungsfälle definieren

### v1.0.0 — Erste produktive Version

Ziel: Die erste belastbare Version für echten Einsatz.

Erledigt:

- [ ] Landingpage strukturell umgesetzt
- [ ] Themen/Spalten funktionieren
- [ ] Bearbeitungsworkflow vorhanden
- [ ] Rollenmodell umgesetzt
- [ ] Dokumentation vollständig genug für die Nutzung

Offen:

- [ ] Feinschliff
- [ ] weitere Sonderfälle
- [ ] gewünschte Erweiterungen aus dem Alltag

## Fortschrittspflege

Wenn wir weiterarbeiten, wird dieser Block jeweils aktualisiert:

- Version: `v0.2.1`
- Letzte Änderung: 2026-07-13
- Nächster Fokus: Editor, Bearbeitungsworkflow und Rollenlogik

## Änderungslog

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
- Testseite um Einzelstart pro Test erweitert

## Offene Ideen für spätere Ergänzungen

- Vorschau-Modus für Redakteure
- Drag-and-drop für Karten und Themen
- Archivbereich für alte Beiträge
- Medienverwaltung pro Card
- automatische Startseiten-Teaser aus Kategorien
- Protokollierung von Änderungen
