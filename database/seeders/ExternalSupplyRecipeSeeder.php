<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class ExternalSupplyRecipeSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Ensure superadmin exists
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        $userId = $superadmin->id;

        $supply1 = DB::table('external_supplies')->first()?->id;
        $supply2 = DB::table('external_supplies')->skip(1)->first()?->id;

        $recipe1 = DB::table('recipes')->first()?->id;
        $recipe2 = DB::table('recipes')->skip(1)->first()?->id;

        if (!$supply1 || !$supply2 || !$recipe1 || !$recipe2) return;

        DB::table('external_supply_recipes')->insert([
            [
                'external_supply_id' => $supply1,
                'recipe_id' => $recipe1,
                'category' => 'Dessert',
                'price' => 70.00,
                'qty' => 2,
                'total_amount' => 140.00,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'external_supply_id' => $supply1,
                'recipe_id' => $recipe2,
                'category' => 'Base',
                'price' => 70.00,
                'qty' => 2,
                'total_amount' => 140.00,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'external_supply_id' => $supply2,
                'recipe_id' => $recipe1,
                'category' => 'Dessert',
                'price' => 90.00,
                'qty' => 3,
                'total_amount' => 270.00,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'external_supply_id' => $supply2,
                'recipe_id' => $recipe2,
                'category' => 'Base',
                'price' => 50.00,
                'qty' => 2,
                'total_amount' => 100.00,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
