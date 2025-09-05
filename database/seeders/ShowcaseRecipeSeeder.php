<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ShowcaseRecipeSeeder extends Seeder
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

        $showcase1 = DB::table('showcases')->first()?->id;
        $showcase2 = DB::table('showcases')->skip(1)->first()?->id;

        $recipe1 = DB::table('recipes')->first()?->id;
        $recipe2 = DB::table('recipes')->skip(1)->first()?->id;

        if (!$showcase1 || !$showcase2 || !$recipe1 || !$recipe2) return;

        DB::table('showcase_recipes')->insert([
            [
                'showcase_id'      => $showcase1,
                'recipe_id'        => $recipe1,
                'category'         => 'Dessert',
                'price'            => 50.00,
                'quantity'         => 10,
                'sold'             => 8,
                'reuse'            => 1,
                'waste'            => 1,
                'potential_income' => 500.00,
                'actual_revenue'   => 400.00,
                'user_id'          => $userId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'showcase_id'      => $showcase1,
                'recipe_id'        => $recipe2,
                'category'         => 'Base',
                'price'            => 60.00,
                'quantity'         => 5,
                'sold'             => 5,
                'reuse'            => 0,
                'waste'            => 0,
                'potential_income' => 300.00,
                'actual_revenue'   => 300.00,
                'user_id'          => $userId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'showcase_id'      => $showcase2,
                'recipe_id'        => $recipe1,
                'category'         => 'Dessert',
                'price'            => 55.00,
                'quantity'         => 6,
                'sold'             => 4,
                'reuse'            => 1,
                'waste'            => 1,
                'potential_income' => 330.00,
                'actual_revenue'   => 220.00,
                'user_id'          => $userId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}
