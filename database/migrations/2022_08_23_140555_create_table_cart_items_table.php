<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('table_orders')->onDelete('cascade')->onUpdate('cascade');;
            $table->foreignId('item_id')->constrained('table_items')->onDelete('cascade')->onUpdate('cascade');;
            $table->decimal('price',10,2);
            $table->integer('quantity');
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
        Schema::dropIfExists('table_cart_items');
    }
}
