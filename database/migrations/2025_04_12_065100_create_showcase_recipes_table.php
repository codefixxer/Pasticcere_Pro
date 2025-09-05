<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowcaseRecipesTable extends Migration
{
    public function up()
    {
        Schema::create('showcase_recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId('showcase_id')->constrained('showcases')->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            // $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('sold')->default(0);
            $table->integer('reuse')->default(0);
            $table->integer('waste')->default(0);
            $table->decimal('potential_income', 10, 2)->default(0);
            $table->decimal('actual_revenue', 10, 2)->default(0);
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('showcase_recipes');
    }
}
