<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use Illuminate\Http\Request;
use App\Support\AbaloSession;
use Illuminate\Support\Facades\Validator;

class ShoppingCartController extends Controller
{
    // Aufgabe 11d: Nach Reload Warenkorb aus DB laden
    public function show()
    {
        $cart = $this->getOrCreateCart();

        if ($cart === null) {
            return response()->json([
                'success' => false,
                'error' => 'Nicht angemeldet.',
            ], 401);
        }

        return $this->cartResponse($cart);
    }

    // Aufgabe 11b: POST /api/shoppingcart mit articleid
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'articleid' => ['required', 'integer', 'exists:ab_article,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $cart = $this->getOrCreateCart();

        if ($cart === null) {
            return response()->json([
                'success' => false,
                'error' => 'Nicht angemeldet.',
            ], 401);
        }

        $articleId = (int) $request->input('articleid');

        // Kein doppelter Eintrag desselben Artikels im selben Warenkorb.
        ShoppingCartItem::firstOrCreate(
            [
                'ab_shoppingcart_id' => $cart->id,
                'ab_article_id' => $articleId,
            ],
            [
                'ab_createdate' => now(),
            ]
        );

        return $this->cartResponse($cart);
    }

    // Aufgabe 11c: DELETE /api/shoppingcart/{shoppingcartid}/articles/{articleId}
    public function destroy(int $shoppingcartid, int $articleId)
    {
        $userId = AbaloSession::userId(request());

        if ($userId === null) {
            return response()->json([
                'success' => false,
                'error' => 'Nicht angemeldet.',
            ], 401);
        }

        $cart = ShoppingCart::where('id', $shoppingcartid)
            ->where('ab_creator_id', $userId)
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'error' => 'Warenkorb nicht gefunden.',
            ], 404);
        }

        $deleted = ShoppingCartItem::where('ab_shoppingcart_id', $cart->id)
            ->where('ab_article_id', $articleId)
            ->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Artikel nicht im Warenkorb gefunden.',
            ], 404);
        }

        return $this->cartResponse($cart);
    }

    private function getOrCreateCart(): ?ShoppingCart
    {
        $userId = AbaloSession::userId(request());

        if ($userId === null) {
            return null;
        }

        $cart = ShoppingCart::where('ab_creator_id', $userId)
            ->orderByDesc('id')
            ->first();

        if (!$cart) {
            $cart = ShoppingCart::create([
                'ab_creator_id' => $userId,
                'ab_createdate' => now(),
            ]);
        }

        return $cart;
    }

    private function cartResponse(ShoppingCart $cart)
    {
        $cart->load('items.article');

        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'article_id' => $item->ab_article_id,
                'name' => $item->article?->ab_name,
                'price' => $item->article?->ab_price,
                'description' => $item->article?->ab_description,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'shoppingcartid' => $cart->id,
            'count' => $items->count(),
            'sum' => $items->sum('price'),
            'items' => $items,
        ]);
    }

}
