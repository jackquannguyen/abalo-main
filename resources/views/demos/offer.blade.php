<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Abalo – Angebot (Demo)</title>
    <style>
        body {font-family:Arial,sans-serif;max-width:760px;margin:30px auto;padding:0 16px}
        .top,.item {border:1px solid #ddd;border-radius:8px;padding:14px;margin-bottom:14px}
        .item.offer {background:#fff8e1;font-weight:bold}
        .price {color:#047857;font-weight:bold;float:right}
        .banner {background:#fff3cd;border:1px solid #ffe69c;padding:12px;border-radius:8px;margin:8px 0}
        button {padding:7px 12px;cursor:pointer}
        .error {color:#a11a1a} .hint {color:#555}
    </style>
</head>
<body>
    <h1>Abalo – Angebot</h1>
    <section class="top">
        @if(session('abalo_user_id'))
            <p>Angemeldet als <strong>{{ session('abalo_user_name') }}</strong> (User-ID {{ session('abalo_user_id') }})</p>
            <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Abmelden</button></form>
        @else
            <p>Gastansicht. <a href="{{ route('login') }}">Anmelden</a>, um eigene Artikel anzubieten.</p>
        @endif
        <p class="hint">Für den Live-Test: Verkäufer in einem Browser anmelden, Zuschauer im Inkognito-Fenster öffnen.</p>
        <button id="loadBtn" type="button">Verbinden &amp; Artikel laden</button>
        <p id="status" role="status">Noch nicht verbunden.</p>
    </section>
    <div id="banners" aria-live="polite"></div>
    <h2>Artikelliste</h2>
    <div id="list"></div>
    <script>
        const myUserId = @json((int) session('abalo_user_id', 0));
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        let socket = null;
        let articles = [];

        document.getElementById('loadBtn').addEventListener('click', async () => {
            connectWebSocket();
            await loadArticles();
        });

        function connectWebSocket() {
            if (socket) socket.close();
            socket = new WebSocket('ws://127.0.0.1:8080');
            const status = document.getElementById('status');
            socket.onopen = () => {
                status.textContent = 'WebSocket verbunden.';
                if (myUserId) {
                    // Für die Hochschul-Demo: die User-ID ist nur ein Anzeigename,
                    // kein authentifizierter WebSocket-Login.
                    socket.send(JSON.stringify({type:'register',userId:myUserId}));
                }
            };
            socket.onerror = () => {status.textContent = 'WebSocket konnte nicht verbunden werden.'};
            socket.onclose = () => {status.textContent = 'WebSocket getrennt.'};
            socket.onmessage = async (event) => {
                const raw = event.data instanceof Blob ? await event.data.text() : event.data;
                try {
                    const data = JSON.parse(raw);
                    if (data.type !== 'offer') return;
                    highlightArticle(data.articleId);
                    if (String(data.ownerId) !== String(myUserId)) {
                        const banner = document.createElement('p');
                        banner.className = 'banner';
                        banner.textContent = String(data.message ?? 'Ein Artikel ist im Angebot.');
                        document.getElementById('banners').prepend(banner);
                    }
                } catch (_error) { /* Ignore non-JSON demo messages. */ }
            };
        }

        async function loadArticles() {
            const list = document.getElementById('list');
            try {
                const response = await fetch('/api/articles', {headers:{'Accept':'application/json'}});
                if (!response.ok) throw new Error('HTTP ' + response.status);
                const result = await response.json();
                articles = result.data ?? [];
                renderList();
            } catch (error) {
                list.textContent = 'Artikel konnten nicht geladen werden: ' + error.message;
            }
        }

        function renderList() {
            const list = document.getElementById('list');
            list.replaceChildren();
            for (const article of articles) {
                const item = document.createElement('div');
                item.className = 'item';
                item.id = 'article-' + Number(article.id);
                const name = document.createElement('strong');
                name.textContent = article.ab_name;
                const price = document.createElement('span');
                price.className = 'price';
                price.textContent = formatPrice(article.ab_price);
                const description = document.createElement('p');
                description.textContent = article.ab_description ?? '';
                item.append(name, price, description);
                if (myUserId && Number(article.ab_creator_id) === myUserId) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = 'Artikel jetzt als Angebot anbieten';
                    button.addEventListener('click', () => makeOffer(article.id));
                    item.append(button);
                }
                list.append(item);
            }
        }

        async function makeOffer(articleId) {
            try {
                const response = await fetch(`/api/articles/${encodeURIComponent(articleId)}/offer`, {
                    method: 'POST',
                    headers: {'Accept':'application/json','X-CSRF-TOKEN':csrf}
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error ?? `HTTP ${response.status}`);
                console.log('Angebot gesendet:', data);
            } catch (error) {
                console.error(error);
                document.getElementById('status').textContent = 'Fehler: ' + error.message;
            }
        }

        function highlightArticle(id) {
            const item = document.getElementById('article-' + Number(id));
            if (item) item.classList.add('offer');
        }

        function formatPrice(price) {
            const num = Number(price);
            return Number.isFinite(num) ? (num / 100).toFixed(2).replace('.', ',') + ' €' : '–';
        }
    </script>
</body>
</html>
