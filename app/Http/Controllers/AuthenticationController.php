<?php

namespace App\Http\Controllers;

use App\Api\ApiError;
use App\Api\ApiSuccess;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\TokenJWT;
use Firebase\JWT\JWT;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    private TokenJWT $tokenJWT;

    public function __construct()
    {
        $this->tokenJWT = (new TokenJWT(new JWT));
    }

    /**
     * @OA\Post(
     *     tags={"Autenticação"},
     *     summary="Criar novo usuário",
     *     description="Crie um novo usuário independente da hierarquia",
     *     path="/api/register",
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="É obrigatório para todas as hierarquias, e deve ser um desses: 'admin', 'manager' e 'dependent'",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="É obrigatório para todas as hierarquias",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="É obrigatório para todas as hierarquias",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="É obrigatório para todas as hierarquias",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="É obrigatório para as hierarquias: 'admin' e 'manager'",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="manager_id",
     *         in="query",
     *         description="É obrigatório para a hierarquia: 'dependent",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="É obrigatório para a hierarquia: 'manager",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="É obrigatório para a hierarquia: 'dependent",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="kinship",
     *         in="query",
     *         description="É obrigatório para a hierarquia: 'dependent",
     *         required=false,
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Erro ao validar os paramêtros enviados",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="code",
     *                         type="integer",
     *                         description="Código HTTP"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Mensagem de erro"
     *                     ),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="array",
     *                         description="Erros",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "code": 403,
     *                         "message": "Houve um ou mais erros nos paramêtros enviados!",
     *                         "data": {
     *                             "username": {
     *                                 "The username has already been taken."
     *                             },
     *                             "kinship": {
     *                                 "The kinship field is required."
     *                             }
     *                         }
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="code",
     *                         type="integer",
     *                         description="Código HTTP"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Mensagem de sucesso"
     *                     ),
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         description="Dados do usuário criado",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "code": 201,
     *                         "message": "Usuário criado com sucesso!",
     *                         "data": {
     *                             "token": "eyDSA8dDSAD7ASOfsdI0da0..."
     *                         }
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     * ),
     * 
    */
    public function register(UserRegisterRequest $request)
    {
        $user = (new UserRepository())->create($request);

        return ApiSuccess::response(
            201,
            'Usuário criado com sucesso!',
            ['token' => $this->tokenJWT->generateByUser($user)]
        );
    }

    public function login(UserLoginRequest $request)
    {
        $user = User::where(function ($q) use ($request) {
            $q->where('email', $request->emailOrUsername)
                ->orWhere('username', $request->emailOrUsername)
            ;
        })->first();

        if (is_null($user) || !Hash::check($request->password, $user->password)) {
            throw new HttpResponseException(ApiError::message(
                404,
                'Falha na autenticação, verifique os dados informados!'
            ));
        }

        return ApiSuccess::response(
            200,
            'Login realizado com sucesso!',
            ['token' => $this->tokenJWT->generateByUser($user)]
        );
    }
}
