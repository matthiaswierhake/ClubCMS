# ClubCMS
## Dokument 00 – Project Charter

**Projekt:** ClubCMS  
**Version:** 1.0  
**Status:** Entwurf  
**Dokument:** 00_Project_Charter.md

---

# 1. Ziel des Projekts

ClubCMS ist ein eigenständiges WordPress-Plugin zur Verwaltung beliebiger Inhalte auf Basis einer generischen CardEngine.

ClubCMS ist ausdrücklich **kein Theme**, **kein PageBuilder** und **kein Ersatz für WordPress**.

Es erweitert WordPress um ein flexibles Redaktionssystem, dessen Inhalte unabhängig vom verwendeten Theme verwaltet und dargestellt werden können.

---

# 2. Projektvision

Redakteure sollen Inhalte möglichst einfach pflegen können.

Designer sollen ausschließlich für das Seitenlayout verantwortlich sein.

Administratoren sollen das System konfigurieren können, ohne Programmcode ändern zu müssen.

Entwickler sollen das System erweitern können, ohne bestehende Module ändern zu müssen.

---

# 3. Projektziele

ClubCMS verfolgt folgende Ziele:

- Trennung von Inhalt und Darstellung
- Trennung von Theme und CMS
- nur ein Editor für Backend und Frontend
- ein zentrales Datenmodell
- möglichst wenig Programmcode für Erweiterungen
- möglichst viele Einstellungen über Konfiguration
- langfristige Wartbarkeit
- Wiederverwendbarkeit für andere Vereine

---

# 4. Nicht-Ziele

ClubCMS soll ausdrücklich **nicht** werden:

- kein eigenes WordPress
- kein Ersatz für Gutenberg
- kein Ersatz für Elementor
- kein Ersatz für Kadence
- keine Benutzerverwaltung
- keine Medienverwaltung
- kein Theme
- kein PageBuilder

WordPress bleibt die Grundlage.

---

# 5. Verantwortlichkeiten

## WordPress

WordPress übernimmt dauerhaft:

- Benutzerverwaltung
- Rollen
- Rechte
- Login
- Passwortverwaltung
- Medienverwaltung
- Uploads
- Datenbank
- Pluginverwaltung
- Themeverwaltung
- Gutenberg
- REST API

---

## Theme

Das Theme übernimmt ausschließlich:

- Layout
- Header
- Footer
- Navigation
- Farben
- Schriftarten
- Responsive Design
- Container
- Spalten
- Positionierung der Inhalte

Das Theme kennt keine Geschäftslogik.

---

## ClubCMS

ClubCMS übernimmt:

- CardEngine
- CardPool
- Kategorien
- Felddefinitionen
- Formulargenerator
- Backend-Dashboard
- Frontend-Editor
- Rendering
- Lebenszyklus einer Card
- Archiv
- Sichtbarkeit
- Sortierung
- Konfiguration
- Erweiterbarkeit

---

# 6. Grundprinzip

Es existiert nur ein zentraler Pool von Cards.

Alle Ausgaben entstehen ausschließlich durch Filtern und Rendern dieses Pools.

Es existieren keine getrennten News-, Termin- oder Flugbetriebssysteme.

News, Termine, Downloads usw. unterscheiden sich ausschließlich durch ihre Konfiguration.

---

# 7. Architekturprinzipien

ClubCMS basiert auf folgenden Grundsätzen:

- Eine Verantwortung pro Klasse
- Eine Implementierung pro Funktion
- Ein Editor für Backend und Frontend
- Keine doppelten Daten
- Keine Logik im Theme
- Keine Logik in Templates
- Keine Hardcodierung fachlicher Regeln
- Fachliche Regeln werden möglichst konfiguriert

---

# 8. Rendering

Die Ausgabe erfolgt unabhängig vom Theme.

Ein Theme entscheidet ausschließlich:

"An welcher Stelle sollen Inhalte erscheinen?"

ClubCMS entscheidet:

"Welche Inhalte erscheinen?"

Die Einbindung erfolgt über:

- Gutenberg Block
- Shortcode
- PHP API

---

# 9. Lebenszyklus

Jede Card besitzt einen Lebenszyklus.

Entwurf

↓

Veröffentlicht

↓

Archiviert

↓

Gelöscht (manuell)

Archivieren bedeutet niemals Löschen.

Gelöscht wird ausschließlich durch einen berechtigten Benutzer.

---

# 10. Zukunftssicherheit

Neue Funktionen sollen möglichst durch Konfiguration ergänzt werden.

Wenn Programmcode notwendig wird, muss dieser die bestehende Architektur respektieren.

Neue Ideen werden grundsätzlich zuerst als Erweiterung für Version 2.x betrachtet.

Version 1.0 bleibt architektonisch stabil.

---

# 11. Entwicklungsregeln

Während der Entwicklung gelten folgende Regeln:

- vollständige Dateien
- vollständige Dokumente
- WordPress Coding Standards
- klare Verantwortlichkeiten
- Dokumentation vor Implementierung
- keine spontanen Architekturänderungen
- Änderungen werden dokumentiert
- Git wird konsequent verwendet

---

# 12. Langfristiges Ziel

ClubCMS soll als eigenständiges Plugin entwickelt werden.

Es soll unabhängig vom verwendeten Theme funktionieren.

Es soll langfristig auch für andere Vereine und Organisationen einsetzbar sein.

Die Engine bleibt allgemein.

Der jeweilige Verein beschreibt sich ausschließlich über Konfiguration.