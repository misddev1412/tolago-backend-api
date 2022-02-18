<?php

namespace App\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class TranslationPostRequest extends BaseRequest
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
            //rules for translation post request
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'locale' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255'
        ];
    }
}
