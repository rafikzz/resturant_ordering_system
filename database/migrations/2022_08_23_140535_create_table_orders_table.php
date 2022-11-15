<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('table_customers');
            $table->foreignId('status_id')->constrained('table_statuses');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bill_no');
            $table->string('destination')->nullable();
            $table->string('destination_no')->nullable();
            $table->decimal('discount',10,2)->nullable()->default(0);
            $table->decimal('total',10,2);
            $table->timestamp('order_datetime')->nullable();
            $table->foreignId('payment_type_id')->nullable()->constrained('table_payment_types');
            $table->string('tax')->nullable();
            $table->string('service_charge')->nullable();
            $table->string('net_total')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('table_orders');
    }
}
