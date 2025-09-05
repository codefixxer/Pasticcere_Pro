<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'manage-users',
            'view users',
            'view roles',
            'view permissions',
            'ingredients',
            'sale comparison',
            'recipe',
            'external supplies',
            'returned goods',
            'recipe categories',
            'clients',
            'cost categories',
            'departments',
            'pastry chefs',
            'equipment',
            'showcase',
            'costs',
            'income',
            'cost comparison',
            'news',
            'production',
            'labor cost',
            'can add admin',
            'blogs',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
        }
    }
}
