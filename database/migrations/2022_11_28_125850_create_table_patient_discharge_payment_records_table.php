<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePatientDischargePaymentRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_patient_discharge_payment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('table_customers');
            $table->decimal('total_amount',10,2);
            $table->decimal('paid_amount',10,2);
            $table->decimal('discount',10,2)->default(0);
            $table->timestamp('discharged_time')->nullable();
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
        Schema::dropIfExists('table_patient_discharge_payment_records');
    }
}
