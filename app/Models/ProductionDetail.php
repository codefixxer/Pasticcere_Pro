<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Production;
use App\Models\Recipe;
use App\Models\PastryChef;

class ProductionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'recipe_id',
        'pastry_chef_id',
        'quantity',
        'execution_time',
        'equipment_ids',
        'potential_revenue',
        'user_id', // ✅ Add user_id to track owner
    ];

    protected $casts = [
        'equipment_ids' => 'array', // ✅ Automatically cast JSON to array
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function chef()
    {
        return $this->belongsTo(PastryChef::class, 'pastry_chef_id');
    }

    // ✅ Link to user who created the detail
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
