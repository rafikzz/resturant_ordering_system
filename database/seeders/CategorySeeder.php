<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'BreakFast',
            'Tiffin/Snacks',
            'Roti',
            'Curry',
            'Momo:',
            'Chowmein',
            'Instant Wai Wai Noddles',
            'Fried Rice',
            'Beverages',
            'Khana',
         ];

         foreach ($categories as $category) {
              Category::create(['title' => $category,
                    'status'=>1]);
         }
    }
}


