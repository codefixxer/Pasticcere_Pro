<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PastryChef extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id', // ✅ Track which user created this chef
    ];

    // ✅ Relationship: chef belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
