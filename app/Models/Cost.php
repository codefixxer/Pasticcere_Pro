<?php

        namespace App\Models;

        use App\Models\CostCategory;
        use App\Models\User;
        use Illuminate\Database\Eloquent\Model;

        class Cost extends Model
        {
            protected $fillable = [
                'supplier',
                'cost_identifier',
                'amount',
                'due_date',
                'category_id',
                'other_category',
                'user_id', // ✅ add this
            ];
    protected $casts = [
        'due_date' => 'date',
    ];
    


    // ✅ Each cost belongs to a category
            public function category()
            {
                return $this->belongsTo(CostCategory::class);
            }

            // ✅ Each cost is created by a user
            public function user()
            {
                return $this->belongsTo(User::class);
            }
        }
