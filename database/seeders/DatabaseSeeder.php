<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();
        $this->call([
            CreateRolePermissionSeeder::class,
            CreateUserSeeder::class,
            StatusSeeder::class,
            CategorySeeder::class,
            ItemSeeder::class,
            SettingSeeder::class,
            PaymentTypeSeeder::class,
            TransactionTypeSeeder::class,
            CustomerTypeSeeder::class,
            CustomerSeeder::class

        ]);
    }
}
