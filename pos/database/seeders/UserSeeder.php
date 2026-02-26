<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     *
     * @return void
     */

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Role',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('adminq1w2e3'),
            'role' => 'admin'

        ]);
        $admin->assignRole('admin');

        $user = User::create([
            'name' => 'User Role',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('userq1w2e3'),
            'role' => 'user'

        ]);

        $user->assignRole('user');
    }
}
