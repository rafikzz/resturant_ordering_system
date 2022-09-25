<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentTypes= ['Cash','Bank','Customer Account'];

        foreach ($paymentTypes as $paymentType) {
            PaymentType::create([
                'name'=>$paymentType
            ]);
        }
    }
}
