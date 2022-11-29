<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponIdToTableOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->constrained('table_coupons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('table_orders', 'coupon_id')) {
            Schema::table('table_orders', function (Blueprint $table) {
                $table->dropForeign(['coupon_id']);
            });
        }
    }
}
