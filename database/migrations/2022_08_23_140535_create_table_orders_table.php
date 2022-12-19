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
            $table->boolean('is_credit')->nullable();
            $table->decimal('tax',10,2)->nullable();
            $table->decimal('service_charge',10,2)->nullable();
            $table->decimal('delivery_charge',10,2)->nullable();
            $table->decimal('net_total',10,2)->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_delivery')->nullable()->default(0);
            $table->softDeletes();
            $table->boolean('guest_menu')->nullable();
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
