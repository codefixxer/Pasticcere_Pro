<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowcasesTable extends Migration
{
    public function up()
    {
        Schema::create('showcases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('showcase_name')->nullable();
            
            $table->boolean('save_template')->default(false)->nullable();
            $table->date('showcase_date');
            $table->string('template_action')->default('none');
            $table->decimal('break_even', 10, 2)->default(0);
            $table->decimal('total_revenue', 10, 2)->nullable();
            $table->decimal('plus', 10, 2)->nullable();
            $table->decimal('real_margin', 8, 2)->nullable();
            $table->decimal('potential_income_average', 10, 2)->nullable();
            
          
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('showcases');
    }
}
