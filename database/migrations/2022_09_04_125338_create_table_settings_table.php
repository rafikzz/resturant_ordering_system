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
            $table->string('bill_no_prefix')->nullable();
            $table->decimal('tax',10,2)->nullable()->default(0);
            $table->decimal('service_charge',10,2)->nullable()->default(0);
            $table->boolean('tax_status')->nullable()->default(0);
            $table->boolean('service_charge_status')->nullable()->default(0);
            $table->decimal('delivery_charge',10,2)->nullable()->default(0);
            $table->boolean('delivery_charge_status')->nullable()->default(0);
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
