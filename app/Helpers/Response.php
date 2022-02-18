<?php

namespace App\Helpers;
use App\Enum\HttpStatusCode;

class Response {
    static public function generateResponse(int $status, $message = '', $data = null) {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return $response;
    }
}