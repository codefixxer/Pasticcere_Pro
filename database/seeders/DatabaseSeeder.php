<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\EquipmentSeeder;
use Database\Seeders\LaborCostSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\IngredientSeeder;
use Database\Seeders\PastryChefSeeder;
use Database\Seeders\CostCategorySeeder;
use Database\Seeders\RecipeCategorySeeder;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

class DatabaseSeeder extends Seeder
{
 
    public function run()
    {
        $this->call([
                                     RecipeCategorySeeder::class,
            ClientSeeder::class,     
            CostCategorySeeder::class,     
            DepartmentSeeder::class,     
            PastryChefSeeder::class,     

            PermissionsTableSeeder::class,        
            RolesAndPermissionsSeeder::class,       
            UserSeeder::class, 

            IngredientSeeder::class,  
            LaborCostSeeder::class, 
            EquipmentSeeder::class,     


            RecipeSeeder::class,
            RecipeIngredientSeeder::class,



            ExternalSupplySeeder::class,
            ExternalSupplyRecipeSeeder::class,

            ShowcaseSeeder::class,
            ShowcaseRecipeSeeder::class,


        ]);        
    }
}
