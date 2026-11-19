<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // Lấy từ khóa search từ URL, ví dụ /articles?search=Toyota
        $search = trim($request->query('search', ''));

        // Tạo query lấy dữ liệu từ bảng ab_article
        $query = DB::table('ab_article')
            ->select('id', 'ab_name', 'ab_price', 'ab_description', 'ab_creator_id', 'ab_createdate');

        // Nếu người dùng có nhập search thì lọc theo tên article
        if ($search !== '') {
            $query->where('ab_name', 'ILIKE', '%' . $search . '%');
        }

        // Lấy dữ liệu, sắp xếp theo id
        $articles = $query->orderBy('id')->get();

        // Kiểm tra ảnh cho từng article
        foreach ($articles as $article) {
            $article->img = null;

            if (file_exists(public_path('img/' . $article->id . '.png'))) { //tìm public/img/1.png
                $article->img = $article->id . '.png';
            } elseif (file_exists(public_path('img/' . $article->id . '.jpg'))) {
                $article->img = $article->id . '.jpg';
            }
        }

        // Trả dữ liệu sang view
        return view('articles.index', [
            'articles' => $articles,
            'search' => $search,
        ]);
    }
}
