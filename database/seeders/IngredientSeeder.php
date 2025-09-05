<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class IngredientSeeder extends Seeder
{
    public function run()
    {
        // ✅ Ensure superadmin user exists
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // use strong password in production
            ]
        );

        $userId = $superadmin->id;

        DB::table('ingredients')->insert([
            ['ingredient_name' => 'Milk', 'price_per_kg' => 50.00, 'user_id' => $userId, 'created_at' => now(), 'updated_at' => now()],
            ['ingredient_name' => 'Cream', 'price_per_kg' => 100.00, 'user_id' => $userId, 'created_at' => now(), 'updated_at' => now()],
            ['ingredient_name' => 'Sugar', 'price_per_kg' => 100.00, 'user_id' => $userId, 'created_at' => now(), 'updated_at' => now()],
            ['ingredient_name' => 'Flour', 'price_per_kg' => 30.00, 'user_id' => $userId, 'created_at' => now(), 'updated_at' => now()],
            ['ingredient_name' => 'Mozzarella Cheese', 'price_per_kg' => 150.00, 'user_id' => $userId, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
