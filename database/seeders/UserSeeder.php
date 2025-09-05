<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'super',       'email' => 'super@example.com',   'password' => 'password123', 'role' => 'super'],
            ['name' => 'shop_user',   'email' => 'shop@example.com',    'password' => 'password123', 'role' => 'shop'],
            ['name' => 'lab_user',    'email' => 'lab@example.com',     'password' => 'password123', 'role' => 'lab'],
            ['name' => 'master_user', 'email' => 'master@example.com',  'password' => 'password123', 'role' => 'master'],
            ['name' => 'admin_user',  'email' => 'admin@example.com',   'password' => 'password123', 'role' => 'admin'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make($data['password']),
                ]
            );

            // sync exactly one role; now guaranteed to exist
            $user->syncRoles($data['role']);
        }
    }
}
