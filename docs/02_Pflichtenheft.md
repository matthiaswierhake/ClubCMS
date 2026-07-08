# ClubCMS
## Dokument 02 – Pflichtenheft

**Projekt:** ClubCMS  
**Version:** 1.0  
**Status:** Entwurf  
**Dokument:** 02_Pflichtenheft.md

---

# 1. Ziel

Version 1.0 soll ein vollständig funktionsfähiges Redaktionssystem für strukturierte Inhalte bereitstellen.

ClubCMS erweitert WordPress, ersetzt WordPress jedoch nicht.

---

# 2. CardPool

Alle Inhalte werden als Cards gespeichert.

Es existiert nur ein zentraler CardPool.

News, Termine, Downloads usw. unterscheiden sich ausschließlich durch ihre Kategorie und Konfiguration.

---

# 3. Kategorien

Jede Card besitzt genau eine Kategorie.

Beispiele:

- News
- Termine
- Flugbetrieb
- Interessierte

Weitere Kategorien können später ergänzt werden.

Die Kategorien werden im Backend verwaltet.

---

# 4. Felddefinitionen

Jede Kategorie besitzt eine Felddefinition.

Die Felddefinition bestimmt,

- welche Felder angezeigt werden
- in welcher Reihenfolge
- welche Pflichtfelder existieren
- welche Feldtypen verwendet werden

Der Formulargenerator erstellt daraus automatisch den Editor.

---

# 5. Feldtypen Version 1.0

Folgende Feldtypen gehören zum Lieferumfang:

- Text
- Textarea
- RichText (WordPress Editor)
- Bild
- Galerie
- Link
- Datum
- Uhrzeit
- Zahl
- Checkbox
- Select
- Datei

Weitere Feldtypen können später ergänzt werden.

---

# 6. Card Editor

Es existiert genau ein Editor.

Der Editor wird verwendet

- im Backend
- im Frontend

Backend und Frontend benutzen dieselbe Formular-Engine.

---

# 7. Backend

Das Plugin besitzt ein eigenes Dashboard.

Dort können

- Cards angelegt
- Cards bearbeitet
- Cards archiviert
- Cards gelöscht
- Kategorien verwaltet
- Felddefinitionen verwaltet

werden.

---

# 8. Frontend

Berechtigte Benutzer können Cards direkt auf der Website bearbeiten.

Sie sehen zusätzlich

- Bearbeiten
- Neue Card
- Archivieren
- Löschen

Normale Besucher sehen diese Funktionen nicht.

---

# 9. Lebenszyklus

Jede Card besitzt einen Status.

Version 1.0 kennt

- Entwurf
- Veröffentlicht
- Archiviert

Gelöscht wird ausschließlich manuell.

Archivieren bedeutet niemals Löschen.

---

# 10. Sichtbarkeit

Jede Card besitzt eine Sichtbarkeit.

Version 1.0 kennt

- Öffentlich
- Mitglieder
- Redaktion

Weitere Sichtbarkeiten können später ergänzt werden.

---

# 11. Sortierung

Jede Kategorie besitzt eine Sortierregel.

Version 1.0 unterstützt

- Datum
- Manuell

Zusätzlich besitzt jede Card

- Statisch (Ja/Nein)
- Position

Statische Cards erscheinen immer an ihrer definierten Position.

Alle übrigen Cards werden nach der Sortierregel einsortiert.

---

# 12. Archiv

Archivierte Inhalte bleiben dauerhaft erhalten.

Das Archiv dient ausschließlich der Anzeige.

Ein Archiv verschiebt keine Daten.

---

# 13. Medien

Cards können enthalten:

- Beitragsbild
- Galerie
- Dateien

Es wird ausschließlich die WordPress-Mediathek verwendet.

---

# 14. Rendering

Cards können dargestellt werden

- als Gutenberg Block
- als Shortcode
- über PHP

Das verwendete Theme entscheidet ausschließlich über die Positionierung.

---

# 15. Responsive

Alle erzeugten HTML-Ausgaben müssen responsive nutzbar sein.

Die eigentliche Darstellung übernimmt das Theme.

---

# 16. Sicherheit

Alle Schreiboperationen müssen abgesichert sein.

Dazu gehören insbesondere

- Nonces
- Capability-Prüfung
- Escaping
- Validierung
- Sanitizing

---

# 17. Performance

Version 1.0 soll auch bei mehreren hundert Cards performant arbeiten.

WordPress-interne Mechanismen sollen bevorzugt verwendet werden.

---

# 18. Erweiterbarkeit

Die Architektur muss zukünftige Erweiterungen ermöglichen.

Insbesondere

- neue Feldtypen
- neue Kategorien
- neue Renderer
- neue Data Provider

dürfen keine Änderungen am Kernsystem erzwingen.

---

# 19. Version 1.0

Nicht Bestandteil der Version 1.0 sind

- Wetter-Provider
- RSS-Provider
- externe APIs
- Mehrsprachigkeit
- Workflow-System
- Versionierung von Cards
- Freigabeprozesse

Die Architektur wird jedoch so vorbereitet, dass diese Funktionen später ergänzt werden können.

---

# 20. Abnahmekriterien

Version 1.0 gilt als fertig, wenn

- Cards vollständig verwaltet werden können
- Backend und Frontend denselben Editor verwenden
- Kategorien konfigurierbar sind
- Felddefinitionen funktionieren
- Cards korrekt gerendert werden
- Sichtbarkeit funktioniert
- Archiv funktioniert
- Sortierung funktioniert
- Dokumentation vollständig ist
- Git Repository vollständig versioniert ist