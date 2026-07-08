# ClubCMS
## Dokument 03 – Begriffserklärungen

**Projekt:** ClubCMS
**Version:** 1.0
**Status:** Entwurf
**Dokument:** 03_Begriffserklaerungen.md

---

# Zweck

Dieses Dokument definiert alle zentralen Begriffe von ClubCMS.

Jeder Begriff besitzt genau eine eindeutige Bedeutung.

---

# Card

Eine Card ist die kleinste eigenständige Inhaltseinheit des Systems.

Eine Card besitzt

- Felder
- Kategorie
- Sichtbarkeit
- Lebenszyklus
- Metadaten

Beispiele

- News
- Termin
- Veranstaltung
- Download
- Ansprechpartner

---

# CardPool

Der CardPool ist die Gesamtheit aller Cards.

Es existiert nur ein CardPool.

Alle Ansichten entstehen ausschließlich durch Filter.

---

# Kategorie

Eine Kategorie beschreibt den fachlichen Typ einer Card.

Beispiele

- News
- Termine
- Flugbetrieb
- Downloads

Die Kategorie definiert keine Logik.

Sie verweist auf Konfigurationen.

---

# Feld

Ein Feld speichert genau einen Wert.

Beispiele

Titel

Bild

Datum

URL

Checkbox

---

# Feldtyp

Ein Feldtyp beschreibt die technische Art eines Feldes.

Version 1.0

- Text
- Textarea
- RichText
- Bild
- Galerie
- Datei
- Datum
- Uhrzeit
- Zahl
- Checkbox
- Select

---

# Felddefinition

Eine Felddefinition beschreibt

- welche Felder existieren
- deren Reihenfolge
- Pflichtfelder
- Standardwerte
- Validierung

Aus der Felddefinition erzeugt ClubCMS automatisch den Editor.

---

# Editor

Der Editor ist die Oberfläche zum Bearbeiten einer Card.

Es existiert genau ein Editor.

Backend und Frontend verwenden denselben Editor.

---

# Renderer

Ein Renderer erzeugt HTML.

Er kennt keine Geschäftslogik.

Er erhält eine Card und erzeugt daraus die Ausgabe.

---

# Template

Ein Template beschreibt das HTML-Layout einer Card.

Templates können themespezifisch überschrieben werden.

---

# Layout

Ein Layout beschreibt die visuelle Darstellung einer Kategorie.

Layouts bestehen aus Templates und CSS.

---

# CardEngine

Die CardEngine ist der Kern von ClubCMS.

Sie verwaltet

- Cards
- Kategorien
- Felddefinitionen
- Rendering
- Lebenszyklus
- Sichtbarkeit

---

# Dashboard

Das Dashboard ist die Verwaltungsoberfläche von ClubCMS.

Es dient ausschließlich der Administration.

---

# Frontend Editor

Der Frontend Editor ist dieselbe Editor-Komponente wie im Backend.

Er wird direkt auf der Website geöffnet.

---

# Shortcode

Ein Shortcode bindet Inhalte an einer beliebigen Stelle der Website ein.

Beispiel

[clubcms category="news"]

---

# Gutenberg Block

Ein Gutenberg Block erfüllt dieselbe Aufgabe wie der Shortcode.

Er ermöglicht die Einbindung ohne Shortcode.

---

# Lebenszyklus

Der Lebenszyklus beschreibt den Status einer Card.

Version 1.0

- Entwurf
- Veröffentlicht
- Archiviert

Gelöscht wird ausschließlich manuell.

---

# Sichtbarkeit

Die Sichtbarkeit bestimmt, welche Benutzer eine Card sehen dürfen.

Version 1.0

- Öffentlich
- Mitglieder
- Redaktion

---

# Statische Card

Eine statische Card besitzt eine feste Position innerhalb einer Kategorie.

Sie wird unabhängig von der Standardsortierung angezeigt.

---

# Dynamische Card

Eine dynamische Card wird anhand der Sortierregel ihrer Kategorie einsortiert.

---

# Data Provider

Ein Data Provider liefert Daten.

Version 1.0

Nur manuelle Eingaben.

Spätere Versionen können

- Wetter
- RSS
- REST
- APIs

unterstützen.

---

# Theme

Das Theme bestimmt ausschließlich die Positionierung und Gestaltung der Inhalte.

Es besitzt keine fachliche Logik.

---

# WordPress

WordPress bildet die technische Plattform.

ClubCMS erweitert WordPress.

ClubCMS ersetzt WordPress nicht.