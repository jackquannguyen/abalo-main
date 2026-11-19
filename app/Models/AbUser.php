<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbUser extends Model
{
    protected $table = 'ab_user';

    public $timestamps = false;

    protected $fillable = [
        'ab_name',
        'ab_password',
        'ab_mail',
    ];
}
