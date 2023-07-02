<?php

namespace App\Http\Middleware;

use App\Api\ApiError;
use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = request()->header('Authorization');

        if (is_null($bearer) || empty($bearer) || $bearer == 'Bearer null' || $bearer == 'Bearer true') {
            throw new HttpResponseException(ApiError::message(
                498,
                'Token inválido!'
            ));
        }

        $objectForToken = JWT::decode(
            str_replace('Bearer ', '', $bearer),
            new Key(env('APP_KEY'), 'HS256')
        );

        $user = User::find($objectForToken->user->id);

        if (is_null($user)) {
            throw new HttpResponseException(ApiError::message(
                '404',
                'Usuário não encontrado!'
            ));
        }

        Auth::login($user);

        return $next($request);
    }
}