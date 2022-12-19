<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $table = 'table_departments';

    protected $fillable = ['name'];

    public function customers()
    {
        return $this->hasMany(Customer::class,'customer_id');
    }

}
