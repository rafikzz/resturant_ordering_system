<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::create([
            'name' => 'tea', 'status' => 1, 'price' => 25, 'category_id' => 2
        ]);

        Item::create([
            'name' => 'coke', 'status' => 1, 'price' => 55, 'category_id' => 2
        ]);

        Item::create([
            'name' => 'burger', 'status' => 1, 'price' => 255, 'category_id' => 1
        ]);


        Item::create([
            'name' => 'mushroom stick', 'status' => 1, 'price' => 66, 'category_id' => 3
        ]);
    }
}
