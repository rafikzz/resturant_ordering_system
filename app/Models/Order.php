<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ='table_orders';
    protected $fillable = ['customer_id','status_id','bill_no','table_no','discount','total','order_datetime'];


    protected $appends = ['net_total','order_date'];

    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class,'status_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function getNetTotalAttribute()
    {
        return ($this->total - $this->discount);
    }

    public function getOrderDateAttribute()
    {
        return Carbon::parse($this->order_datetime)->format('M d Y');
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($order) {
            $order->cart_items()->delete();
        });

        static::restoring(function($order) {
            $order->cart_items()->restore();
        });
    }
}
