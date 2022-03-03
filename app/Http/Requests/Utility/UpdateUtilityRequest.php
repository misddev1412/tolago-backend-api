<?php

namespace App\Http\Requests\Utility;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class UpdateUtilityRequest extends BaseRequest
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
            'name' => 'required|string|max:255',
            'description' => 'max:500',
            'status' => 'required|integer|in:0,1',
            'type' => 'required|string|in:hotel,room,restaurant,service,other',
        ];
    }
}
