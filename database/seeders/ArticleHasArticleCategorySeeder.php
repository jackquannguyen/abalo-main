<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleHasArticleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đọc toàn bộ file user.csv thành mảng các dòng
        $lines = file(__DIR__ . '/data/article_has_articlecategory.csv');
        
        // Tách từng dòng theo dấu ; để biến mỗi dòng thành một mảng nhỏ
        $rows = array_map(fn($line) => str_getcsv($line, ';'), $lines);

        // Bỏ dòng đầu tiên vì là header (tên cột)
        array_shift($rows);
        
        foreach ($rows as $row) {
            DB::table('ab_article_has_articlecategory')->insert([
                'ab_articlecategory_id' => $row[0],
                'ab_article_id' => $row[1],
            ]);
        }
    }
}
