<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('discount',10,2)->nullable();
            $table->date('expiry_date');
            $table->integer('max_uses_per_day')->nullable();
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
        Schema::dropIfExists('table_coupons');
    }
}
