<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class SignupRequest extends BaseRequest
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
            //rules for create user
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'retype_password' => 'required|same:password',
        ];
    }
}
