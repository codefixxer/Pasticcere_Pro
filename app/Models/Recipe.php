<?php

namespace App\Models;

use App\Models\User;
use App\Models\LaborCost;
use App\Models\Department;
use App\Models\Ingredient;
use App\Models\RecipeCategory;
use App\Models\RecipeIngredient;
use Illuminate\Support\Facades\Auth;
use App\Services\LaborCostCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_name',
        'recipe_category_id',
        'department_id',
        'unit_ing_cost',

        'sell_mode',
        'selling_price_per_piece',
        'selling_price_per_kg',

        'labour_time_min',
        'labor_cost_id',     // keep the link to “active” LaborCost set for this user (usually the global)
        'labor_cost_mode',   // 'shop' | 'external'
        'packing_cost',
        'total_expense',
        'potential_margin',
        'potential_margin_pct',

        'total_pieces',
        'recipe_weight',
        'production_cost_per_kg',
        'add_as_ingredient',
        'vat_rate',
        'user_id',
    ];

    // ----- Relationships -----
    public function laborCostRate()
    {
        return $this->belongsTo(LaborCost::class, 'labor_cost_id');
    }

    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class, 'recipe_category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ----- Helpers / Accessors -----

    public function getIngredientsTotalCostAttribute(): float
    {
        return $this->ingredients->sum('cost');
    }

    public function getIngredientsCostPerBatchAttribute(): float
    {
        return $this->ingredients->sum(function ($ri) {
            return ($ri->quantity_g / 1000) * ($ri->ingredient->price_per_kg ?? 0);
        });
    }

    public function getRawCostPerUnitAttribute(): float
    {
        if ($this->sell_mode === 'kg') {
            return $this->ingredients_cost_per_batch / max(1, ($this->recipe_weight ?: 0));
        }
        return $this->ingredients_cost_per_batch / max(1, ($this->total_pieces ?: 0));
    }

    /**
     * Effective €/min (shop, external) built from GLOBAL + dept override + shared %.
     */
    public function getEffectiveLaborRatesAttribute(): array
    {
        $user   = $this->user ?? Auth::user();
        $dept   = $this->department;
        return LaborCostCalculator::effectiveRates($user, $dept);
    }

    /**
     * Final labour € for this recipe using effective rates and this recipe's minutes/mode.
     */
    public function getLaborCostAttribute(): float
    {
        $rates = $this->effective_labor_rates; // ['shop'=>x,'external'=>y]
        $rate  = ($this->labor_cost_mode === 'external') ? ($rates['external'] ?? 0) : ($rates['shop'] ?? 0);
        return round(($this->labour_time_min ?? 0) * $rate, 2);
    }

    public function asIngredient()
    {
        return $this->hasOne(Ingredient::class, 'recipe_id');
    }
}
