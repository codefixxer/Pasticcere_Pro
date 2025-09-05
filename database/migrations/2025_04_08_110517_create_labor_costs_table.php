<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('labor_costs', function (Blueprint $table) {
            $table->id();
$table->string('name')->nullable();                // label for this row (e.g., "Global", "Pastry override")
$table->foreignId('department_id')->nullable()    // when set => per-department override
      ->constrained('departments')
      ->nullOnDelete();
      $table->boolean('is_default')->default(false);    // true = Global shared row

            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('num_chefs')->default(1);
            $table->integer('opening_days')->default(22);
            $table->integer('hours_per_day')->default(8);

            $table->decimal('electricity',        10, 2)->default(0);
            $table->decimal('ingredients',        10, 2)->default(0);
            $table->decimal('leasing_loan',       10, 2)->default(0);
            $table->decimal('packaging',          10, 2)->default(0);
            $table->decimal('owner',              10, 2)->default(0);
            $table->decimal('van_rental',         10, 2)->default(0);
            $table->decimal('chefs',              10, 2)->default(0);
            $table->decimal('shop_assistants',    10, 2)->default(0);
            $table->decimal('other_salaries',     10, 2)->default(0);
            $table->decimal('taxes',              10, 2)->default(0);
            $table->decimal('other_categories',   10, 2)->default(0);
            $table->decimal('driver_salary',      10, 2)->default(0);

            $table->decimal('monthly_bep',           10, 2)->nullable();
            $table->decimal('daily_bep',             10, 2)->nullable();
            $table->decimal('shop_cost_per_min',     10, 4)->nullable();
            $table->decimal('external_cost_per_min', 10, 4)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_costs');
    }
};
