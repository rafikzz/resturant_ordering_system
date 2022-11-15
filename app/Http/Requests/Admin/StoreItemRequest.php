<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
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
            'name'=>'required|min:3',
            'image'=>'nullable|mimes:jpg,jpeg,png,bmp,tiff|image',
            'price'=>'required|min:0',
            'category_id'=>'required|exists:table_categories,id',
        ];
    }
}
