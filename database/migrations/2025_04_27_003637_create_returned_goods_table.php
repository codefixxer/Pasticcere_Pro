<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Modify the returned_goods table to add external_supply_id
        Schema::create('returned_goods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('external_supply_id') // Add external_supply_id here
                  ->constrained('external_supplies')  // References the external_supplies table
                  ->cascadeOnDelete();
            $table->date('return_date');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        // The returned_good_recipes table will also reference returned_goods
        Schema::create('returned_good_recipes', function (Blueprint $table) {
            $table->id();

            // Link back to the returned_goods master record
            $table->foreignId('returned_good_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Link to the specific external supply line being returned
            $table->foreignId('external_supply_recipe_id')
                  ->constrained()              // assumes table name = external_supply_recipes
                  ->cascadeOnDelete();

            // The unit price, quantity returned, and line total
            $table->decimal('price',        10, 2);
            $table->integer('qty');
            $table->decimal('total_amount', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returned_good_recipes');
        Schema::dropIfExists('returned_goods');
    }
};
