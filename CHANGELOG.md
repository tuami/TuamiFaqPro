# Changelog

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
