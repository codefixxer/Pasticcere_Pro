<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShowcaseRecipe;
use App\Models\User;

class Showcase extends Model
{
    use HasFactory;

    protected $fillable = [
        'showcase_name',
        'showcase_date',
        'template_action',
        'save_template',
        'break_even',
        'total_revenue',
        'plus',
        'real_margin',
        'potential_income_average',
        'user_id',
    ];

    protected $casts = [
        'showcase_date' => 'date',
    ];

    /**
     * The lines (recipes) in this showcase.
     */
    public function recipes()
    {
        return $this->hasMany(ShowcaseRecipe::class);
    }

    /**
     * The user who created this showcase.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
