<?php
// app/Models/ReturnedGoodRecipe.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnedGoodRecipe extends Model
{
    protected $fillable = [
        'returned_good_id',
        'external_supply_recipe_id',
        'price',
        'qty',
        'total_amount',
    ];

    public function returnedGood()
    {
        return $this->belongsTo(ReturnedGood::class);
    }

    public function supplyLine()
    {
        return $this->belongsTo(ExternalSupplyRecipe::class, 'external_supply_recipe_id');
    }

    /**
     * Convenience accessor so you can still write $line->recipe
     */
    public function getRecipeAttribute()
    {
        return $this->supplyLine->recipe;
    }


    public function recipe()
    {
        return $this->hasOneThrough(
            Recipe::class,
            ExternalSupplyRecipe::class,
            'id',                      // ForeignSupplyRecipe primary key
            'id',                      // Recipe primary key
            'external_supply_recipe_id', // Local FK on this table
            'recipe_id'                  // FK on external_supply_recipes
        );
    }
}
