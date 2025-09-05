<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('external_supplies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId('client_id')
                  ->constrained()
                  ->onDelete('cascade');
                  $table->string('supply_name')->nullable();
            
            $table->boolean('save_template')->default(false)->nullable();
            $table->date('supply_date');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_supplies');
    }
};
