<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningDay extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'days',
    ];
}
