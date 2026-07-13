# ClubCMS Roadmap

Dokument fuer Fortschritt, Versionierung und naechste Schritte.

Dieses Dokument ist bewusst so aufgebaut, dass wir es laufend erweitern koennen:

- neue Punkte einfach unten ergaenzen
- erledigte Punkte mit einem gruenen Haken markieren
- offene Punkte mit einem roten Kreuz markieren
- Versionen als Meilensteine pflegen
- den aktuellen Status oben aktuell halten

## Aktueller Stand

- Projektstatus: Inhaltsverwaltung und Frontend-Editor in Aufbau
- Aktuelle Version: `v0.5.4`
- Naechster sinnvoller Meilenstein: `v0.5.5`

## Versionierungsmodell

Wir verwenden eine einfache semantische Versionierung fuer das Produkt:

- `v0.x.y` = Aufbauphase, Funktionen werden schrittweise ergaenzt
- `v1.0.0` = erster stabiler, nutzbarer Funktionsumfang
- `x` = groessere Ausbaustufe
- `y` = kleine Ergaenzungen, Korrekturen, Detailverbesserungen

Beispiel:

- `v0.1.0` = Grundstruktur vorhanden
- `v0.2.0` = erste Inhaltslogik und Rollenlogik
- `v0.2.1` = kleine Nacharbeiten ohne neue Kernfunktion
- `v0.2.2` = Wartung und Tooling-Verbesserungen
- `v0.2.3` = Bugfixes und Robustheit
- `v0.3.0` = Inhaltsverwaltung beginnt
- `v0.3.1` = Shortcode-Feinsteuerung fuer Spalten
- `v0.3.2` = Einzelspalten-Shortcode
- `v0.4.0` = Card-Workflow im Backend
- `v0.4.1` = Frontend-Aktionen fuer Cards verbunden
- `v0.4.2` = Backend-Lockdown fuer Nicht-Admins
- `v0.4.3` = Admin-Bar fuer Nicht-Admins ausgeblendet
- `v0.4.4` = Admin-Bar hart deaktiviert
- `v0.4.5` = Admin-Bar per Frontend-CSS entfernt
- `v0.4.6` = Admin-Bar wieder admin-spezifisch
- `v0.5.0` = Redaktions-Editor im Frontend gestartet
- `v0.5.1` = Editor-URL im Backend speicherbar
- `v0.5.2` = Ruecksprung nach Bearbeitung
- `v0.5.3` = Dashboard-Kachel fuer den Editor
- `v0.5.4` = Frontend-Fehlermeldungen im Editor

## Roadmap nach Versionen

### v0.1.0 - Projektgrundlage

Ziel: Das Plugin ist strukturell sauber aufgesetzt und die Basis ist klar.

Erledigt:

- ✅ Plugin-Struktur pruefen und festziehen
- ✅ Hauptmodule und Namenskonventionen definieren
- ✅ Inhalte und Layout-Anforderungen aus README und Vorlage festhalten
- ✅ Basis-Dokumentation anlegen
- ✅ Landingpage-Grundstruktur als Shortcode bereitstellen
- ✅ technische Architektur fuer Cards, Shortcodes und Editoren definieren

Offen:

- ❌ Rollenmodell fuer Admin / Redakteur / eingeloggte Nutzer festlegen

### v0.2.0 - Card-Grundlage

Ziel: Inhalte koennen als Cards gespeichert und auf der Landingpage angezeigt werden.

Erledigt:

- ✅ Card-Datenmodell ergaenzt
- ✅ Card-Persistenz in der WordPress-Optionsspeicherung ergaenzt
- ✅ Landingpage rendert Kategorien als Spalten
- ✅ Beitraege werden innerhalb der Spalten angezeigt
- ✅ Basis-Tests fuer Card-Modell und Rendering ergaenzt

Offen:

- ❌ echte Beitraegsverwaltung mit Anlegen/Bearbeiten/Loeschen
- ❌ Sortierung je Kategorie vollstaendig parametrierbar machen
- ❌ Sichtbarkeitslogik fuer eingeloggte Nutzer praezisieren

