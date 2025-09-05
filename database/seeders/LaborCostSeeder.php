<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaborCost;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LaborCostSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ✅ Ensure superadmin exists
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // secure this in production
            ]
        );

        LaborCost::updateOrCreate(
            ['user_id' => $superadmin->id], // uniquely match on user
            [
                'num_chefs'             => 1,
                'opening_days'          => 22,
                'hours_per_day'         => 8,
                'electricity'           => 0.00,
                'ingredients'           => 0.00,
                'leasing_loan'          => 0.00,
                'packaging'             => 0.00,
                'owner'                 => 0.00,
                'van_rental'            => 0.00,
                'chefs'                 => 0.00,
                'shop_assistants'       => 0.00,
                'other_salaries'        => 0.00,
                'taxes'                 => 0.00,
                'other_categories'      => 0.00,
                'driver_salary'         => 0.00,
                'monthly_bep'           => 0.00,
                'daily_bep'             => 0.00,
                'shop_cost_per_min'     => 0.0000,
                'external_cost_per_min' => 0.0000,
                'user_id'               => $superadmin->id,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]
        );
    }
}
