<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDischargePaymentRecord extends Model
{
    use HasFactory;
    protected $table = 'table_patient_discharge_payment_records';
    protected $fillable = ['total_amount','paid_amount','discount','customer_id'];



     /**
     * Get the customer associated with the Patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->with('patient');
    }
}
