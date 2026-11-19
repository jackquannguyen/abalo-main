<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_user', function (Blueprint $table) {
            $table->bigIncrements('id'); // uint8 PK
            $table->string('ab_name', 80)->unique();
            $table->string('ab_password', 200);
            $table->string('ab_mail', 200)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_user');
    }
};
