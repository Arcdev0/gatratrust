<?php

namespace Database\Seeders; // <- penting!

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAndRoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['name' => 'Admin'],
            ['name' => 'Admin']
        );

        $clientRole = Role::updateOrCreate(
            ['name' => 'Client'],
            ['name' => 'Client']
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('passsword'),
                'role_id' => $adminRole->id,
                'company' => 'PT. Gatra Perdana Trustrue',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'role_id' => $clientRole->id,
                'company' => 'ClientCorp',
            ]
        );
    }
}
