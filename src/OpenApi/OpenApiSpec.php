<?php

namespace Rainwaves\LaraAuthSuite\OpenApi;


use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="Laravel Authentication Suite",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="support@example.com"
 *   )
 * )
 * @OA\Tag(name="Auth", description="Operations related to authentications")
 * @OA\PathItem(path="/api/v1")
 * @OA\Server(
 *     url="http://localhost",
 *     description="API server"
 * )
 */
class OpenApiSpec
{

}