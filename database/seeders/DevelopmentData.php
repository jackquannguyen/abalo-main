<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DevelopmentData extends Seeder
{
    public function run(): void
    {
        // Gọi lần lượt 3 hàm để nạp dữ liệu cho 3 bảng
        $this->seedUsers();
        $this->seedArticleCategories();
        $this->seedArticles();
    }

    /**
     * Nạp dữ liệu từ user.csv vào bảng ab_user
     */
    private function seedUsers(): void
    {
        // Đọc toàn bộ file user.csv thành mảng các dòng
        $lines = file(__DIR__ . '/data/user.csv');

        // Tách từng dòng theo dấu ; để biến mỗi dòng thành một mảng nhỏ
        $rows = array_map(fn($line) => str_getcsv($line, ';'), $lines);

        // Bỏ dòng đầu tiên vì đó là header (tên cột)
        array_shift($rows);

        // Duyệt từng dòng dữ liệu
        foreach ($rows as $row) {
            DB::table('ab_user')->insert([
                'id' => (int) $row[0],
                'ab_name' => $row[1],
                'ab_password' => Hash::make($row[2]),
                'ab_mail' => $row[3],
            ]);
        }
    }

    /**
     * Nạp dữ liệu từ articlecategory.csv vào bảng ab_articlecategory
     */
    private function seedArticleCategories(): void
    {
        $lines = file(__DIR__ . '/data/articlecategory.csv');
        $rows = array_map(fn($line) => str_getcsv($line, ';'), $lines);

        // Bỏ dòng header
        array_shift($rows);

        foreach ($rows as $row) {
            // Nếu parent là 0, NULL, null, hoặc rỗng
            // thì hiểu là category gốc => ab_parent = null
            if (
                $row[2] === 'NULL'
            ) $row[2] = null;
            else {
                $row[2] = (int) $row[2];
            }


            DB::table('ab_articlecategory')->insert([
                'id' => (int) $row[0],
                'ab_name' => $row[1],
                'ab_parent' => $row[2],
            ]);
        }
    }

    /**
     * Nạp dữ liệu từ articles.csv vào bảng ab_article
     */
    private function seedArticles(): void
    {
        $lines = file(__DIR__ . '/data/articles.csv');
        $rows = array_map(fn($line) => str_getcsv($line, ';'), $lines);

        // Bỏ dòng header
        array_shift($rows);

        foreach ($rows as $row) {
            // Giá trong file CSV đang ở dạng có dấu chấm phân tách hàng nghìn
            // Ví dụ: 30.000. Bỏ dấu chấm đi => 30000
            $price = str_replace('.', '', $row[2]);

            // Đổi sang cent
            $price = (int) $price * 100;

            // Chuyển ngày giờ từ format trong CSV sang format chuẩn DB
            $createdate = Carbon::createFromFormat('d.m.y H:i', trim($row[5]))
                ->format('Y-m-d H:i:s');

            DB::table('ab_article')->insert([
                'id' => (int) $row[0],
                'ab_name' => $row[1],
                'ab_price' => $price,
                'ab_description' => $row[3],
                'ab_creator_id' => (int) $row[4],
                'ab_createdate' => $createdate,
            ]);
        }
    }
}
