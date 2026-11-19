<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_articlecategory', function (Blueprint $table) {
            $table->bigIncrements('id'); // uint8 PK
            $table->string('ab_name', 100)->unique();
            $table->unsignedBigInteger('ab_parent')->nullable();

            $table->foreign('ab_parent')
                ->references('id')
                ->on('ab_articlecategory');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_articlecategory');
    }
};
