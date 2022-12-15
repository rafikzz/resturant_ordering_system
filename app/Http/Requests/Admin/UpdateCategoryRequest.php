<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'title'=>'required|min:3|unique:table_categories,title,'.$this->category->id,
            'image'=>'nullable|mimes:jpg,jpeg,png,bmp,tiff|image|max:4096',
            'coupon_discount_percentage'=>'required|numeric|min:0|max:100',
            'order'=>'required'
        ];
    }
}
