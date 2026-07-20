# TUAMI FAQ Pro

FAQ-Verwaltung für Shopware 6.7 mit gezielter Ausgabe auf Produktseiten, Kategorieseiten und in Erlebniswelten.

## Funktionen

- Mehrsprachige FAQ-Gruppen, Fragen und Antworten
- Automatische Ausgabe auf zugeordneten Produkt- und Kategorieseiten
- Zuordnung über einzelne Produkte, Kategorien, dynamische Produktgruppen oder Schlüsselwörter
- Einschränkung nach Verkaufskanal und Rule-Builder-Regel
- Manuelle Platzierung über das Erlebniswelten-Element **FAQ Pro**
- Eigene Überschrift pro Erlebniswelten-Element
- Sortierung von Gruppen und Fragen
- Aktivieren und Deaktivieren einzelner Gruppen und Fragen
- Strukturierte FAQPage-Daten als JSON-LD
- Optionaler KI-Feed unter `/faq-ai.txt`
- Barrierearm bedienbare FAQ-Akkordeons

## Gestaltung

Die Darstellung wird in der Plugin-Konfiguration allgemein für den jeweiligen Verkaufskanal eingestellt.

### Layout

- Standardbreite von 960 Pixeln
- frei einstellbare maximale Breite
- volle verfügbare Breite
- Abstand zwischen den Fragen
- Eckenradius der FAQ-Karten

### Farben

- Hintergrundfarbe für Fragen und Antworten ein- oder ausschalten
- eigene Hintergrundfarbe
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

[TuamiFaqPro.zip herunterladen](https://github.com/tuami/TuamiFaqPro/raw/refs/heads/main/dist/TuamiFaqPro.zip)

1. Die heruntergeladene TuamiFaqPro.zip unter **Erweiterungen > Meine Erweiterungen** hochladen.
2. Plugin installieren und aktivieren.
3. Shopware-Cache leeren und die Administration neu laden.
4. Das Storefront-Theme kompilieren.

## Verwendung

1. Unter **Kataloge > FAQ Pro > Gruppen** eine Gruppe anlegen.
2. Der Gruppe Produkte, Kategorien, dynamische Produktgruppen oder Schlüsselwörter zuweisen.
3. Unter **FAQs** Fragen und Antworten anlegen und einer Gruppe zuordnen.
4. Optional Verkaufskanäle und eine Rule-Builder-Regel festlegen.
5. Darstellung und Verhalten in der Plugin-Konfiguration einstellen.

Eine Gruppe wird auf Produkt- oder Kategorieseiten nur automatisch angezeigt, wenn eine passende Zuordnung vorhanden ist. Gruppen ohne Zuordnung können weiterhin über das Erlebniswelten-Element **FAQ Pro** ausgegeben werden.

Dynamische Produktgruppen gelten für Produktseiten. Für die automatische Ausgabe auf Kategorieseiten muss die Kategorie direkt in der FAQ-Gruppe ausgewählt werden.

Produkte, Kategorien, dynamische Produktgruppen und Schlüsselwörter innerhalb einer Gruppe werden mit **ODER** verknüpft. Das Produkt muss daher nur eine der Zuordnungen erfüllen.

## Lizenz

TUAMI FAQ Pro darf kostenlos in privaten und gewerblichen Shops verwendet, angepasst und kostenlos weitergegeben werden. Der Verkauf des Plugins und die Aufnahme in kostenpflichtige Plugin-Pakete sind nicht erlaubt. Kostenpflichtige Installation, Anpassung und Support bleiben zulässig.

Es gilt die [Community License 1.0](LICENSE). Diese ist eine Source-Available-Lizenz und keine OSI-anerkannte Open-Source-Lizenz.