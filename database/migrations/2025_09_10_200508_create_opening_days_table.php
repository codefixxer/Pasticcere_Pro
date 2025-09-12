<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->year('year');
            $table->unsignedTinyInteger('month'); // 1..12
            $table->unsignedTinyInteger('days')->default(0); // 0..31
            $table->timestamps();

            $table->unique(['user_id','year','month']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_days');
    }
};
