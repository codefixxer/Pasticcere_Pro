<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalSupplyRecipe extends Model
{
    protected $fillable = [
        'external_supply_id',
        'recipe_id',
        'category',
        'price',
        'qty',
        'total_amount',
        'user_id',
    ];

    public function externalSupply()
    {
        return $this->belongsTo(ExternalSupply::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    // ✅ Add this to track returns
    public function returns()
    {
        return $this->hasMany(ReturnedGoodRecipe::class, 'external_supply_recipe_id');
    }

    // ✅ This is used in the controller to validate how much can be returned
    public function getRemainingQtyAttribute()
    {
        $returnedQty = $this->returns->sum('qty');
        return $this->qty - $returnedQty;
    }
}
