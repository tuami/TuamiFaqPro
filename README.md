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
- Eigener Überschriftentext pro Erlebniswelt-Element, wahlweise zentriert, linksbündig oder ausgeblendet
- Globale Überschriften-Darstellung pro Verkaufskanal mit optionaler Überschreibung je Erlebniswelt-Element
- Sortierung von Gruppen und Fragen
- Aktivieren und Deaktivieren einzelner Gruppen und Fragen
- Strukturierte FAQPage-Daten als JSON-LD
- Optionaler KI-Feed unter `/faq-ai.txt`
- Barrierearm bedienbare FAQ-Akkordeons
- Sanfte Auf-/Zuklapp- und Plus-/Minus-Animation ohne zusätzliches JavaScript, mit Rücksicht auf reduzierte Bewegung

### Neu in 1.1.5

Überschriften lassen sich jetzt gezielt ausrichten oder ausblenden – global oder je Erlebniswelt-Element. Das Auf- und Zuklappen wirkt durch weichere Übergänge und ein animiertes Plus/Minus ruhiger. Außerdem wurden Cache-Aktualisierung, das Laden passender FAQs, Übersetzungen und die Deinstallation verbessert.

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
- Überschrift zentriert, linksbündig oder ausgeblendet; im Erlebniswelt-Element überschreibbar
- sanftes Auf- und Zuklappen mit animiertem Plus/Minus; berücksichtigt reduzierte Bewegung
- automatische Ausgabe auf Produktseiten separat aktivieren oder deaktivieren
- automatische Ausgabe auf Kategorieseiten separat aktivieren oder deaktivieren
- JSON-LD und KI-Feed separat aktivieren oder deaktivieren

## Voraussetzungen

- Shopware 6.7
- PHP gemäß der verwendeten Shopware-Version

Version 1.1.5 berücksichtigt die Schnittstellenänderungen bis Shopware 6.7.15.1. Vor dem Live-Update bitte in einer Testumgebung prüfen; Shopware 6.8 ist nicht freigegeben.

## Installation

Aktuelle Version: **1.1.5**.

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

## Darstellung der Überschrift

Unter **Plugin-Konfiguration → Verhalten → Darstellung der Überschrift** stehen **Zentriert**, **Linksbündig** und **Ausblenden** zur Verfügung. Standard bleibt zentriert. Die Einstellung gilt für die automatische Ausgabe auf Produkt- und Kategorieseiten sowie für Erlebniswelt-Elemente ohne eigene Vorgabe.

### Im Erlebniswelt-Element überschreiben

1. Das Element **TM FAQ Pro** in der Erlebniswelt öffnen und die gewünschte FAQ-Gruppe auswählen.
2. Optional einen eigenen **Überschriftentext** eingeben. Ein leeres Textfeld übernimmt die Plugin-Überschrift; ist auch diese leer, wird die übersetzte Standardüberschrift verwendet.
3. Unter **Darstellung der Überschrift** eine Einstellung wählen:
   - **Plugin-Einstellung übernehmen**: verwendet die Vorgabe des jeweiligen Verkaufskanals. Dies ist der Standard für bestehende und neue Elemente.
   - **Zentriert** oder **Linksbündig**: zeigt die Überschrift in dieser Ausrichtung – auch wenn sie global ausgeblendet ist.
   - **Ausblenden**: zeigt für dieses Element keine Überschrift, unabhängig von der globalen Vorgabe.
4. Die Erlebniswelt speichern.

Beim Ausblenden bleiben der Überschriftentext, die Fragen und Antworten erhalten. Es bleibt kein leerer Überschriftenabstand zurück. **Leeres Textfeld bedeutet übernehmen, nicht ausblenden.** Layout und Farben werden weiterhin über die Plugin-Konfiguration gesteuert.

### Animation und Bedienung

Der Antwortbereich öffnet weich und schließt etwas schneller. Der senkrechte Balken des Pluszeichens zieht sich beim Öffnen zum Minus zurück. Die Darstellung funktioniert mit Karten und Trennlinien; eine zusätzliche Einstellung ist nicht erforderlich.

Die System-/Browsereinstellung **Bewegung reduzieren** deaktiviert die Übergänge. Die weiche Höhenanimation setzt Unterstützung für moderne CSS-Funktionen voraus; ältere Browser behalten das native Auf- und Zuklappen. Die Bedienung per Tastatur bleibt erhalten.

## Daten und Deinstallation

Änderungen an FAQs, Gruppen, Übersetzungen und dynamischen Produktgruppen lösen eine Invalidierung des FAQ-Caches aus. Für die automatische Ausgabe werden FAQ-Antworten erst geladen, nachdem die passenden Gruppen ermittelt wurden.

Bei einer Deinstallation bleiben FAQ-Daten erhalten, wenn **Benutzerdaten behalten** ausgewählt ist. Ohne diese Option werden die eigenen FAQ-Tabellen entfernt. Vor einer Deinstallation oder einem Update empfiehlt sich eine Datensicherung.

## Entwicklung und Build

Releases werden mit Shopware CLI unter Linux/WSL, macOS oder Docker gebaut:

```sh
bash tools/build-release.sh
```

Der offizielle esbuild-Modus erstellt Administration- und Storefront-Assets samt Manifesten. Die ZIP entsteht über `extension package --disable-git --release` aus dem aktuellen Quellcode. Kein manuelles Zusammenfügen von JavaScript und keine handgeschriebenen Build-Manifeste. Nach Installation das Theme kompilieren; Storefront-Styles stammen ausschließlich aus `base.scss`.

Der manuell startbare GitHub-Workflow **Build installable plugin ZIP** prüft PHP-Syntax und isolierte Regressionstests, baut mit Shopware CLI und kontrolliert die ZIP-Struktur. Er erzeugt ein Download-Artefakt, veröffentlicht aber nicht automatisch ein Release.

## Lizenz

TM FAQ Pro darf kostenlos in privaten und gewerblichen Shops verwendet, angepasst und kostenlos weitergegeben werden. Der Verkauf des Plugins und die Aufnahme in kostenpflichtige Plugin-Pakete sind nicht erlaubt. Kostenpflichtige Installation, Anpassung und Support bleiben zulässig.

Es gilt die [Community License 1.0](LICENSE). Diese ist eine Source-Available-Lizenz und keine OSI-anerkannte Open-Source-Lizenz.
