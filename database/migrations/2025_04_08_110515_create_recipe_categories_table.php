<?php
// 2025_04_18_000001_create_recipe_categories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('recipe_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->unique();
            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipe_categories');
    }
}
