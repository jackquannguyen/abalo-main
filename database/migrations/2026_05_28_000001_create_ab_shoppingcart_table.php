<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_shoppingcart', function (Blueprint $table) {
            // uint8 im Aufgabentext entspricht hier bigIncrements/bigint.
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ab_creator_id');
            $table->timestamp('ab_createdate');

            $table->foreign('ab_creator_id')
                ->references('id')
                ->on('ab_user')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_shoppingcart');
    }
};
