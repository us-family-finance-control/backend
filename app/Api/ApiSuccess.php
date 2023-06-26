<?php

namespace App\Api;

class ApiSuccess
{
    public static function response(int $code, string $message, array $response)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $response
        ], $code);
    }
}
