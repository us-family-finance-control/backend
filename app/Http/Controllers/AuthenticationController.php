<?php

namespace App\Http\Controllers;

use App\Api\ApiError;
use App\Api\ApiSuccess;
use App\Http\Requests\ConfirmEmailRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Mail\UserConfirmEmailMail;
use App\Mail\UserEmailConfirmedMail;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\GenerateCode;
use App\Services\TokenJWT;
use Firebase\JWT\JWT;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
     *                         description="Token do usuário criado",
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

    /**
     * @OA\Post(
     *     tags={"Autenticação"},
     *     summary="Realizar login",
     *     description="Realize login independente da hierarquia",
     *     path="/api/login",
     *     @OA\Parameter(
     *         name="emailOrUsername",
     *         in="query",
     *         description="É obrigatório ser um email ou um username",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="É obrigatório ser uma senha",
     *         required=true,
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
     *                             "emailOrUsername": {
     *                                 "The emailOrUsername field is required."
     *                             },
     *                             "password": {
     *                                 "The password field is required."
     *                             }
     *                         }
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Login realizado com sucesso",
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
     *                         description="Token do usuário",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "code": 201,
     *                         "message": "Login realizado com sucesso!",
     *                         "data": {
     *                             "token": "eyDSA8dDSAD7ASOfsdI0da0..."
     *                         }
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Erro na autenticação",
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
     *                     example={
     *                         "code": 404,
     *                         "message": "Falha na autenticação, verifique os dados informados!",
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     * ),
     * 
    */
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

    /**
     * @OA\Post(
     *     tags={"Autenticação"},
     *     summary="Enviar código de verificação pro email",
     *     description="Envia um código de verificação para o email para ele confirmar o email",
     *     path="/api/trigger-email-to-confirm",
     *     security={ {"BearerAuth": {}}},
     *     @OA\Response(
     *         response=409,
     *         description="O e-mail já foi confirmado",
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
     *                     example={
     *                         "code": 409,
     *                         "message": "O e-mail já foi confirmado!"
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="E-mail enviado com sucesso!",
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
     *                     example={
     *                         "code": 200,
     *                         "message": "O e-mail com o código foi enviado com sucesso!"
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=498,
     *         description="Token inválido",
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
     *                     example={
     *                         "code": 498,
     *                         "message": "Token inválido!",
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
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
     *                     example={
     *                         "code": 404,
     *                         "message": "Usuário não encontrado!",
     *                     }
     *                 )
     *              )
     *         }
     *     )
     * ),
     * 
     */
    public function triggerEmailToConfirm()
    {
        /** @var User */
        $user = auth()->user();

        if (!is_null($user->email_verified_at)) {
            throw new HttpResponseException(ApiError::message(
                '401',
                'O e-mail já foi confirmado!'
            ));
        }

        $code = (new GenerateCode())->forVerifyEmail();

        $user->update([
            'email_verify_code' => $code
        ]);

        Mail::to($user->email)->send(new UserConfirmEmailMail($code));

        return ApiSuccess::message(
            200,
            'O e-mail com o código foi enviado com sucesso!'
        );
    }

    /**
     * @OA\Post(
     *     tags={"Autenticação"},
     *     summary="Confirmar email",
     *     description="Confirma o email do usuário por meio de um código",
     *     path="/api/confirm-email",
     *     security={ {"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="Código enviado anteriormente no e-mail do usuário",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="O e-mail já foi confirmado",
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
     *                     example={
     *                         "code": 409,
     *                         "message": "O e-mail já foi confirmado!"
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Código enviado pelo usuário incorreto",
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
     *                     example={
     *                         "code": 401,
     *                         "message": "Código incorreto!"
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="E-mail confirmado com sucesso!",
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
     *                     example={
     *                         "code": 200,
     *                         "message": "O e-mail foi confirmado com sucesso!"
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=498,
     *         description="Token inválido",
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
     *                     example={
     *                         "code": 498,
     *                         "message": "Token inválido!",
     *                     }
     *                 )
     *              )
     *         }
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
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
     *                     example={
     *                         "code": 404,
     *                         "message": "Usuário não encontrado!",
     *                     }
     *                 )
     *              )
     *         }
     *     )
     * ),
     * 
     */
    public function confirmEmail(ConfirmEmailRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        if (!is_null($user->email_verified_at)) {
            throw new HttpResponseException(ApiError::message(
                '401',
                'O e-mail já foi confirmado!'
            ));
        }

        if ($user->email_verify_code != $request->code) {
            throw new HttpResponseException(ApiError::message(
                '401',
                'Código incorreto!'
            ));
        }

        $user->update([
            'email_verified_at' => now()
        ]);

        Mail::to($user->email)->send(new UserEmailConfirmedMail($user));

        return ApiSuccess::message(
            200,
            'O e-mail foi confirmado com sucesso!'
        );
    }
}