### v0.2.1 - Testseite verbessert

Ziel: Die ClubCMS-Testseite kann jeden Test einzeln starten.

Erledigt:

- ✅ Einzeltests auf der Diagnostics-Seite ergaenzt
- ✅ Test-Auswahl auf erlaubte Tests begrenzt
- ✅ uebersichtlicher Start-Button fuer alle Tests beibehalten

Offen:

- ❌ Testergebnisse pro Einzeltest historisieren
- ❌ Statusanzeige fuer letzte Ausfuehrung ergaenzen

### v0.2.2 - WordPress-Stubs ergaenzt

Ziel: IDE- und Analyse-Warnungen zu WordPress-Funktionen reduzieren.

Erledigt:

- ✅ WordPress-Stubs fuer globale Funktionen ergaenzt
- ✅ Dev-Autoload um Stubs erweitert
- ✅ Stubs in den direkten Testlauf eingebunden

Offen:

- ❌ falls noetig weitere WP-spezifische Helper ergaenzen
- ❌ Analysewarnungen im Editor gegenpruefen

### v0.2.3 - Testseite abgesichert

Ziel: Die Diagnostics-Seite darf nicht mehr fatale Fehler werfen, wenn Testklassen fehlen.

Erledigt:

- ✅ Testklassen nur nach dem Laden instanziiert
- ✅ fehlende Testdateien werden abgefangen
- ✅ Testseite bleibt auch bei unvollstaendiger Installation stabil

Offen:

- ❌ optional: Hinweise zur Testseite in der Admin-Doku ergaenzen

### v0.3.0 - Inhaltsverwaltung

Ziel: Inhalte koennen gepflegt und strukturiert zugeordnet werden.

Erledigt:

- ✅ Kategorie-/Themen-Editor als CRUD-Funktion ergaenzt
- ✅ Formularfelder je Kategorie definierbar gemacht
- ✅ Kategorien bearbeitbar und loeschbar gemacht
- ✅ Felddefinitionen bearbeitbar und loeschbar gemacht

Offen:

- ❌ Zuordnung von Themen zu Spalten per Shortcode
- ❌ Initiale Beitraegslogik fuer Cards
- ❌ Datenmodell fuer Beitraege und Kategorien konsolidieren

### v0.3.1 - Spaltenzuordnung per Shortcode

Ziel: Die Themen in den 4 Spalten koennen im Shortcode direkt festgelegt werden.

Erledigt:

- ✅ Shortcode nimmt explizite Spaltenzuordnung an
- ✅ nicht gesetzte Spalten fuellen sich automatisch mit verbleibenden Kategorien
- ✅ Shortcode-Dokumentation ergaenzt
- ✅ Test fuer die Spaltenzuordnung ergaenzt

Offen:

- ❌ Auswahl im Backend-Editor noch nicht vorhanden
- ❌ Validierung nicht vorhandener Kategorien im Frontend sichtbarer machen

### v0.3.2 - Einzelspalte als Shortcode

Ziel: Jedes Thema kann als einzelne Spalte per Shortcode eingebunden werden.

Erledigt:

- ✅ Einzelspalten-Shortcode ergaenzt
- ✅ Thema kann ueber ID oder Slug adressiert werden
- ✅ README um Einzelspalten-Beispiel ergaenzt
- ✅ Test fuer Einzelspalten-Rendering ergaenzt

Offen:

- ❌ Kurzcode-Generator im Backend fehlt noch
- ❌ sichtbare Fehlermeldung fuer unbekannte Themen fehlt noch

### v0.4.0 - Card-Workflow im Backend

Ziel: Cards koennen im Admin-Backend angelegt, bearbeitet und geloescht werden.

Erledigt:

- ✅ Card-Adminseite ergaenzt
- ✅ Card-Submission-Handler ergaenzt
- ✅ Card-Repositories um getById/delete erweitert
- ✅ Card-Formular mit Kategorie-, Status- und Sichtbarkeitsfeldern ergaenzt
- ✅ Testabdeckung fuer Card-Speicherung und -Loeschung ergaenzt

Offen:

