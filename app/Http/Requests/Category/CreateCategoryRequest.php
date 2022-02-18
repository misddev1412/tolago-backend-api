<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class CreateCategoryRequest extends BaseRequest
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
            //rules for categories model
            'name' => 'required|string|max:255',
            'parent_id' => 'required|integer|exists:categories,id',
            'type' => 'required|string|max:255',
            'image' => 'required|max:10000|mimes:jpeg,jpg,png',
            'status' => 'required|integer',
            'meta_title' => 'string|max:255',
            'meta_description' => 'string|max:255',
            'meta_keywords' => 'string|max:255',

        ];
    }
}
