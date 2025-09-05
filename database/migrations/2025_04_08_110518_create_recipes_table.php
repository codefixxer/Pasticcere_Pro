<?php
// database/migrations/2025_04_27_000000_create_recipes_and_pivot.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('recipe_name');

            $table->foreignId('recipe_category_id')
                  ->constrained('recipe_categories')
                  ->cascadeOnDelete();

            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->cascadeOnDelete();

                   $table->decimal('unit_ing_cost', 10, 2)
                  ->default(0);

            $table->enum('sell_mode', ['piece','kg'])->default('kg');
            $table->decimal('selling_price_per_piece', 10, 2)->nullable();
            $table->decimal('selling_price_per_kg',    10, 2)->nullable();

            $table->integer('labour_time_min')->default(0);

            $table->unsignedBigInteger('labor_cost_id');
            $table->foreign('labor_cost_id')
                  ->references('id')->on('labor_costs')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->enum('labor_cost_mode', ['shop','external'])->default('shop');

            $table->decimal('packing_cost',             8, 2)->default(0);
            $table->decimal('total_expense',           12, 2)->default(0);
            $table->decimal('potential_margin',        12, 2)->default(0);
                    $table->decimal('potential_margin_pct', 5, 2)->default(0);;

            $table->decimal('total_pieces',           10, 2)->nullable();
            $table->integer('recipe_weight')->nullable();
            $table->decimal('production_cost_per_kg',  10, 2)->nullable();
            $table->integer('add_as_ingredient')->nullable();
            $table->integer('vat_rate')->nullable();

            $table->timestamps();
        });

        Schema::create('recipe_ingredient', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recipe_id')
                  ->constrained('recipes')
                  ->cascadeOnDelete();

            $table->foreignId('ingredient_id')
                  ->constrained('ingredients')
                  ->restrictOnDelete();

            $table->integer('quantity_g')->default(0);
            // $table->decimal('cost', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredient');
        Schema::dropIfExists('recipes');
    }
};