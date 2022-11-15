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
        return [
           'destination'=>'nullable',
           'destination_no'=>'nullable',
           'customer_id'=>'required_without:customer_name',
           'customer_type'=>'nullable',
           'customer_name'=>'required_without:customer_id',
           'customer_phone_no'=>['required_without:customer_id'],
           'discount'=>'nullable',

        ];
    }
}
