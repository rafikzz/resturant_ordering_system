<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    protected $table ='table_statuses';
    protected $fillable = ['title','color'];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
