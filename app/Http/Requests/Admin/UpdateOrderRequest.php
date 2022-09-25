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
        return [
            'table_no'=>'required',
            'customer_id'=>'required_without:customer_name',
            'customer_name'=>'required_without:customer_id',
            'customer_phone_no'=>['required_without:customer_id','regex:/(?:\(?\+977\)?)?[9][6-9]\d{8}|01[-]?[0-9]{7}/','unique:table_customers,phone_no'],
            'discount'=>'nullable',
            'is_take_away'=>'required',
         ];
    }
}
