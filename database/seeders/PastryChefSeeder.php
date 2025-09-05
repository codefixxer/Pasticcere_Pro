<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PastryChefSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks so we can truncate even if other tables reference pastry_chefs
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Empty out the pastry_chefs table
        DB::table('pastry_chefs')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // (Intentionally leave table empty; no inserts)
    }
}
