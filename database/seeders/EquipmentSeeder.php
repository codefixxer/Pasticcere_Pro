<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('equipment')->truncate();

        $names = [
            'Planetaria',
            'Tuffante',
            'Forno',
            'Abbattitore',
            'Sfogliatrice',
            'Raffinatrice',
            'Temperatrice',
        ];

        foreach ($names as $name) {
            DB::table('equipment')->insert([
                'name'       => $name,
                'user_id'    => null,
                                'status'     => 'Default',

                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
