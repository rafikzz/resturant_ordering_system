<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustomerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_customer_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_creditable')->default(0);
            $table->boolean('can_use_coupon')->default(0);
            $table->boolean('is_default')->default(0);
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
        Schema::dropIfExists('table_customer_types');
    }
}
