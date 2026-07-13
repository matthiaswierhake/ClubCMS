# ClubCMS

ClubCMS ist ein WordPress-Plugin für eine strukturierte Vereins- und Inhaltsverwaltung auf Basis des Kadence-Themes.

Die Landingpage orientiert sich an der Vorlage `LandingPage.png` und an der funktionalen Beschreibung aus dieser Datei.

## Ziel

Das Plugin soll Vereinsinhalte so darstellen und pflegen, dass:

- die Startseite ein klares, redaktionell steuerbares Layout erhält
- Inhalte in thematischen Cards und Spalten organisiert werden
- eingeloggte Nutzer direkt aus der Oberfläche bearbeiten können
- Redakteure nicht in das WordPress-Backend müssen
- Administratoren das Backend weiterhin nutzen können
- Kategorien und Formularfelder zentral definierbar sind

## Aufbau der Landingpage

Die gewünschte Struktur ist im Kern:

1. Menü
2. großes Bild / Hero-Bereich
3. Teaser-Zeile mit wichtigen Menüpunkten
4. eigentlicher ClubCMS-Inhaltsbereich
5. Kadence-Zeile mit 4 Spalten
6. pro Spalte eine Card mit Beiträgen eines Themas oder einer Kategorie

Die Beiträge innerhalb einer Card werden zunächst zeitlich sortiert angezeigt.

## Inhaltslogik

- Jede Spalte bildet ein Thema oder eine Kategorie ab.
- Die Card enthält mehrere Beiträge zu diesem Thema.
- Die Darstellung erfolgt für nicht eingeloggte Nutzer rein lesend.
- Für eingeloggte Nutzer werden Bearbeitungsaktionen eingeblendet.

Vorgesehene Aktionen:

- Löschen
- Bearbeiten
- Neuer Beitrag

Diese Aktionen sollen direkt in einen zentralen Editor führen, der von überall aufgerufen werden kann.

## Rechtekonzept

- Admin: darf das WordPress-Backend nutzen
- Redakteur: arbeitet über den ClubCMS-Editor, nicht über das Backend
- eingeloggte Nutzer: sehen je nach Berechtigung die Bearbeitungssymbole
- nicht eingeloggte Nutzer: sehen nur Inhalte

## Kategorien und Felder

Es soll einen Editor geben, über den Kategorien und die dazugehörigen Formularfelder definiert werden können.

Das Ziel ist, dass Inhalte nicht starr sind, sondern je Thema passend gepflegt werden können.

## Kurzcode-Prinzip

Per Shortcode werden Themen den Spalten zugeordnet.

Damit kann die Startseite oder ein Bereich der Seite flexibel gesteuert werden, ohne das Layout hart zu verdrahten.

Beispiel:

```text
[clubcms_landing_page spalte_1="cat-events" spalte_2="cat-news" spalte_3="cat-verein" spalte_4="cat-kontakt"]
```

Wichtig:

- Die Werte sind Kategorienamen oder Slugs aus ClubCMS.
- Nicht gesetzte Spalten werden automatisch mit den übrigen Kategorien gefüllt.
- Wenn eine Spalte bewusst leer bleiben soll, kann dort kein Wert gesetzt werden.

Wenn du nur eine einzelne Spalte für ein bestimmtes Thema einbinden willst, nutze den Einzel-Shortcode:

```text
[clubcms_column thema="cat-events"]
```

Auch hier gilt:

- `thema`, `kategorie`, `category`, `slug` oder `id` können verwendet werden
- es wird genau eine Card für dieses Thema gerendert
- das ist sinnvoll für modulare Seiten, Unterseiten oder individuelle Layouts

## Projektfortschritt und Roadmap

Der aktuelle Fortschritt, die Versionierung und die nächsten Schritte sind in der Roadmap dokumentiert:

- [ROADMAP.md](ROADMAP.md)

Diese Datei ist die zentrale Stelle für:

- aktuelle Version
- Status
- offene Aufgaben
- Meilensteine
- spätere Erweiterungen

## Nächster sinnvoller Schritt

Aus heutiger Sicht ist der nächste Schritt:

1. Architektur festziehen
2. Datenmodell definieren
3. Rollen- und Rechtekonzept präzisieren
4. erste Grundstruktur für Cards und Shortcodes umsetzen

## Hinweise zur Pflege

Wenn neue Anforderungen dazukommen, werden sie zuerst in der Roadmap ergänzt.

Die README bleibt die kompakte Projekteinführung, die Roadmap der laufende Arbeitsstand.
