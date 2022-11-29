<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory;

    protected $table ='table_settings';

    protected $fillable =['company_name','logo','contact_information','office_location','tax','tax_status','service_charge','service_charge_status',
        'delivery_charge','delivery_charge_status'];


    public function logo()
    {
        return (Storage::disk('public')->exists($this->logo)) ? '/storage/' . $this->logo : asset('noimgavialable.png');
    }

    public function getTax()
    {
        if($this->tax_status)
        {
            return $this->tax;
        }else{
            return 0;
        }
    }


    public function getServiceCharge()
    {
        if($this->service_charge_status)
        {
            return $this->service_charge;
        }else{
            return 0;
        }
    }

    public function getDeliveryCharge()
    {
        if($this->delivery_charge_status)
        {
            return $this->delivery_charge;
        }else{
            return 0;
        }
    }
}
