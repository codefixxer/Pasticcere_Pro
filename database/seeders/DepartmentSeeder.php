<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Disable FK checks so truncate will work even if other tables reference departments
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('departments')->truncate();

        // Re-enable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $names = [
            'Pasticceria',
            'Caffetteria',
            'Panificio',
            'Aperitivi',
            'Gastronomia',
            'Gelateria',
        ];

        foreach ($names as $name) {
            DB::table('departments')->insert([
                'name'       => $name,
                'user_id'    => null,
                                'status'     => 'Default',

                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
