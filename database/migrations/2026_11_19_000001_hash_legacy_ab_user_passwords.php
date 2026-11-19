<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ab_user')) {
            return;
        }

        // Older milestone seeders stored demo passwords in plain text.
        // Upgrade existing local databases without requiring a destructive reset.
        DB::table('ab_user')->select('id', 'ab_password')->orderBy('id')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    if (!preg_match('/^\$(2[aby]\$|argon2(?:id|i)\$)/', (string) $user->ab_password)) {
                        DB::table('ab_user')->where('id', $user->id)
                            ->update(['ab_password' => Hash::make($user->ab_password)]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Password hashes cannot be safely reverted to their original values.
    }
};
