<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=  User::create([
            'name' => 'super',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole([1]);

        $user=    User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole([2]);

        $user=  User::create([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole([3]);

    }
}
