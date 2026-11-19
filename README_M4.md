# DBWT2 – Meilenstein 4 (M4)

> Historische Kursnotizen. Routen und Setup können sich in der aktuellen Version geändert haben. Siehe [README.md](README.md).

Autoren:
1. Thua Vu, Nguyen
2. Ha Hong Quan, Nguyen

## Enthaltene M4-Bestandteile

- Aufgabe 5: Import/Seeder für `article_has_articlecategory.csv`
- Aufgabe 7: Vue über NPM in Abalo eingebunden (`vue`, `@vitejs/plugin-vue`, `vite.config.js`)
- Aufgabe 10: dynamische Artikelsuche mit Vue unter `/article-search`
- Aufgabe 12: Diskussion als `Aufgabe 12.docx`
- Aufgabe 13: Artikeleingabe mit Vue unter `/newarticle-vue`, Speicherung über `POST /api/articles`
- Konfigurationen: `package.json`, `package-lock.json`, `vite.config.js`, Laravel-Konfigurationen, Routen und Controller

## Starten

Falls `vendor/` und `node_modules/` nicht vorhanden sind:

```bash
composer install
npm install
```

Danach zwei Terminals starten:

```bash
php artisan serve
```

```bash
npm run dev
```

Wichtige Test-URLs:

- Artikelsuche: `http://127.0.0.1:8000/article-search`
- Artikeleingabe mit Vue: `http://127.0.0.1:8000/newarticle-vue`
- Artikelübersicht: `http://127.0.0.1:8000/articles`
