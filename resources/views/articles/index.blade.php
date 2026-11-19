@extends('navigation')
@section('title', 'Artikelübersicht')
@section('nav')<nav id="navigation"></nav>@endsection
@section('content')
<div class="articles-wrapper">
    <h1>Artikelübersicht</h1>

    <h2>Warenkorb</h2>
    <div id="cart-box">
        <ul id="cart-items">
            <li>Der Warenkorb ist leer.</li>
        </ul>

        <div id="cart-sum-row">
           <span>Summe:</span>
           <span id="cart-sum">0,00 €</span>
        </div>
    </div>

    <br>

    <form method="GET" action="{{ route('articles.index') }}">
        <label for="search">Suche:</label>
        <input type="text" id="search" name="search" value="{{ $search }}">
        <button type="submit">Suchen</button>
    </form>

    <br>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Bild</th>
                <th>Name</th>
                <th>Preis</th>
                <th>Beschreibung</th>
                <th>Ersteller-ID</th>
                <th>Erstellt am</th>
                <th>Aktion</th>
            </tr>
        </thead>
        <tbody>
            @forelse($articles as $article)
                <tr>
                    <td>{{ $article->id }}</td>
                    <td>
                        @if ($article->img)
                            <img src="/img/{{ $article->img }}" alt="{{ $article->ab_name }}" width="120">
                        @else
                            Kein Bild
                        @endif
                    </td>
                    <td>{{ $article->ab_name }}</td>
                    <td>{{ number_format($article->ab_price / 100, 2, ',', '.') }} €</td>
                    <td>{{ $article->ab_description }}</td>
                    <td>{{ $article->ab_creator_id }}</td>
                    <td>{{ $article->ab_createdate }}</td>
                    <td>
                        <button type="button" class="add-to-cart" data-id="{{ $article->id }}"
                            data-name="{{ $article->ab_name }}"
                            data-price="{{ number_format($article->ab_price / 100, 2, ',', '.') }}">
                            +
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Keine Artikel gefunden.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    </div>

@endsection



