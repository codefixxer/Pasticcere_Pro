<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CostCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Disable foreign key checks so we can truncate even if 'costs' references 'cost_categories'
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('cost_categories')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $names = [
            'Energia Elettrica',
            'Materie Prime',
            'TFR',
            'Tasse',
            'Stipendi',
            'Affitto/mutuo',
            'Noleggi',
            'Acqua',
            'Gas',
            'Packaging',
            'Marketing',
            'Altro',
        ];

        foreach ($names as $name) {
            DB::table('cost_categories')->insert([
                'name'       => $name,
                'user_id'    => null,
                                'status'     => 'Default',

                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
