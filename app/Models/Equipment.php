<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id', // ✅ Add user_id to fillable
    ];

    // ✅ Relationship: each equipment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
