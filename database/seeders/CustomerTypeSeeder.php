<?php

namespace Database\Seeders;

use App\Models\CustomerType;
use Illuminate\Database\Seeder;

class CustomerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer_types = [
            ['Walk-in Customer',  0,0,1],
            [ 'Staff',  1,1,0],
            ['Patient', 1,0,0],

        ];

        foreach ($customer_types as $customer_type) {
            CustomerType::create([
                'name' => $customer_type[0],
                'is_creditable' => $customer_type[1],
                'can_use_coupon'=>$customer_type[2],
                'is_default'=>$customer_type[3],
            ]);
        }
    }
}
