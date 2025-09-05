<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Recipe;

class RecipeCategory extends Model
{
    protected $fillable = [
        'name',
        'user_id', // ✅ Allow mass assignment of the user
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    // ✅ Each category belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
