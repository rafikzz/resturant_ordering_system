<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'table_customers';

    protected $fillable = ['name', 'phone_no', 'balance', 'is_staff', 'room_no', 'status', 'customer_type_id'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($name)
    {
        return  ucwords($name);
    }

    /**
     * Get all of the cusomter_wallet_transaction for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cusomter_wallet_transactions()
    {
        return $this->hasMany(CustomerWalletTransaction::class, 'customer_id');
    }

    public function last_transaction()
    {
        return $this->hasOne(CustomerWalletTransaction::class)->latestOfMany()->withDefault([
            'current_amount' => 0,
        ]);;
    }

    public function getWalletBalanceAttribute()
    {
        if ($this->cusomter_wallet_transactions()->count()) {
            return $this->cusomter_wallet_transactions()->latest()->first()->current_amount;
        } else {
            return 0;
        }
    }

    public function wallet_balance()
    {
        if ($this->cusomter_wallet_transactions()->count()) {
            return $this->cusomter_wallet_transactions()->latest()->first()->current_amount;
        } else {
            return 0;
        }
    }

    /**
     * Get the customer_type that owns the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer_type()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type_id');
    }

    /**
     * The coupons that belong to the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function coupons()
    {
        return $this->belongsToMany(Role::class, 'table_orders', 'customer_id', 'coupon_id')->withPivot('id');
    }

    /**
     * Get the patient associated with the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Get all of the orders for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
