<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['Processing',  '#7285DF'],
            [ 'Cancelled',  '#C12D2D'],
            ['Completed', '#0ABA3B'],

        ];

        foreach ($statuses as $status) {
            Status::create([
                'title' => $status[0],
                'color' => $status[1],

            ]);
        }
    }
}
