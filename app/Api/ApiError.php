<?php

namespace App\Api;

use Illuminate\Contracts\Support\MessageBag;

class ApiError
{
    public static function validate(string $message, MessageBag $errors)
    {
        return response()->json([
            'code' => 403,
            'message' => $message,
            'errors' => $errors
        ], 403);
    }

    public static function message(int $code, string $message)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
        ], $code);
    }
}
