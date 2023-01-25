<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'name'=>'Walk-in Customer',
            'customer_type_id'=>1,
            'phone_no'=>'N/A',
        ]);
    }
}
