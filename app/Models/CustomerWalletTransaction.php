<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'table_customer_wallet_transactions';
    protected $fillable = [
        'customer_id', 'order_id','transaction_type_id' ,'previous_amount','amount','current_amount','description','author_id'
    ];
}
