<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\ExternalSupply;
use App\Models\ReturnedGoodRecipe;
use App\Models\User;

class ReturnedGood extends Model
{
    protected $fillable = [
        'external_supply_id',
        'client_id',
        'return_date',
        'total_amount',
        'user_id', // ✅ Track the user who created it
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    // ✅ Relationship with the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relationship with ExternalSupply
    public function externalSupply()
    {
        return $this->belongsTo(ExternalSupply::class, 'external_supply_id');
    }

    // Related returned items
    public function recipes()
    {
        return $this->hasMany(ReturnedGoodRecipe::class);
    }

    public function lines()
    {
        return $this->hasMany(ReturnedGoodRecipe::class);
    }
}