- ❌ Rechtekonzept fuer Redakteure noch nicht umgesetzt
- ❌ Backend-Zugriff fuer Redakteure noch nicht gesperrt

### v0.4.1 - Frontend-Aktionen verbunden

Ziel: Die im Frontend sichtbaren Aktionen fuehren zu den Card-Editorseiten.

Erledigt:

- ✅ Neuer-Beitrag-Link pro Themenkarte ergaenzt
- ✅ Bearbeiten-Link pro Card ergaenzt
- ✅ Loeschen per Frontend-Formular an den Card-Editor angebunden
- ✅ Kategorie kann beim neuen Card-Entwurf vorgewaehlt werden

Offen:

- ❌ Redakteursrechte und zentraler Editor noch nicht umgesetzt
- ❌ Backend-Zugriff fuer Redakteure noch nicht gesperrt

### v0.4.2 - Backend gesperrt

Ziel: Nicht-Admins werden aus dem WordPress-Backend ferngehalten.

Erledigt:

- ✅ Admin-Access-Guard ergaenzt
- ✅ Non-Admin-Zugriffe auf wp-admin werden umgeleitet
- ✅ Ajax- und Admin-Post-Pfade bleiben nutzbar
- ✅ Testabdeckung fuer Redirect und Bypass ergaenzt

Offen:

- ❌ Rollenmodell fuer Redakteure und zentralen Editor noch nicht finalisiert

### v0.4.3 - Admin-Bar ausgeblendet

Ziel: Die WordPress-Zeile oben wird fuer Nicht-Admins auf der Website nicht angezeigt.

Erledigt:

- ✅ Admin-Bar-Guard ergaenzt
- ✅ Admin-Bar fuer Nicht-Admins ausgeblendet
- ✅ Testabdeckung fuer Admin- und Nicht-Admin-Fall ergaenzt

Offen:

- ❌ falls gewuenscht: Admin-Bar auch fuer Admins komplett deaktivieren

### v0.4.4 - Admin-Bar deaktiviert

Ziel: Die WordPress-Zeile oben wird auf der Website nicht mehr angezeigt.

Erledigt:

- ✅ show_admin_bar(false) im Plugin-Init gesetzt
- ✅ Filter bleibt als zusaetzliche Absicherung aktiv
- ✅ Testabdeckung vereinfacht und abgesichert

Offen:

- ❌ optional: auch andere WordPress-Frontend-Elemente gezielt ausblenden

### v0.4.5 - Admin-Bar entfernt

Ziel: Die WordPress-Leiste wird auch dann auf der Website ausgeblendet, wenn WordPress sie trotzdem rendert.

Erledigt:

- ✅ CSS zum Verstecken der Admin-Bar ergaenzt
- ✅ WordPress-Bump am Head entfernt
- ✅ Frontend-Test fuer CSS-Ausgabe ergaenzt

Offen:

- ❌ falls noetig weitere Theme-/Plugin-Elemente verstecken

### v0.4.6 - Admin-Bar wieder admin-spezifisch

Ziel: Admins sehen die WordPress-Leiste wieder, Nicht-Admins nicht.

Erledigt:

- ✅ Admin-Bar-Filter wieder auf Rollenpruefung umgestellt
- ✅ Frontend-CSS nur noch fuer Nicht-Admins ausgegeben
- ✅ Admins behalten die WordPress-Leiste

Offen:

- ❌ falls gewuenscht: Admin-Bar fuer alle komplett deaktivieren

### v0.5.0 - Redaktions-Editor im Frontend

Ziel: Redakteure arbeiten nicht mehr im WordPress-Backend, sondern im zentralen ClubCMS-Editor.

Erledigt:

- ✅ Rollen- und Zugriffspruefung fuer den Editor ergaenzt
- ✅ Shortcode `clubcms_editor` fuer den Frontend-Editor umgesetzt
- ✅ Landingpage kann mit `editor_url` auf den Frontend-Editor verlinken
- ✅ Tests fuer Editor-Zugriff, Editor-Rendern und Editor-Speichern ergaenzt
- ✅ Version auf `v0.5.0` angehoben

Offen:

