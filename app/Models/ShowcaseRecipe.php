<?php
// app/Models/ShowcaseRecipe.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Showcase;
use App\Models\Recipe;
use App\Models\Department;
use App\Models\User;

class ShowcaseRecipe extends Model
{
    use HasFactory;

    protected $table = 'showcase_recipes';

    protected $fillable = [
        'showcase_id',
        'recipe_id',
        'category',
        'price',
        'quantity',
        'sold',
        'reuse',
        'waste',
        'potential_income',
        'actual_revenue',
        'user_id',
    ];

    protected $casts = [
        'reuse' => 'integer',
        'waste' => 'integer',
    ];

    /**
     * Parent showcase.
     */
    public function showcase()
    {
        return $this->belongsTo(Showcase::class);
    }

    /**
     * The recipe being showcased.
     */
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * The department for that recipe line.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * The user who created this line.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
