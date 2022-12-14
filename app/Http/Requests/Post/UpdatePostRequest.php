<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\BaseRequest;

class UpdatePostRequest extends BaseRequest
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
            //rules for post model
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'max:10000|mimes:jpeg,jpg,png',
            'status' => 'required|integer',
            'meta_title' => 'string|max:255',
            'meta_description' => 'string|max:255',
            'meta_keywords' => 'string|max:255',
            'featured' => 'required|boolean',
        ];
    }

}
