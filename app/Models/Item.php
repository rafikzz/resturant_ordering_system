<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ='table_items';
    protected $fillable = ['name','status','price','guest_price','category_id','image','for_staff'];

    public function image()
    {

        return (Storage::disk('public')->exists($this->image)) ? '/storage/' . $this->image : asset('assets/img/product/noimage.jpg');
    }

    /**
     * Get the category that owns the Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->withTrashed();
    }

    // public function setNameAttribute($value)
    // {

    //     $this->attributes['name'] = strtolower($value);
    // }

    public function getNameAttribute($value)
    {
        return  ucwords($value);
    }
    /**
     * Get all of the order_items for the Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_items()
    {
        return $this->hasMany(OrderItem::class, 'item_id');
    }
}
