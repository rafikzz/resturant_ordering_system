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
            'contact_information'=>'required|min:3',
            'office_location'=>'required|min:3',
            'logo'=>'mimes:jpg,jpeg,png,bmp,tiff|image|max:4096',
        ];
    }
}
