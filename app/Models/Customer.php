<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'table_customers';

    protected $fillable = ['name', 'phone_no'];

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
    public function cusomter_wallet_transaction()
    {
        return $this->hasMany(CustomerWalletTransaction::class, 'customer_id');
    }

    public function wallet_balance()
    {
        if($this->cusomter_wallet_transaction()->count())
        {
            return $this->cusomter_wallet_transaction()->latest()->first()->current_amount;
        }else{
            return 0;
        }
    }
}
