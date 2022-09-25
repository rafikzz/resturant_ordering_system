<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tansaction_types= [
            ['Crediting',  1],
            [ 'Deducting',  0],
            ['Order Payment', 0],

        ];

        foreach ($tansaction_types as $tansaction_type) {
            TransactionType::create([
                'name'=>$tansaction_type[0],
                'is_add'=>$tansaction_type[1]
            ]);
        }
    }
}
