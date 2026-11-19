<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'ab_article';

    // Wichtig: Die Tabelle hat ab_createdate, aber kein created_at/updated_at.
    public $timestamps = false;

    protected $fillable = [
        'ab_name',
        'ab_price',
        'ab_description',
        'ab_creator_id',
        'ab_createdate',
    ];
}
