<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenJWT
{
    /**
     * @var JWT
     */
    private $jwt;

    /**
     * TokenJwt constructor.
     */
    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * Generate a JWT Token string.
     *
     * @param User
     *
     * @return string
     */
    public function generateByUser(User $user)
    {
        $params = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'is_admin' => $user->getType() == User::TYPE_ADMIN,
                'is_manager' => $user->getType() == User::TYPE_MANAGER,
                'is_dependent' => $user->getType() == User::TYPE_DEPENDENT,
            ],
        ];

        return $this->jwt->encode(
            array_merge($params, ['iss' => url('/'), 'iat' => time()]), 
            env('APP_KEY'),
            'HS256'
        );
    }

    public function getUserByToken(string $token = null): User
    {
        if (is_null($token) || empty($token) || $token == 'Bearer null' || $token == 'Bearer true') {
            throw new \Exception('Token não encontrado!');
        }

        $obj = $this->jwt->decode(
            str_replace('Bearer ', '', $token), 
            new Key(env('APP_URL'), 'HS256')
        );

        return User::findOrFail($obj->user->id);
    }
}