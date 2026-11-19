<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class MassUserSeeder extends Seeder
{
    public function run(): void
    {
        $fakeData = Faker::create('de_DE');

        $totalUsers = 10000;
        $limit = 1000;
        $listUsers = [];

        for ($i = 1; $i <= $totalUsers; $i++) {
            $listUsers[] = [
                'ab_name' => 'massuser_' . $i . '_' . $fakeData->userName(), //massuser_1_leon123
                'ab_password' => Hash::make('1234'),
                'ab_mail' => 'massuser_' . $i . '_' . $fakeData->email(),
            ];

            if (count($listUsers) === $limit) {
                DB::table('ab_user')->insert($listUsers);
                $listUsers = [];
            }
        }
    }
}
