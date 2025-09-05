<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\IncomeCategory;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'date',
        'identifier',
        'user_id',
        'income_category_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function category()
{
    return $this->belongsTo(IncomeCategory::class, 'income_category_id');
}

}
