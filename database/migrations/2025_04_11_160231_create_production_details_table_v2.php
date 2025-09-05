<?php
// database/migrations/xxxx_xx_xx_create_production_details_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionDetailsTableV2 extends Migration
{
    public function up()
    {
        Schema::create('production_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId('production_id')->constrained('productions')->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('pastry_chef_id')->constrained('pastry_chefs')->onDelete('cascade');
            $table->integer('quantity');
            $table->string('execution_time');
            $table->string('equipment_ids')->nullable(); // not json
            $table->decimal('potential_revenue', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_details');
    }
}
