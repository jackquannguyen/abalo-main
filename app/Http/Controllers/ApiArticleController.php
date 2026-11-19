<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Support\AbaloSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiArticleController extends Controller
{
    // Aufgabe 8: GET /api/articles?search=...
    public function search(Request $request)
    {
        $search = trim($request->query('search', ''));

        $page = max(1, (int)$request->query('page', 1));
        $pagelimit = 5;
        $offset = ($page - 1) * $pagelimit;

        $query = Article::query()->orderBy('id');

        if ($search !== '') {
            // PostgreSQL: ILIKE; SQLite in automated tests: LIKE.
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->where('ab_name', $operator, '%' . $search . '%');
        }
        $total = (clone $query)->reorder()->count();

        $articles = $query->limit($pagelimit)->offset($offset)
        ->get([
            'id',
            'ab_name',
            'ab_price',
            'ab_description',
            'ab_creator_id',
            'ab_createdate',
        ]);

        return response()->json([
            'success' => true,
            'search' => $search,
            'count' => $articles->count(),
            'data' => $articles,
            'limit' => $pagelimit,
            'total' => $total,
            'page' => $page,
        ]);
    }

    // Aufgabe 9: POST /api/articles mit name, price, description
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:80'],
            // price ist hier in Cent gemeint, passend zu ab_article.ab_price.
            'price' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $creatorId = AbaloSession::userId(request());

        if ($creatorId === null) {
            return response()->json([
                'success' => false,
                'error' => 'Nicht angemeldet.',
            ], 401);
        }

        $article = Article::create([
            'ab_name' => $request->input('name'),
            'ab_price' => (int) $request->input('price'),
            'ab_description' => $request->input('description'),
            'ab_creator_id' => $creatorId,
            'ab_createdate' => now(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $article->id,
        ], 201);
    }

}
