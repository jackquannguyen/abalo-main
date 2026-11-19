<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    protected $table = 'ab_shoppingcart';

    public $timestamps = false;

    protected $fillable = [
        'ab_creator_id',
        'ab_createdate',
    ];

    public function items()
    {
        return $this->hasMany(ShoppingCartItem::class, 'ab_shoppingcart_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(AbUser::class, 'ab_creator_id', 'id');
    }
}
