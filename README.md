<img src="src/Resources/config/plugin.png" alt="TM FAQ Pro" width="64" height="64">

# TM FAQ Pro

FAQ-Verwaltung für Shopware 6.7 mit gezielter Ausgabe auf Produktseiten, Kategorieseiten und in Erlebniswelten.

[Plugin-ZIP herunterladen](https://github.com/tuami/TuamiFaqPro/releases/latest/download/TuamiFaqPro.zip) · [Änderungen](CHANGELOG.md)

## Funktionen

- Mehrsprachige FAQ-Gruppen, Fragen und Antworten
- Automatische Ausgabe auf zugeordneten Produkt- und Kategorieseiten
- Zuordnung über einzelne Produkte, Kategorien, dynamische Produktgruppen oder Schlüsselwörter
- Einschränkung nach Verkaufskanal und Rule-Builder-Regel
- Manuelle Platzierung über das Erlebniswelten-Element **TM FAQ Pro**
- Eigene Überschrift pro Erlebniswelten-Element
- Sortierung von Gruppen und Fragen
- Aktivieren und Deaktivieren einzelner Gruppen und Fragen
- Strukturierte FAQPage-Daten als JSON-LD
- Optionaler KI-Feed unter `/faq-ai.txt`
- Barrierearm bedienbare FAQ-Akkordeons

## Gestaltung

Die Darstellung wird in der Plugin-Konfiguration allgemein für den jeweiligen Verkaufskanal eingestellt.

### Layout

- Darstellung als Karten oder als flache Liste mit individuell einstellbaren Trennlinien
- Standardbreite von 960 Pixeln
- frei einstellbare maximale Breite
- volle verfügbare Breite
- Abstand zwischen den Karten
- Eckenradius der FAQ-Karten

### Farben

- Hintergrundfarbe für Fragen und Antworten ein- oder ausschalten
- eigene Hintergrundfarbe für Karten
- frei wählbare Farbe der Trennlinien
- Farbe der geöffneten Frage aus der Bootstrap-Primärfarbe
- Farbe der geöffneten Frage aus der Bootstrap-Sekundärfarbe
- frei wählbare Farbe für die geöffnete Frage
- frei wählbare Textfarbe der geöffneten Frage

### Verhalten

- erste Frage beim Laden automatisch öffnen oder geschlossen lassen
- automatische Ausgabe auf Produktseiten separat aktivieren oder deaktivieren
- automatische Ausgabe auf Kategorieseiten separat aktivieren oder deaktivieren
- JSON-LD und KI-Feed separat aktivieren oder deaktivieren

## Voraussetzungen

- Shopware 6.7
- PHP gemäß der verwendeten Shopware-Version

## Installation

[TuamiFaqPro.zip aus dem neuesten Release herunterladen](https://github.com/tuami/TuamiFaqPro/releases/latest/download/TuamiFaqPro.zip)

1. Die heruntergeladene TuamiFaqPro.zip unter **Erweiterungen > Meine Erweiterungen** hochladen.
2. Plugin installieren und aktivieren oder eine bestehende Installation aktualisieren.
3. Shopware-Cache leeren und die Administration neu laden.
4. Das Storefront-Theme kompilieren.

Für die Installation die oben verlinkte Plugin-ZIP verwenden, nicht GitHubs „Source code“-Archiv. Der technische Pluginname bleibt `TuamiFaqPro`; beim Update auf TM FAQ Pro bleiben die bisherigen Daten und Einstellungen erhalten.

## Verwendung

1. Unter **Kataloge > TM FAQ Pro > Gruppen** eine Gruppe anlegen.
2. Der Gruppe Produkte, Kategorien, dynamische Produktgruppen oder Schlüsselwörter zuweisen.
3. Unter **FAQs** Fragen und Antworten anlegen und einer Gruppe zuordnen.
4. Optional Verkaufskanäle und eine Rule-Builder-Regel festlegen.
5. Darstellung und Verhalten in der Plugin-Konfiguration einstellen.

Eine Gruppe wird auf Produkt- oder Kategorieseiten nur automatisch angezeigt, wenn eine passende Zuordnung vorhanden ist. Gruppen ohne Zuordnung können weiterhin über das Erlebniswelten-Element **TM FAQ Pro** ausgegeben werden.

Dynamische Produktgruppen gelten für Produktseiten. Für die automatische Ausgabe auf Kategorieseiten muss die Kategorie direkt in der FAQ-Gruppe ausgewählt werden.

Kategoriezuordnungen gelten ausschließlich für die direkt ausgewählten Kategorieseiten, nicht für Unterkategorien oder deren Produkte. Auf Produktseiten werden bestimmte Produkte, dynamische Produktgruppen und Schlüsselwörter mit **ODER** verknüpft. Der Hauptschalter „FAQ-Ausgabe aktivieren“ gilt auch für die Erlebniswelten-Ausgabe.

### Wo werden die FAQs angezeigt?

| Zuordnung | Automatische Ausgabe |
| --- | --- |
| Kategorie | Nur auf der ausgewählten Kategorieseite, nicht auf Unterkategorien oder Artikeln |
| Hauptprodukt | Auf dem Produkt und seinen Varianten |
| Einzelne Variante | Nur auf dieser Variante |
| Dynamische Produktgruppe | Auf passenden Produktseiten |
| Produkt-Schlüsselwörter | Auf Produktseiten mit passendem Namen, Beschreibung oder Produktnummer |
| Keine Zuordnung | Nur durch manuelle Platzierung in den Erlebniswelten |

Mehrere Produktzuordnungen werden mit **ODER** verknüpft. Verkaufskanal, aktive Gruppe und eine gegebenenfalls hinterlegte Regel müssen ebenfalls passen.

## Lizenz

TM FAQ Pro darf kostenlos in privaten und gewerblichen Shops verwendet, angepasst und kostenlos weitergegeben werden. Der Verkauf des Plugins und die Aufnahme in kostenpflichtige Plugin-Pakete sind nicht erlaubt. Kostenpflichtige Installation, Anpassung und Support bleiben zulässig.

Es gilt die [Community License 1.0](LICENSE). Diese ist eine Source-Available-Lizenz und keine OSI-anerkannte Open-Source-Lizenz.
