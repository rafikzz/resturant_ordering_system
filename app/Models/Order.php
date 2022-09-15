<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'table_orders';
    protected $fillable = ['customer_id', 'status_id', 'table_no', 'created_by', 'updated_by', 'bill_no', 'table_no', 'discount', 'total', 'order_datetime'];


    protected $appends = ['net_total', 'order_date'];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getNetTotalAttribute()
    {
        return ($this->total - $this->discount);
    }

    public function getOrderDateAttribute()
    {
        return Carbon::parse($this->order_datetime)->format('M d Y');
    }

    public function getOrderNo()
    {
        if ($this->order_items()->count()) {
            return  $this->order_items()->max('order_no') + 1;
        } else {
            return 1;
        }
    }

    public function setTotal($user =null)
    {
        $orderTotal = $this->order_items()->sum(DB::raw('total * price'));
        $order = $this->update([
            'total' => $orderTotal,
            'updated_by' => ($user)?$user:auth()->id(),

        ]);
        return $orderTotal;
    }

    public function getTotal($user =null)
    {

        return  $orderTotal = ($this->order_items()->count())? $this->order_items()->sum(DB::raw('total * price')) : 0;
    }


}
