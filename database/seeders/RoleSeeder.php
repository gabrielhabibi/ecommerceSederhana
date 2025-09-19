<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        Role::updateOrCreate(
            ['role_name' => 'super admin'], // dicari berdasarkan role_name
            ['permissions' =>  [
            'Dashboard',
            'Categories',
            'Product',
            'Users',
            'Admins',
            'Orders',
            'Setting',
            'Role',
        ]]
        );

        // Admin
        Role::updateOrCreate(
            ['role_name' => 'admin'],
            ['permissions' => ['Dashboard', 'Categories']]
        );

        // User
        Role::updateOrCreate(
            ['role_name' => 'user'],
            ['permissions' => []]
        );
    }
}
