<?php
// app/Models/ExternalSupply.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalSupply extends Model
{
    protected $fillable = [
        'client_id',
        'supply_name',
        'supply_date',
        'total_amount',
        'save_template',
        'user_id', // ← ensure this is here
    ];

    protected $casts = [
        'supply_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function recipes()
    {
        return $this->hasMany(ExternalSupplyRecipe::class);
    }

    // ← add this:
    public function returnedGoods()
    {
        return $this->hasMany(ReturnedGood::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}
}
