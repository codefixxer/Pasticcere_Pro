<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class ExternalSupplySeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Ensure superadmin exists
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // use secure password in production
            ]
        );

        $userId = $superadmin->id;

        $client1 = DB::table('clients')->first()?->id;
        $client2 = DB::table('clients')->skip(1)->first()?->id;

        if (!$client1 || !$client2) return;

        DB::table('external_supplies')->insert([
            [
                'client_id' => $client1,
                'user_id' => $userId,
                'supply_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'total_amount' => 280.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $client2,
                'user_id' => $userId,
                'supply_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'total_amount' => 370.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
