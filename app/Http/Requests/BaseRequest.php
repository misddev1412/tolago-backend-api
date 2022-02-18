<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use Illuminate\Validation\ValidationException;
use Response as BaseResponse;
class BaseRequest extends FormRequest
{
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors()->getMessages();
        $message = '';
        foreach ($errors as $key => $value) {
            $message .= $value[0] . ' ';
        }
        $response = BaseResponse::json(Response::generateResponse(HttpStatusCode::BAD_REQUEST, $message));
        throw new ValidationException($validator, $response);
    }
}
