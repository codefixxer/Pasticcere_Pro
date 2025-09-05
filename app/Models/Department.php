<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Recipe;
use App\Models\User;
use App\Models\LaborCost;

class Department extends Model
{
    protected $fillable = [
        'name',
        'status',
        'share_percent', // NEW nullable % of shared costs that belongs to this department
        'user_id',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional overrides this department may have
    public function laborCosts()
    {
        return $this->hasMany(LaborCost::class);
    }
}
