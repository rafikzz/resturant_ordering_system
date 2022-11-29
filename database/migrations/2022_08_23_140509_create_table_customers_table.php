<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_type_id')->nullable()->constrained('table_customer_types');
            $table->string('name');
            $table->string('phone_no')->nullable();
            $table->decimal('balance',10,2)->nullable()->default(0);
            $table->boolean('status')->nullable()->default(1);
            $table->boolean('is_staff')->nullable();
            $table->boolean('creditable')->nullable();
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
        Schema::dropIfExists('table_customers');
    }
}
