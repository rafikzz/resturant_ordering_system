<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustomerWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_customer_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('table_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('customer_id')->constrained('table_customers')->onDelete('cascade');
            $table->foreignId('transaction_type_id')->constrained('table_transaction_types')->onDelete('cascade');
            $table->decimal('previous_amount',10,2);
            $table->decimal('amount',10,2);
            $table->decimal('current_amount',10,2)->default(0);
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('table_customer_wallet_transactions');
    }
}
