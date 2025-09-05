<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use App\Models\User;
    use App\Models\Recipe;
    use App\Models\Ingredient;

    class RecipeIngredient extends Model
    {
        use HasFactory;

        protected $table = 'recipe_ingredient';

        protected $fillable = [
            'recipe_id',
            'ingredient_id',
            'quantity_g',
            // 'cost',
            'user_id', // ✅ Add user_id for ownership
        ];

        /**
         * The recipe this belongs to.
         */
        public function recipe()
        {
            return $this->belongsTo(Recipe::class);
        }

        /**
         * The ingredient this refers to.
         */
        public function ingredient()
        {
            return $this->belongsTo(Ingredient::class);
        }

        // ✅ User who added this ingredient
        public function user()
        {
            return $this->belongsTo(User::class);
        }
    }
