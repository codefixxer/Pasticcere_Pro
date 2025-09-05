<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('supplier');
            $table->string('cost_identifier')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->foreignId('category_id')
            ->constrained('cost_categories')
            ->onDelete('cascade');

            $table->string('other_category')->nullable();

                  $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('costs');
    }
};
