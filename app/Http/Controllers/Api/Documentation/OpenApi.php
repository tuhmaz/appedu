<?php

namespace App\Http\Controllers\Api\Documentation;

/**
 * @OA\Info(
 *     title="Education Platform API",
 *     version="1.0.0",
 *     description="API documentation for the Education Platform. This API provides comprehensive endpoints for managing educational content including articles, users, and files.",
 *     @OA\Contact(
 *         email="support@alemedu.com",
 *         name="Support Team"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(
 *     name="Articles",
 *     description="API Endpoints for managing articles"
 * )
 */
class OpenApi {}
