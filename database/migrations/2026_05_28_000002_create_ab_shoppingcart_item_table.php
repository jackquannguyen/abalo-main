<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_shoppingcart_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ab_shoppingcart_id');
            $table->unsignedBigInteger('ab_article_id');
            $table->timestamp('ab_createdate');

            // Aufgabe 11a: Wenn ein shoppingcart entfernt wird,
            // werden alle zugehoerigen shoppingcart_items automatisch entfernt.
            $table->foreign('ab_shoppingcart_id')
                ->references('id')
                ->on('ab_shoppingcart')
                ->cascadeOnDelete();

            $table->foreign('ab_article_id')
                ->references('id')
                ->on('ab_article')
                ->cascadeOnDelete();

            $table->unique(['ab_shoppingcart_id', 'ab_article_id'], 'ab_cart_article_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_shoppingcart_item');
    }
};
