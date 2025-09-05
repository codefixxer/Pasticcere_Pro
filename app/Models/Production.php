<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ProductionDetail;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_name',
        'save_template',
        'production_date',
        'total_potential_revenue',
        'user_id', // ✅ Added user_id
    ];

    public function details()
    {
        return $this->hasMany(ProductionDetail::class);
    }

    // ✅ Link to the user who created the production
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function chef()
{
    return $this->belongsTo(\App\Models\PastryChef::class, 'pastry_chef_id');
}

}
