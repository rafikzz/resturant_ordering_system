<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledOrderItem extends Model
{
    use HasFactory;

    protected $table ='table_cancelled_order_items';

    protected $fillable =['item_id','price','order_id','quantity','user_id','deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id')->withTrashed();
    }
}
