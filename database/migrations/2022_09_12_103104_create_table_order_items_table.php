<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('table_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_id')->constrained('table_items')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('price',10,2);
            $table->integer('order_no');
            $table->integer('quantity');
            $table->integer('removed_quantity')->nullable()->default(0);
            $table->integer('total');
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
        Schema::dropIfExists('table_order_items');
    }
}
