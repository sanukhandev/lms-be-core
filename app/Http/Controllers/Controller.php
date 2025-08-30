<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="LMS SaaS Backend API",
 *     version="1.0.0",
 *     description="Multi-tenant Learning Management System API with role-based access control",
 *     @OA\Contact(
 *         email="support@lms.example.com",
 *         name="LMS Support Team"
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
 *     bearerFormat="JWT",
 *     description="Use JWT token in Authorization header"
 * )
 */
abstract class Controller
{
    //
}
