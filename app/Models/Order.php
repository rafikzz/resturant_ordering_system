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
    protected $fillable = [
        'customer_id', 'status_id', 'table_no', 'created_by', 'updated_by', 'tax', 'service_charge', 'payment_type', 'bill_no', 'table_no', 'discount',
        'total', 'net_total', 'order_datetime','is_take_away','payment_type_id'
    ];


    protected $appends = ['order_date'];

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

    public function order_taken_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function last_updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    public function setTotal($user = null)
    {
        $orderTotal = $this->order_items()->sum(DB::raw('total * price'));
        $order = $this->update([
            'total' => $orderTotal,
            'updated_by' => ($user) ? $user : auth()->id(),

        ]);
        return $orderTotal;
    }

    public function getTotal($user = null)
    {

        return  $orderTotal = ($this->order_items()->count()) ? $this->order_items()->sum(DB::raw('total * price')) : 0;
    }

    public function serviceCharge($discount = 0)
    {
        $setting = Setting::first();
        if (isset($setting)) {
            return   round(($setting->getServiceCharge() / 100) * ($this->total - $discount), 2);
        }
        return 0;
    }
    public function taxAmount($discount = 0)
    {
        $setting = Setting::first();
        if (isset($setting)) {
            return   round($this->totalWithTax($discount) - $this->totalWithServiceCharge($discount), 2);
        }
        return 0;
    }

    public function totalWithServiceCharge($discount = 0)
    {
        $setting = Setting::first();
        if (isset($setting)) {
            return   round((1 + $setting->getServiceCharge() / 100) * ($this->total - $discount), 2);
        }
        return ($this->total - $discount);
    }

    public function totalWithTax($discount = 0)
    {
        $setting = Setting::first();
        if (isset($setting)) {
            return   round((1 + $setting->getTax() / 100) * ($this->totalWithServiceCharge($discount)), 2);
        }
        return $this->totalWithServiceCharge($discount);
    }
}
