<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'phone',
        'email',
        'notes',
        'user_id', // ✅ add user_id to fillable
    ];

    // ✅ Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
