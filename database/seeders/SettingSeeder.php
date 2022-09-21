<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
                'company_name'=>'XYZ Company',
                'contact_information'=>'9803210213',
                'office_location'=>'Sankhamul',
                'tax'=>13,
                'tax_status'=>0,
                'service_charge'=>10,
                'service_charge_status'=>0,
            ]);
    }
}
