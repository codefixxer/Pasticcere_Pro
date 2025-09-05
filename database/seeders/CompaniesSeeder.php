<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CompaniesSeeder extends Seeder
{
    public function run()
    {
        // ✅ Ensure superadmin user exists (create if not)
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Use a secure password in real apps
            ]
        );

        // ✅ Now safely use its ID
        Company::firstOrCreate(
            ['id' => 1],
            ['name' => 'SuperAdmin', 'user_id' => $superadmin->id]
        );

        Company::firstOrCreate(
            ['id' => 2],
            ['name' => 'Acme Bakery', 'user_id' => $superadmin->id]
        );
    }
}
