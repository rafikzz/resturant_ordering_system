<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    use HasFactory;
    protected $table = 'table_customer_types';

    protected $fillable = ['name','is_creditable','can_use_coupon','is_default'];

}
