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
            $table->string('bill_no');
            $table->string('table_no')->nullable();
            $table->decimal('discount',10,2)->nullable()->default(0);
            $table->decimal('total',10,2);
            $table->timestamp('order_datetime');
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
