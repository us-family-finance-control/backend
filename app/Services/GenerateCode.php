<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class GenerateCode
{
    public function forVerifyEmail(): string
    {
        do {
            $code = strtoupper(Str::random(5));
        } while (User::where('email_verify_code', $code)->exists());

        return $code;
    }
}
