<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory;

    protected $table ='table_order_items';

    protected $fillable =['order_id','item_id','created_by','updated_by','price','total','removed_quantity','order_no','quantity'];


    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }

    public function getSubTotalAttribute()
    {
        return $this->total *$this->price;
    }

    public function order_taken_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function last_updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeCompleted($query)
    {

        return $query->where('is_completed',1);
    }

    public function getCreatedAtAttribute($data)
    {
        return Carbon::parse($data)->setTimezone('Asia/Kathmandu')->format('Y-m-d g:i a ');
    }

}
