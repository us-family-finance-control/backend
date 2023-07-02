<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Us API",
 *      description="API do aplicativo US"
 * ),
 * @OA\SecurityScheme(
 *      type="http",
 *      description="Paramêtro obrigatório para identificar se o usuário está autenticado",
 *      name="Authorization",
 *      in="header",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      securityScheme="BearerAuth"
 * ),
 * @OA\Tag(
 *      name="Autenticação",
 *      description="Rotas para gerar um token"
 * ),
 *
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
