# Changelog

## 1.1.7 – 14.10.2026

- Eigene, beschriftete FAQ-Suche direkt neben dem Gruppenfilter; globale Shopware-Suche bleibt getrennt
- Suche in Fragen und Antworten mit kurzer Tipppause oder sofort per Enter
- Such- und Filterfelder umbrechen auf schmalen Ansichten
- Aktionsbuttons in FAQ- und Gruppenverwaltung auf Shopwares Größe „large“ (48 Pixel) angehoben

## 1.1.6 – 14.10.2026

- Serverseitiger Gruppenfilter in der FAQ-Liste, kombinierbar mit Suche und Seitennavigation; Schutz vor veralteten Antworten bei schnellem Wechsel
- Anzeigename einheitlich „FAQ Pro“; technischer Name unverändert
- Navigation unter Kataloge → FAQ Pro mit getrennten Unterpunkten FAQs und Gruppen
- Deutlich sichtbare 48-Pixel-Icons in Kopfbereichen und Erlebniswelt-Vorschauen
- Änderungsdatum in FAQ- und Gruppenlisten in lesbarem lokalisiertem Format

## 1.1.5 – 14.10.2026

- Überschrift unter Verhalten zentrierbar, linksbündig oder ausblendbar; pro Erlebniswelt-Element überschreibbar
- Erlebniswelt-Elemente übernehmen standardmäßig die Verkaufskanal-Vorgabe; eigene Ausrichtung und Sichtbarkeit haben Vorrang
- Ausblenden erhält den gespeicherten Überschriftentext und entfernt den sichtbaren Überschriftenabstand
- Sanfte FAQ-Animation und Plus-/Minus-Übergang mit Unterstützung für reduzierte Bewegung
- Bestehende Überschriften bleiben standardmäßig zentriert; ältere Browser behalten natives Auf-/Zuklappen

### Enthaltene Verbesserungen aus der nicht öffentlich veröffentlichten Testversion 1.1.4

- Cache-Tags auch für Seiten ohne passende FAQs; Invalidierung bei FAQ-, Gruppen-, Übersetzungs- und Produktgruppenänderungen
- FAQ-Antworten werden erst nach Ermittlung der passenden Gruppen geladen
- Deinstallation entfernt eigene Tabellen nur bei deaktivierter Option zum Behalten der Benutzerdaten
- Übersetzte Standardüberschrift mit korrekt benannten Storefront-Sprachdateien
- Styles ausschließlich über Shopwares SCSS-/Theme-Verarbeitung
- Eigenen JavaScript-Bundler durch den offiziellen Shopware-CLI-Build ersetzt
- Offizieller Shopware-CLI-Build und ZIP-Prüfung bestanden

## 1.1.3

- Anpassungen anhand der Shopware-Änderungen bis 6.7.15.1
- Dynamische Produktgruppen verwenden ab Shopware 6.7.13 die neue enrichCriteria-Schnittstelle; ältere 6.7-Versionen behalten den bisherigen Aufruf
- Dienste und Routen auf PHP-Konfiguration umgestellt, um die XML-Abkündigung zu berücksichtigen
- JSON-LD verwendet lokal definierte Kodierungsoptionen und funktioniert auch bei strengem Twig-Variablencheck
- Keine Änderung an bestehenden FAQ-Zuordnungen oder Datenbanktabellen
- Lokale Regressionstests erweitert; ein vollständiger Shopware-Integrationstest ist vor dem Live-Update erforderlich

## 1.1.2

- Produktzuordnung berücksichtigt das Hauptprodukt einer Variante
- Einzelne Varianten-Zuordnungen bleiben auf die ausgewählte Variante begrenzt

## 1.1.1

- Kategoriezuordnungen auf direkt ausgewählte Kategorieseiten begrenzt
- Keine automatische Übernahme von Kategorie-FAQs auf Produkte oder Unterkategorien
- Erlebniswelten-Ausgabe berücksichtigt den globalen FAQ-Hauptschalter
- Zuordnungshinweise in der Administration präzisiert

- Anzeigename TM FAQ Pro; technischer Pluginname bleibt TuamiFaqPro
- Neues Pluginicon und einheitliche FAQ-Icons in den Erlebniswelten

## 1.1.0

- Zwei auswählbare FAQ-Darstellungen: Karten und Trennlinien
- Trennlinien-Layout nach dem Vorbild einer flachen FAQ-Liste mit einstellbarer Linienfarbe
- Bestehende Installationen verwenden weiterhin das Kartenlayout

## 1.0.0

- Erste öffentliche Version
- Mehrsprachige FAQ-Gruppen und FAQs
- Zuordnung über Produkte, Kategorien, dynamische Produktgruppen und Schlüsselwörter
- Einschränkung nach Verkaufskanal und Rule Builder
- Automatische Ausgabe auf zugeordneten Produkt- und Kategorieseiten
- Erlebniswelten-Element für die gezielte Platzierung
- Konfigurierbares Layout und strukturierte FAQ-Daten
