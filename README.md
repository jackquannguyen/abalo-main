# Abalo – University Web Application

**Course:** Datenbanken und Webtechnologien (DBWT2), FH Aachen, Summer Semester 2026  
**Type:** University team project / learning application  
**Stack:** PHP 8.2+, Laravel 12, PostgreSQL, Vue 3, Vite, PrimeVue, Node.js WebSocket (`ws`)

Abalo is a university exercise project for managing articles, browsing/searching listings, a shopping cart and real-time notification demonstrations. This is **not** a production-ready marketplace. It evolved through course milestones; some routes and UI pages are demonstrations rather than completed commercial features.

## Implemented features

- Article list and name search with server-side pagination (5 results per page)
- Vue-based live search with a 300 ms debounce and cancellation of outdated requests
- Article creation and a database-backed shopping cart
- A simple session login against the `ab_user` table, with hashed passwords
- **Offer demo:** an authenticated article owner can trigger `POST /api/articles/{id}/offer`. Laravel validates ownership, sends JSON to the local Node.js HTTP bridge, and Node.js broadcasts a WebSocket notification to connected demo browsers
- Additional coursework demos under `public/` (some incomplete)

### Offer demo flow

```text
Seller browser → POST /api/articles/{id}/offer (Laravel + CSRF)
               → server-side owner check using the login session
               → POST http://127.0.0.1:8080/notify (Node.js, local only)
               → WebSocket broadcast → viewer browser
```

## Running locally (Windows / PowerShell)

Prerequisites: PHP 8.2+, Composer, Node.js supported by Vite 7, npm and PostgreSQL with the `pdo_pgsql` PHP extension.

1. Create an empty database called `abalo` in PostgreSQL. In the project root:

   ```powershell
   composer install
   npm ci
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   If you already have `.env`, **do not overwrite it**. Enter the correct `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` for your local PostgreSQL database.

2. Apply migrations. On a new **empty** database, populate the original exercise dataset:

   ```powershell
   php artisan migrate --seed
   ```

   On an existing database with data, run **only** `php artisan migrate` (do not repeat the seed). The new password migration hashes old plain-text demo credentials in `ab_user` without dropping your data.

3. Launch three terminals in the project root:

   ```powershell
   php artisan serve
   ```
   ```powershell
   npm run dev
   ```
   ```powershell
   node server.js
   ```

4. Open the application at <http://127.0.0.1:8000/> and the offer demo at <http://127.0.0.1:8000/demo/offer>.

### Testing Angebot (two browser sessions)

- Sign in at `/login` using a **local test account from `database/seeders/data/user.csv`**. For example, user `seller` owns sample article 1 in the exercise dataset. These are **public demonstration credentials**, not accounts that should ever be deployed to the Internet.
- Open `/demo/offer` in the seller session. Press **Verbinden & Artikel laden** and then **Artikel jetzt als Angebot anbieten** under an article owned by the signed-in user.
- Open `/demo/offer` as a guest in an **incognito window or another browser profile** and press **Verbinden & Artikel laden**. The second window receives the offer notification immediately.
- Two normal tabs in the same browser share the same Laravel session; manually entering different user IDs is not authentication.
- The legacy `/5-ws14-offer.html` link redirects to `/demo/offer`.

## Automated tests

```powershell
php artisan test
```

Tests cover login, ownership checks for offer notifications and basic article search. The default test configuration uses **SQLite in memory**; install/enable PHP's `pdo_sqlite` extension for tests. Real application search uses PostgreSQL `ILIKE`.

## Project structure

```text
app/Http/Controllers/   Laravel article, cart, login and offer endpoints
app/Support/            Session identity helper
resources/components/   Vue components
resources/views/demos/  Server-rendered offer demo with CSRF token
routes/web.php          Laravel routes
server.js               Local Node.js WebSocket + HTTP notification bridge
database/               Migrations and sample CSV seed data
tests/                  Laravel feature tests
```

## Limitations and security notes

- The course test accounts are public and should be replaced before any real deployment.
- `server.js` binds to **127.0.0.1** so the notification bridge is only intended for local use. It is **not a production authorization layer** and WebSocket user registration is still a coursework demo, not an authenticated subscription.
- Offer notifications are broadcast to all connected demo clients; they are **not yet limited to viewers of one particular article**.
- The separate `sold` example may refer to endpoints not implemented in the current version.
- Do not commit `.env`, secrets, real personal data or real student identification numbers. Check Git history, not just the current files, before publishing.
- Run `composer audit` and `npm audit`; dependency vulnerabilities may remain.

## Team and attribution

Developed as a **team project at FH Aachen**. The original GitLab coursework and contributions belong to the project team; the later repository import and bug fixes should not be represented as a solo implementation. If you publish this project, ensure your teammates and FH Aachen permit public redistribution. Document your personal contributions accurately in your CV or portfolio.
