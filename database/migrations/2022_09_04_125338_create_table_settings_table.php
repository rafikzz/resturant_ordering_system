<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('logo')->nullable();
            $table->text('contact_information')->nullable();
            $table->text('office_location')->nullable();
            $table->string('tax')->nullable()->default(0);
            $table->string('service_charge')->nullable()->default(0);
            $table->boolean('tax_status')->nullable()->default(0);
            $table->boolean('service_charge_status')->nullable()->default(0);
            $table->boolean('enable_kot')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_settings');
    }
}
