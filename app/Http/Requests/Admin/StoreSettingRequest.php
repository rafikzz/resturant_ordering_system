<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
            'company_name'=>'required|min:3',
            'contact_information'=>'required|min:3',
            'office_location'=>'required|min:3',
            'logo'=>'mimes:jpg,jpeg,png,bmp,tiff|image|max:4096',
            'tax'=>'required_with:tax_status|numeric|max:100|min:0',
            'service_charge'=>'required_with:service_charge_status|numeric|max:100|min:0',
        ];
    }
}
