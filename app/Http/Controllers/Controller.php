<?php

namespace App\Http\Controllers;

/**
 * @OA\Swagger(
 *     schemes={"http", "https"},
 *     @OA\Info(
 *          title="Viblo API",
 *          version="1.0.0",
 *          description="API documentation for Viblo, utilizing Laravel Sanctum for authentication.",
 *          @OA\Contact(
 *                email="chien.nd@zinza.com.vn"
 *          ),
 *      )
 * )
 *
 * @OA\Server(
 *     url="https://api.viblo.clone",
 *     description="API Server"
 * )
 */
abstract class Controller
{
    //
}
