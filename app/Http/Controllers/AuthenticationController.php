<?php

namespace App\Http\Controllers;

use App\Api\ApiSuccess;
use App\Http\Requests\UserRegisterRequest;
use App\Repositories\UserRepository;
use App\Services\TokenJWT;
use Firebase\JWT\JWT;

class AuthenticationController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $user = (new UserRepository())->create($request);

        return ApiSuccess::response(
            201,
            'Usuário criado com sucesso!',
            ['token' => (new TokenJWT(new JWT))->generateByUser($user)]
        );
    }
}
