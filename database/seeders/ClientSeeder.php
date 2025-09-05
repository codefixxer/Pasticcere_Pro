<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK checks so truncate will work even if other tables reference clients
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Empty out clients
        DB::table('clients')->truncate();

        // Re-enable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // (No default clients to re-insert)
    }
}
