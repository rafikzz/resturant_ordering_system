<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
        $rules = [
            'customer_id' => 'required_without:customer_name',
            'customer_name' => 'required_without:customer_id',
            'customer_phone_no' => ['required_without:customer_id'],
            // 'patient_register_no' => ['required_without:customer_id','required_if:custoemr_type,3'],
            'payment_type' => 'required',
            'discount' => 'nullable',
            'destination' => 'nullable',
            'destination_no' => 'nullable',
        ];
        if (request('customer_type') == 3) {
            $rules['patient_register_no'] = 'required_without:customer_id|unique:table_patients,register_no';
        } else if (request('customer_type') == 2) {
            $rules['code'] = 'required_without:customer_id|unique:table_staffs,code';
            $rules['department_id'] = 'required_without:customer_id';
        }
        return $rules;
    }
}
