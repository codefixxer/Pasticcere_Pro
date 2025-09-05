<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('external_supply_recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId('external_supply_id')
                  ->constrained('external_supplies')
                  ->onDelete('cascade');
            $table->foreignId('recipe_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('qty');
            // Only total_amount here:
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_supply_recipes');
    }
};
