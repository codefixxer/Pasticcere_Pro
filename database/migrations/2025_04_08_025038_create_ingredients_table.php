<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
{
    Schema::create('ingredients', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('recipe_id')->nullable();

        $table->string('ingredient_name');
        $table->decimal('price_per_kg', 8, 2);
        $table->timestamps();
    });
    
}

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
