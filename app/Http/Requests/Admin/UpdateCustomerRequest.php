<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
        return [
            'name'=>'required',
            'phone_no'=>['required_without:customer_id','regex:/(?:\(?\+977\)?)?[9][6-9]\d{8}|01[-]?[0-9]{7}/','unique:table_customers,phone_no,'.$this->customer->id],
        ];
    }
}
