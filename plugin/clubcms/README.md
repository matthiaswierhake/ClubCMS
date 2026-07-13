# ClubCMS

ClubCMS ist ein WordPress-Plugin fuer eine strukturierte Vereins- und Inhaltsverwaltung auf Basis des Kadence-Themes.

Die Landingpage orientiert sich an der Vorlage `LandingPage.png` und an der funktionalen Beschreibung aus dieser Datei.

## Ziel

Das Plugin soll Vereinsinhalte so darstellen und pflegen, dass:

- die Startseite ein klares, redaktionell steuerbares Layout erhaelt
- Inhalte in thematischen Cards und Spalten organisiert werden
- eingeloggte Nutzer direkt aus der Oberflaeche bearbeiten koennen
- Redakteure nicht in das WordPress-Backend muessen
- Administratoren das Backend weiterhin nutzen koennen
- Kategorien und Formularfelder zentral definierbar sind

## Aufbau der Landingpage

Die gewuenschte Struktur ist im Kern:

1. Menue
2. grosses Bild / Hero-Bereich
3. Teaser-Zeile mit wichtigen Menuepunkten
4. eigentlicher ClubCMS-Inhaltsbereich
5. Kadence-Zeile mit 4 Spalten
6. pro Spalte eine Card mit Beitraegen eines Themas oder einer Kategorie

Die Beitraege innerhalb einer Card werden je Kategorie nach der dort konfigurierten Sortierung angezeigt.

## Inhaltslogik

- Jede Spalte bildet ein Thema oder eine Kategorie ab.
- Die Card enthaelt mehrere Beitraege zu diesem Thema.
- Die Sortierung pro Kategorie kann im Backend festgelegt werden.
- Public-Cards sind fuer alle sichtbar.
- Cards mit `members` sind nur fuer eingeloggte Nutzer sichtbar.
- Cards mit `editorial` sind fuer Redakteure und Administratoren sichtbar.
- Fuer eingeloggte Nutzer werden Bearbeitungsaktionen eingeblendet.

Vorgesehene Aktionen:

- Loeschen
- Bearbeiten
- Neuer Beitrag

Diese Aktionen koennen direkt in einen zentralen Editor fuehren, der von ueberall aufgerufen werden kann.

## Rechtekonzept

- Admin: darf das WordPress-Backend nutzen
- Redakteur: arbeitet ueber den ClubCMS-Editor, nicht ueber das Backend
- eingeloggte Nutzer: sehen die Bearbeitungssymbole auf der Landingpage
- nicht eingeloggte Nutzer: sehen nur Inhalte

Intern gilt dabei die einfache Trennung:

- `manage_options` = Admin
- `edit_posts` = Redakteur
- eingeloggt = darf die Frontend-Bearbeitungssymbole sehen

## Kategorien und Felder

Es gibt einen Editor, ueber den Kategorien und die dazugehoerigen Formularfelder definiert werden koennen.

Das Ziel ist, dass Inhalte nicht starr sind, sondern je Thema passend gepflegt werden koennen.

## Kurzcode-Prinzip

Per Shortcode werden Themen den Spalten zugeordnet.

Damit kann die Startseite oder ein Bereich der Seite flexibel gesteuert werden, ohne das Layout hart zu verdrahten.

Beispiel:

```text
[clubcms_landing_page spalte_1="cat-events" spalte_2="cat-news" spalte_3="cat-verein" spalte_4="cat-kontakt"]
```

Wichtig:

- Die Werte sind Kategorienamen oder Slugs aus ClubCMS.
- Nicht gesetzte Spalten werden automatisch mit den uebrigen Kategorien gefuellt.
- Wenn eine Spalte bewusst leer bleiben soll, kann dort kein Wert gesetzt werden.

Wenn du nur eine einzelne Spalte fuer ein bestimmtes Thema einbinden willst, nutze den Einzel-Shortcode:

```text
[clubcms_column thema="cat-events"]
```

Auch hier gilt:

