M5 – Aufgaben 13 bis 15 (historische Kurs-Demos)
================================================

Aktuelle Installations- und Testanleitung: README.md

1) Node-Bridge starten: node server.js (127.0.0.1:8080)
2) Laravel starten: php artisan serve
3) Frontend starten: npm run dev

AUFGABE 14 – ANGEBOT (implementiert, lokal testbar)
---------------------------------------------------
- Die frühere Datei /5-ws14-offer.html leitet auf /demo/offer weiter.
- Login ist unter /login; POST /login prüft Benutzername und Passwort aus ab_user.
- Der Verkäufer muss per Laravel-Session angemeldet sein und den Artikel besitzen.
- POST /api/articles/{id}/offer ruft den OfferController auf; dieser sendet
  serverseitig POST /notify an die lokale Node-Bridge.
- Teste mit Verkäufer im Browser und Zuschauer in einem privaten Browserfenster.
  Zwei normale Tabs nutzen dieselbe Login-Session.
- Die Benachrichtigung wird an alle verbundenen Demo-Browser verteilt, nicht
  nur an Nutzer, die exakt diesen Artikel betrachten.

AUFGABE 13 – VERKAUFSMELDUNG (nicht vollständig integriert)
-------------------------------------------------------------
- Die Seite /5-ws13-sold.html ist eine ältere Übung.
- Die Route POST /api/articles/{id}/sold fehlt derzeit im Laravel-Backend.
  Ohne Implementierung liefert der Button 404; nicht als fertige Funktion ausgeben.
- Die WebSocket-User-ID-Registrierung ist eine Demo, keine echte Authentifizierung.

SICHERHEIT
----------
- WebSocket-/HTTP-Brücke nur für lokale Entwicklung (127.0.0.1) gedacht.
- Im aktuellen Webprojekt werden POST-Routen durch CSRF geschützt.
- Vor Produktivbetrieb müssen Demo-Nutzer, Tokens, WebSocket-Authentifizierung
  und sonstige Demo-Endpunkte überarbeitet werden.