- ❌ Feinschliff am Rollenmodell
- ❌ Editor-Zielseite ggf. als Einstellung im Backend speichern
- ❌ weitere Redaktionshilfen und Validierungen

### v0.5.1 - Editor-URL im Backend gespeichert

Ziel: Die Editor-Zielseite kann zentral im Backend gepflegt werden.

Erledigt:

- ✅ Einstellungen-Seite fuer die Editor-URL ergaenzt
- ✅ Editor-URL wird gespeichert und von der Landingpage verwendet
- ✅ Landingpage bleibt per Shortcode weiterhin uebersteuerbar
- ✅ Tests fuer Settings, Shortcode und Speicherung ergaenzt
- ✅ Version auf `v0.5.1` angehoben

Offen:

- ❌ klare Ruecksprung-Logik nach Bearbeitung
- ❌ sichtbare Fehlermeldungen fuer Eingabeprobleme
- ❌ bessere UI fuer Redakteure
- ❌ Vorlagen und Schnellaktionen

### v0.5.2 - Ruecksprung nach Bearbeitung

Ziel: Nach dem Bearbeiten springt der Frontend-Editor sauber zur Ausgangsseite zurueck.

Erledigt:

- ? klare Ruecksprung-Logik nach Bearbeitung
- ? `back_to` wird ueber Landingpage und Editor durchgereicht
- ? Tests fuer den Ruecksprungpfad ergaenzt
- ? Version auf `v0.5.2` angehoben

Offen:

- ? bessere Eingabevalidierung
- ? sichtbare Fehlermeldungen fuer Eingabeprobleme
- ? bessere UI fuer Redakteure
- ? Vorlagen und Schnellaktionen

### v0.5.3 - Dashboard-Kachel fuer den Editor

Ziel: Im ClubCMS-Dashboard gibt es einen direkten Schnellzugriff auf den Frontend-Editor.

Erledigt:

- ✅ Dashboard-Kachel fuer den Frontend-Editor ergaenzt
- ✅ Kachel verlinkt auf die gespeicherte Editor-URL
- ✅ Testabdeckung fuer die Kachel ergaenzt
- ✅ Version auf `v0.5.3` angehoben

Offen:

- ❌ Vorlagen fuer neue Beitraege
- ❌ bessere Eingabevalidierung
- ❌ schnellere Redaktionsaktionen

### v0.5.4 - Frontend-Fehlermeldungen im Editor

Ziel: Der Frontend-Editor zeigt Eingabeprobleme direkt sichtbar an.

Erledigt:

- ✅ Fehlermeldungen aus der Card-Validierung werden im Frontend angezeigt
- ✅ Invalides JSON und fehlende Pflichtangaben werden sichtbar rueckgemeldet
- ✅ Testabdeckung fuer Fehlermeldungen ergaenzt
- ✅ Version auf `v0.5.4` angehoben

Offen:

- ❌ Vorlagen fuer neue Beitraege
- ❌ bessere Eingabevalidierung
- ❌ schnellere Redaktionsaktionen

### v0.5.5 - Editor-Feinschliff

Ziel: Der Frontend-Editor wird im Alltag angenehmer nutzbar.

Erledigt:

- ❌ Vorlagen fuer neue Beitraege
- ❌ bessere Eingabevalidierung
- ❌ schnellere Redaktionsaktionen

Offen:

- ❌ Vorlagen fuer neue Beitraege
- ❌ bessere Eingabevalidierung
- ❌ schnellere Redaktionsaktionen

## Fortschrittspflege

Wenn wir weiterarbeiten, wird dieser Block jeweils aktualisiert:

- Version: `v0.5.4`
- Letzte Aenderung: 2026-07-13
- Naechster Fokus: Vorlagen und weitere Redaktionshilfen

## Aenderungslog

### v0.5.0 - Redaktions-Editor im Frontend gestartet

- Shortcode `clubcms_editor` eingefuehrt
- EditorAccessGuard fuer Redakteure und Administratoren eingefuehrt
- Landingpage kann auf eine Frontend-Editor-URL zeigen
- Tests fuer Editor, Zugriff und Editor-Links ergaenzt

