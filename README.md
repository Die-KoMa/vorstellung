# Fachschaftsvorstellung im Anfangsplenum

Dieses Tool soll die Fachschaftsvorstellung im Anfangsplenum hilfreicher gestalten. Dazu sollen Fachschaften im Vornherein einige Informationen zu sich eintragen. Aus diesen Informationen wird dann eine Präsentation erstellt wird.

In der Präsentation werden die Fachschaften nach Bundesland gruppiert und von Norden nach Süden geordnet. Zu jeder Fachschaft wird neben den von ihr angegebenen Daten ggf. noch relevante Daten angezeigt. Dies geschieht aus dem Datenbestand von [Freebase].

[Freebase]: http://www.freebase.com

## Installation

Die Installation ist einfach: Kopiere den Inhalt der `config.sample.php` in eine neue `config.php` und passe ihn ggf. an. Insbesondere müssen ggf. [Google-API-Keys] eingetragen werden, da ohne sie die korrekte Interaktion mit Freebase nicht sichergestellt ist.

Die Daten von den Fachschaften eingegebenen Daten werden in der Datei `data.json` abgespeichert, sodass die Webanwendung Schreibzugriff auf diese Datei haben muss.

[Google-API-Keys]: https://console.developers.google.com/
