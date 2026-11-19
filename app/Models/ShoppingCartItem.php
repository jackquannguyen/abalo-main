<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCartItem extends Model
{
    protected $table = 'ab_shoppingcart_item';

    public $timestamps = false;

    protected $fillable = [
        'ab_shoppingcart_id',
        'ab_article_id',
        'ab_createdate',
    ];

    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'ab_shoppingcart_id', 'id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'ab_article_id', 'id');
    }
}