- `thema`, `kategorie`, `category`, `slug` oder `id` koennen verwendet werden
- es wird genau eine Card fuer dieses Thema gerendert
- das ist sinnvoll fuer modulare Seiten, Unterseiten oder individuelle Layouts

## Zentraler Editor im Frontend

Redakteure sollen nicht ins WordPress-Backend muessen. Dafuer gibt es den zentralen Frontend-Editor:

```text
[clubcms_editor]
```

Der Editor zeigt:

- vorhandene Cards
- Bearbeiten
- Duplizieren
- Loeschen
- neue Cards anlegen

Wichtige Punkte:

- Zugriff haben Redakteure mit `edit_posts` und Administratoren mit `manage_options`
- das Formular arbeitet direkt im Frontend
- das Backend bleibt fuer Nicht-Admins gesperrt

Der Einstieg in den Editor ist an mehreren Stellen vorgesehen:

- ueber die Dashboard-Kachel im ClubCMS-Backend
- ueber die Bearbeiten- und Neu-Links auf der Landingpage
- optional mit einer zentral gespeicherten `editor_url`

Damit die Aktionen auf der Landingpage zum Frontend-Editor fuehren, kann beim Landingpage-Shortcode die Ziel-URL angegeben werden:

```text
[clubcms_landing_page editor_url="https://example.test/clubcms-editor/"]
```

Dann zeigen Bearbeiten- und Neu-Links direkt auf die Frontend-Seite mit `[clubcms_editor]`.

## Editor-URL im Backend speichern

Wenn du die Zielseite lieber zentral im Backend pflegen willst, gibt es dafuer den Menüpunkt:

```text
ClubCMS -> Einstellungen
```

Dort kannst du die Editor-URL eintragen, zum Beispiel:

```text
/clubcms-editor/
```

Oder als volle URL:

```text
https://example.test/clubcms-editor/
```

Diese Einstellung wird von der Landingpage automatisch verwendet, solange im Shortcode keine eigene `editor_url` gesetzt ist.

Wenn du von der Landingpage in den Editor gehst, nimmt ClubCMS die Ursprungseite mit und springt nach dem Speichern dorthin zurueck.

Auch die Dashboard-Kachel fuehrt direkt in diesen Editor, damit Redakteure ohne Umweg starten koennen.

Im Frontend-Editor gibt es Vorlagen fuer neue Beitraege wie Standard, News und Veranstaltung.

Zusaetzlich koennen bestehende Cards direkt dupliziert werden, damit neue Entwuerfe schneller entstehen.

Der Editor zeigt ausserdem eine einfache Vorschau der Card, damit du Titel, Kategorie, Status und Sichtbarkeit sofort einordnen kannst.

Wenn eine Eingabe fehlschlaegt, bleiben die zuletzt eingegebenen Werte im Formular erhalten, damit du nicht neu anfangen musst.

Im Editor gibt es auch einen Reset-Link, mit dem du sofort wieder eine neue Card ohne Bearbeitungs- oder Vorlagenkontext startest.

Wenn bei der Eingabe etwas nicht passt, zeigt der Editor eine direkte Fehlermeldung im Frontend an.

## Projektfortschritt und Roadmap

Der aktuelle Fortschritt, die Versionierung und die naechsten Schritte sind in der Roadmap dokumentiert:

- [ROADMAP.md](ROADMAP.md)

Diese Datei ist die zentrale Stelle fuer:

- aktuelle Version
- Status
- offene Aufgaben
- Meilensteine
- spaetere Erweiterungen

## Naechster sinnvoller Schritt

Aus heutiger Sicht ist der naechste Schritt:

1. Feinschliff am Rollenmodell
2. noch bessere Verknuepfung von Landingpage und Editor
3. weitere Redaktionswerkzeuge
4. Sonderfaelle und Validierung erweitern

## Hinweise zur Pflege

Wenn neue Anforderungen dazukommen, werden sie zuerst in der Roadmap ergaenzt.

Die README bleibt die kompakte Projekteinfuehrung, die Roadmap der laufende Arbeitsstand.

