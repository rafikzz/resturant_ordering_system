<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules=   [
            'destination'=>'nullable',
            'destination_no'=>'nullable',
            'customer_id'=>'required_without:customer_name',
            'customer_type'=>'required',
            'customer_name'=>'required_without:customer_id',
            'customer_phone_no'=>['required_without:customer_id'],
            'patient_register_no' => '',
            'discount'=>'nullable',
            'payment_type'=>'required',


         ];
         if(request('customer_type') ==3)
         {
            $rules['patient_register_no']='required_without:customer_id|unique:table_patients,register_no';

         }
        return $rules;
    }
}