### v0.5.1 - Editor-URL im Backend gespeichert

- Einstellungen-Seite fuer die Editor-URL eingefuehrt
- Landingpage nutzt die gespeicherte Editor-URL automatisch
- Tests fuer das Speichern und Lesen der URL ergaenzt

### v0.5.2 - Ruecksprung nach Bearbeitung

- `back_to` fuer Landingpage und Editor eingefuehrt
- Editor springt nach dem Speichern zur Ausgangsseite zurueck
- Tests fuer Redirect und Link-Persistenz ergaenzt

### v0.5.3 - Dashboard-Kachel fuer den Editor

- Dashboard-Kachel als Schnellzugriff auf den Frontend-Editor eingefuehrt
- Kachel nutzt die gespeicherte Editor-URL
- Test fuer die Kachel ergaenzt

### v0.5.4 - Frontend-Fehlermeldungen im Editor

- Fehler aus der Card-Validierung werden im Frontend angezeigt
- Test fuer die Fehlermeldung ergaenzt

### v0.4.6 - Admin-Bar wieder admin-spezifisch

- Admins sehen die Leiste wieder
- Nicht-Admins bleiben verborgen

### v0.4.5 - Admin-Bar entfernt

- CSS-Hide fuer `#wpadminbar`
- `_admin_bar_bump_cb` entfernt

### v0.4.4 - Admin-Bar deaktiviert

- Admin-Bar auf der Website dauerhaft deaktiviert

### v0.4.3 - Admin-Bar ausgeblendet

- Admin-Bar fuer Nicht-Admins verborgen

### v0.4.2 - Backend gesperrt

- Admin-Access-Guard eingefuehrt
- Nicht-Admins werden aus wp-admin umgeleitet

### v0.4.1 - Frontend-Aktionen verbunden

- Frontend-Icons auf Cards an die Card-Verwaltung angebunden
- New/Edit/Delete-Linklogik ergaenzt

### v0.4.0 - Card-Workflow im Backend

- Cards im Backend verwaltbar gemacht
- Card-Adminseite eingefuehrt
- Card-Handler und Tests ergaenzt

### v0.3.2 - Einzelspalten-Shortcode

- neuer Shortcode `clubcms_column`
- einzelne Themenkarte direkt einbindbar

### v0.3.1 - Spaltenzuordnung per Shortcode

- Shortcode um `spalte_1` bis `spalte_4` erweitert
- README um Beispiel ergaenzt
- Landingpage-Shortcode-Test ergaenzt

### v0.3.0 - Inhaltsverwaltung gestartet

- Kategorie- und Felddefinitions-Editor erweitert
- Bearbeiten und Loeschen fuer Kategorien und Felddefinitionen ergaenzt
- Repositories um CRUD-Methoden erweitert
- Submission-Handler auf Update/Delete vorbereitet

### v0.2.3 - Testseite abgesichert

- Testseite gegen fehlende Testklassen abgesichert
- Diagnostics-Seite stabilisiert

### v0.2.2 - WordPress-Stubs ergaenzt

- WordPress-Stubs fuer IDE und lokale Entwicklung ergaenzt

### v0.2.1 - Testseite verbessert

- Testseite um Einzelstart pro Test erweitert

### v0.2.0 - Cards und Anzeige

- Roadmap angelegt
- Versionierungsmodell definiert
- Meilensteine fuer den Produktaufbau festgehalten
- README ueberarbeitet
- Landingpage-Grundstruktur als renderbarer Shortcode begonnen
- Testabdeckung fuer den ersten Frontend-Baustein ergaenzt
- Card-Modell und Repository ergaenzt
- Renderer liest Cards und Kategorien
- Tests fuer Card- und Landingpage-Ausgabe ergaenzt

## Offene Ideen fuer spaetere Ergaenzungen

- Vorschau-Modus fuer Redakteure
- Drag-and-drop fuer Karten und Themen
- Archivbereich fuer alte Beitraege
- Medienverwaltung pro Card
- automatische Startseiten-Teaser aus Kategorien
- Protokollierung von Aenderungen

