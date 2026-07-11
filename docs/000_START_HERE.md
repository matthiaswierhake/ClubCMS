# ClubCMS 1.0

Willkommen zum Projekt **ClubCMS**.

Dieses Repository enthält die Entwicklung eines eigenständigen WordPress-Plugins zur Verwaltung strukturierter Inhalte auf Basis einer generischen CardEngine.

---

# Projektstatus

Aktueller Meilenstein:

✅ Milestone 0 – Projektstart abgeschlossen

Folgende Arbeiten wurden bereits erledigt:

- Git Repository erstellt
- Projektstruktur definiert
- Dokumentationsstruktur erstellt
- Entwicklungsregeln festgelegt
- Initial Commit durchgeführt

Die eigentliche Implementierung hat noch **nicht** begonnen.

---

# Ziel

ClubCMS ist ein eigenständiges WordPress-Plugin.

Es erweitert WordPress um eine flexible CardEngine.

ClubCMS ersetzt WordPress ausdrücklich nicht.

---

# Verantwortlichkeiten

## WordPress

WordPress bleibt verantwortlich für:

- Benutzer
- Rollen
- Rechte
- Login
- Medienverwaltung
- Datenbank
- Gutenberg
- REST API
- Themeverwaltung

---

## Theme

Das Theme übernimmt ausschließlich:

- Layout
- Header
- Footer
- Navigation
- Responsive Verhalten
- Farben
- Container
- Positionierung der Inhalte

Das Theme enthält keine Geschäftslogik.

---

## ClubCMS

ClubCMS übernimmt:

- CardPool
- CardEngine
- Kategorien
- Felddefinitionen
- Rendering
- Backend
- Frontend Editor
- Lebenszyklus
- Sichtbarkeit
- Sortierung
- Konfiguration

---

# Architekturprinzipien

- Plugin statt Child Theme
- Theme unabhängig
- Ein Editor für Backend und Frontend
- Ein zentraler CardPool
- Konfiguration vor Programmierung
- Dokumentation vor Implementierung
- Keine spontanen Architekturänderungen
- Immer vollständige Dateien
- Git First

---

# Entwicklungsregeln

Während Version 1.0 gilt:

- Änderungen erfolgen ausschließlich auf Basis der Dokumentation.
- Neue Ideen werden nicht sofort umgesetzt.
- Neue Ideen werden in `docs/14_Version_2_Ideen.md` dokumentiert.
- Version 1.0 bleibt architektonisch stabil.

---

# Coding

- PHP >= 8.2
- WordPress Coding Standards
- PSR-4 Autoloading
- Namespaces
- Composer

---

# Repository

```
wpClubCMS
│
├── docs
├── plugin
├── tests
├── examples
├── tools
└── ...
```

---

# Aktueller Arbeitsstand

Die Dokumentation befindet sich im Aufbau.

Die eigentliche Implementierung beginnt erst nach Abschluss der Architektur.

---

# Nächster Schritt

Erstellung des vollständigen Entwicklerhandbuchs.

Erst danach beginnt die Implementierung des Plugins.

---

# Grundsatz

ClubCMS wird wie ein professionelles Softwareprodukt entwickelt.

Die Dokumentation ist die verbindliche Grundlage des gesamten Projekts.
Test