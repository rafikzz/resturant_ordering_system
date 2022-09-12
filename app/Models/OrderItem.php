<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ='table_cart_items';

    protected $fillable =['item_id','price','order_id','quantity','deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id')->withTrashed();
    }
}
