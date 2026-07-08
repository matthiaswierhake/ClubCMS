# ClubCMS
## Dokument 01 – Vision

**Projekt:** ClubCMS  
**Version:** 1.0  
**Status:** Entwurf  
**Dokument:** 01_Vision.md

---

# 1. Vision

ClubCMS ist ein modernes, flexibles und leicht verständliches Redaktionssystem für WordPress.

Es richtet sich in erster Linie an Vereine, kleine Organisationen und Unternehmen, die ihre Inhalte strukturiert verwalten möchten, ohne sich mit der Komplexität des WordPress-Backends beschäftigen zu müssen.

ClubCMS erweitert WordPress um eine generische CardEngine, die Inhalte unabhängig vom verwendeten Theme verwalten und darstellen kann.

---

# 2. Leitidee

Der Redakteur soll sich ausschließlich auf Inhalte konzentrieren.

Der Designer gestaltet ausschließlich das Layout.

Der Administrator konfiguriert das System.

Der Entwickler erweitert die Engine.

Jede Rolle besitzt einen klar abgegrenzten Verantwortungsbereich.

---

# 3. Zielgruppe

ClubCMS richtet sich insbesondere an:

- Vereine
- Sportvereine
- Segelflugvereine
- Musikvereine
- Feuerwehren
- THW
- Schützenvereine
- Modellflugvereine
- kleine Unternehmen
- Organisationen

Grundsätzlich eignet sich ClubCMS für jede Website, deren Inhalte strukturiert verwaltet werden sollen.

---

# 4. Philosophie

ClubCMS verfolgt folgende Grundprinzipien:

## Inhalte statt Seiten

Redakteure bearbeiten keine Seiten.

Sie bearbeiten Inhalte.

Das Theme entscheidet später, wo diese Inhalte erscheinen.

---

## Ein Editor

Es existiert genau ein Editor.

Dieser wird sowohl im Backend als auch im Frontend verwendet.

Dadurch entsteht keine doppelte Programmierung.

---

## Ein Datenbestand

Alle Inhalte befinden sich in einem gemeinsamen CardPool.

Unterschiedliche Ansichten entstehen ausschließlich durch Filter und Renderer.

---

## Konfiguration statt Programmierung

Neue Kategorien

Neue Felddefinitionen

Neue Layouts

Neue Sortierungen

Neue Sichtbarkeiten

werden möglichst über Konfiguration realisiert.

Programmcode soll nur dann notwendig sein, wenn die Engine selbst erweitert wird.

---

# 5. Benutzerfreundlichkeit

Ein Redakteur soll nach kurzer Einweisung in der Lage sein,

- Inhalte anzulegen
- Inhalte zu bearbeiten
- Bilder hochzuladen
- Galerien anzulegen
- Inhalte zu archivieren
- Inhalte wiederherzustellen

ohne Kenntnisse von WordPress.

---

# 6. Flexibilität

ClubCMS soll nicht auf einen bestimmten Vereinstyp beschränkt sein.

Neue Kategorien können jederzeit ergänzt werden.

Neue Feldtypen können später erweitert werden.

Neue Renderer können unabhängig entwickelt werden.

---

# 7. Trennung von Inhalt und Layout

Das verwendete Theme bestimmt ausschließlich:

- Position der Inhalte
- Farben
- Schriften
- Abstände
- Container
- Responsive Verhalten

ClubCMS bestimmt ausschließlich:

- welche Inhalte existieren
- welche Inhalte sichtbar sind
- welche Inhalte gerendert werden

---

# 8. Zukunftssicherheit

Die Architektur wird so entwickelt, dass zukünftige Erweiterungen möglichst ohne Änderungen am Kernsystem möglich sind.

Neue Funktionen sollen bevorzugt über Konfiguration oder Erweiterungsmodule integriert werden.

---

# 9. Performance

ClubCMS soll auch bei einer großen Anzahl von Cards performant bleiben.

Dazu gehören unter anderem:

- effiziente Datenbankabfragen
- sinnvolle Caching-Strategien
- Lazy Loading von Medien
- minimierte JavaScript- und CSS-Dateien

---

# 10. Responsive Design

ClubCMS unterstützt vollständig responsive Websites.

Das Plugin erzeugt semantisch korrektes HTML.

Die endgültige Darstellung auf Desktop, Tablet und Smartphone erfolgt durch das verwendete Theme.

---

# 11. Barrierefreiheit

ClubCMS soll möglichst barrierearm entwickelt werden.

Dazu gehören:

- semantisches HTML
- Tastaturbedienbarkeit
- sinnvolle ARIA-Attribute
- ausreichende Kontraste (themeabhängig)
- Alternativtexte für Medien

---

# 12. Langfristiges Ziel

ClubCMS soll sich zu einer flexiblen Plattform entwickeln, die strukturierte Inhalte unabhängig vom verwendeten Theme verwaltet.

Dabei bleibt WordPress dauerhaft die technische Grundlage.

ClubCMS ersetzt WordPress nicht.

ClubCMS erweitert WordPress.