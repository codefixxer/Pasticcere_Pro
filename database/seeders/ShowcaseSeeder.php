<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class ShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Ensure superadmin user exists
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        $userId = $superadmin->id;

        $dept1 = DB::table('departments')->first()?->id;
        $dept2 = DB::table('departments')->skip(1)->first()?->id;

        if (!$dept1 || !$dept2) return;

        DB::table('showcases')->insert([
            [
               
                'user_id'                   => $userId,
                'showcase_date'             => Carbon::now()->subDays(1)->format('Y-m-d'),
                'template_action'           => 'daily',
                'break_even'                => 500.00,
                'total_revenue'             => 600.00,
                'plus'                      => 100.00,
                'real_margin'               => 15.5,
                'potential_income_average'  => 650.00,
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
            [
                
                'user_id'                   => $userId,
                'showcase_date'             => Carbon::now()->format('Y-m-d'),
                'template_action'           => 'none',
                'break_even'                => 400.00,
                'total_revenue'             => 420.00,
                'plus'                      => 20.00,
                'real_margin'               => 10.0,
                'potential_income_average'  => 450.00,
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
        ]);
    }
}
