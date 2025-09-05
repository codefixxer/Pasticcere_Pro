<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecipeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Disable FK checks so we can truncate even if 'recipes' references 'recipe_categories'
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Empty out the recipe_categories table
        DB::table('recipe_categories')->truncate();

        // Re-enable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $names = [
            'Preparazioni di Base',
            'Torte da Forno',
            'Torte Classiche',
            'Torte Moderne',
            'Mignon',
            'Cioccolateria',
            'Colazione',
            'Biscotteria',
            'Monoporzioni',
            'Semifreddi',
            'Caffetteria',
            'Lievitati',
            'Panificazione',
            'Salato',
        ];

        foreach ($names as $name) {
            DB::table('recipe_categories')->insert([
                'name'       => $name,
                'user_id'    => null,
                'status'     => 'Default',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
