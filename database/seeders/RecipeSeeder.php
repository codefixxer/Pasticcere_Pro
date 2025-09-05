<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\RecipeCategory;
use App\Models\Department;
use App\Models\LaborCost;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 0) Disable FK checks so we can truncate recipes even if other tables reference it
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('recipes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1) Ensure superadmin exists
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2) Ensure a LaborCost record exists (you already seed LaborCostSeeder)
        $labor = LaborCost::firstOrCreate(
            ['user_id' => $superadmin->id],
            [
                'num_chefs'             => 1,
                'opening_days'          => 22,
                'hours_per_day'         => 8,
                'electricity'           => 0,
                'ingredients'           => 0,
                'leasing_loan'          => 0,
                'packaging'             => 0,
                'owner'                 => 0,
                'van_rental'            => 0,
                'chefs'                 => 0,
                'shop_assistants'       => 0,
                'other_salaries'        => 0,
                'taxes'                 => 0,
                'other_categories'      => 0,
                'driver_salary'         => 0,
                'monthly_bep'           => null,
                'daily_bep'             => null,
                'shop_cost_per_min'     => 1.00,
                'external_cost_per_min' => 1.50,
            ]
        );
        $laborId = $labor->id;

        // 3) Lookup Italian-named recipe categories and departments
        $cakeCategoryId = RecipeCategory::where('name', 'Torte da Forno')->value('id');
        $doughCategoryId = RecipeCategory::where('name', 'Panificazione')->value('id');

        $pastryDeptId = Department::where('name', 'Pasticceria')->value('id');
        $bakeryDeptId = Department::where('name', 'Panificio')->value('id');

        // 4) Insert recipes
        DB::table('recipes')->insert([
            [
                'recipe_name'             => 'Chocolate Cake',
                'recipe_category_id'      => $cakeCategoryId,
                'department_id'           => $pastryDeptId,
                'sell_mode'               => 'piece',
                'selling_price_per_piece' => 150.00,
                'selling_price_per_kg'    => null,
                'labor_cost_id'           => $laborId,
                'labour_time_min'         => 30,
                'labor_cost_mode'         => 'shop',
                'packing_cost'            => 10.00,
                'total_expense'           => 160.00,
                'potential_margin'        => -10.00,
                'total_pieces'            => 20,
                'recipe_weight'           => null,
                'production_cost_per_kg'  => null,
                'add_as_ingredient'       => 0,
                'user_id'                 => $superadmin->id,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],
            [
                'recipe_name'             => 'Pizza Dough',
                'recipe_category_id'      => $doughCategoryId,
                'department_id'           => $bakeryDeptId,
                'sell_mode'               => 'kg',
                'selling_price_per_piece' => null,
                'selling_price_per_kg'    => 90.00,
                'labor_cost_id'           => $laborId,
                'labour_time_min'         => 20,
                'labor_cost_mode'         => 'shop',
                'packing_cost'            => 5.00,
                'total_expense'           => 85.00,
                'potential_margin'        => 5.00,
                'total_pieces'            => null,
                'recipe_weight'           => 5,
                'production_cost_per_kg'  => null,
                'add_as_ingredient'       => 0,
                'user_id'                 => $superadmin->id,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],
        ]);
    }
}
