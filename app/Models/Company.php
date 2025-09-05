<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Company extends Model
{
    protected $fillable = [
        'name',
        'user_id', // ✅ allow mass assignment of user_id
    ];

    // ✅ Relationship: each company belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
