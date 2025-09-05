<?php
// database/migrations/xxxx_xx_xx_create_productions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration
{
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('production_name')->nullable();
            $table->boolean('save_template')->default(false)->nullable();
            $table->date('production_date');
            $table->decimal('total_potential_revenue', 10, 2)->default(0);
            
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productions');
    }
}
