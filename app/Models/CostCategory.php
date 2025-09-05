<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cost;
use App\Models\User;

class CostCategory extends Model
{
    protected $fillable = [
        'name',
        'user_id', // ✅ make user_id mass assignable
    ];

    // ✅ A cost category can have many costs
    public function costs()
    {
        return $this->hasMany(Cost::class);
    }

    // ✅ A cost category belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
