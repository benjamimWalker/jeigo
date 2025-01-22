<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $hidden = [
        'id'
    ];

    public static function saved($callback)
    {
        cache()->flush();
    }

    public static function deleted($callback)
    {
        cache()->flush();
    }
}
