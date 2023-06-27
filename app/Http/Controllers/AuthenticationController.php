<?php

namespace App\Http\Controllers;

use App\Api\ApiSuccess;
use App\Http\Requests\UserRegisterRequest;
use App\Repositories\UserRepository;
use App\Services\TokenJWT;
use Firebase\JWT\JWT;

class AuthenticationController extends Controller
{
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
            ['token' => (new TokenJWT(new JWT))->generateByUser($user)]
        );
    }
}
