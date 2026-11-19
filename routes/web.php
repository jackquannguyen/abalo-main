<?php

use App\Http\Controllers\ApiArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\AuthController;
use App\Support\AbaloSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

// from m5 use this vue web for default
Route::get('/', function () {
    return view('NewSite');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/isloggedin', [AuthController::class, 'isLoggedIn'])->name('haslogin');
Route::get('/demo/offer', fn () => view('demos.offer'))->name('demo.offer');

Route::get('/newarticle', function () {
    return view('articles.newarticle');
});

Route::post('/articles', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0.01',
        'description' => 'nullable|string'
    ]);

    $ownerId = AbaloSession::userId($request);
    if ($ownerId === null) {
        return redirect()->route('login');
    }

    DB::table('ab_article')->insert([
        'ab_name' => $validated['name'],
        'ab_price' => $validated['price'],
        'ab_description' => $validated['description'] ?? '',
        'ab_creator_id' => $ownerId,
        'ab_createdate' => now()
    ]);

    return redirect('/articles')->with('success', 'Artikel wurde erfolgreich gespeichert.');
});

Route::get('/test', function () {
    return view('/articles/test');
});

// M3 Aufgabe 8 + 9: Webservice fuer Artikel
// Hinweis: Diese API-Routen stehen bewusst in web.php, damit Session + CSRF fuer AJAX funktionieren.
Route::get('/api/articles', [ApiArticleController::class, 'search']);
Route::post('/api/articles', [ApiArticleController::class, 'store']);

// M3 Aufgabe 11: serverseitiger Warenkorb
Route::get('/api/shoppingcart', [ShoppingCartController::class, 'show']);
Route::post('/api/shoppingcart', [ShoppingCartController::class, 'store']);
Route::delete('/api/shoppingcart/{shoppingcartid}/articles/{articleId}', [ShoppingCartController::class, 'destroy']);

Route::post('/api/articles/{id}/offer', [OfferController::class, 'offer']);

// Vue M5
// Route::get('/newsite', function () {
//     return view('NewSite');
// });
