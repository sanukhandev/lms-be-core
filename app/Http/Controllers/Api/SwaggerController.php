<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="LMS Backend Core API",
 *     version="1.0.0",
 *     description="Learning Management System Backend API Documentation. This API provides comprehensive endpoints for managing courses, users, enrollments, and learning progress.",
 *     @OA\Contact(
 *         email="api-support@lms.com",
 *         name="LMS API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="LMS API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter JWT Bearer token in format: Bearer <token>"
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="bio", type="string", example="Software developer with 5 years experience"),
 *     @OA\Property(property="avatar_url", type="string", example="https://example.com/avatars/user1.jpg"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-15"),
 *     @OA\Property(property="gender", type="string", example="male"),
 *     @OA\Property(property="country", type="string", example="United States"),
 *     @OA\Property(property="timezone", type="string", example="America/New_York"),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Course",
 *     type="object",
 *     title="Course",
 *     description="Course model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Introduction to Laravel"),
 *     @OA\Property(property="slug", type="string", example="introduction-to-laravel"),
 *     @OA\Property(property="short_description", type="string", example="Learn Laravel framework basics"),
 *     @OA\Property(property="description", type="string", example="Comprehensive course covering Laravel fundamentals..."),
 *     @OA\Property(property="thumbnail_url", type="string", example="https://example.com/thumbnails/course1.jpg"),
 *     @OA\Property(property="level", type="string", enum={"beginner", "intermediate", "advanced"}, example="beginner"),
 *     @OA\Property(property="status", type="string", enum={"draft", "published", "archived"}, example="published"),
 *     @OA\Property(property="price", type="number", format="float", example=99.99),
 *     @OA\Property(property="estimated_duration_hours", type="integer", example=20),
 *     @OA\Property(property="language", type="string", example="en"),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="is_free", type="boolean", example=false),
 *     @OA\Property(property="published_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="instructor", ref="#/components/schemas/User"),
 *     @OA\Property(property="category", ref="#/components/schemas/Category"),
 *     @OA\Property(property="enrollment_count", type="integer", example=150),
 *     @OA\Property(property="total_chapters", type="integer", example=12)
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Course category model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Programming"),
 *     @OA\Property(property="slug", type="string", example="programming"),
 *     @OA\Property(property="description", type="string", example="Programming and software development courses"),
 *     @OA\Property(property="icon", type="string", example="fas fa-code"),
 *     @OA\Property(property="image_url", type="string", example="https://example.com/images/programming.jpg"),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="level", type="integer", example=1),
 *     @OA\Property(property="sort_order", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="courses_count", type="integer", example=25),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Enrollment",
 *     type="object",
 *     title="Enrollment",
 *     description="Course enrollment model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="course_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", enum={"active", "completed", "suspended", "cancelled"}, example="active"),
 *     @OA\Property(property="enrolled_at", type="string", format="date-time"),
 *     @OA\Property(property="completed_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="cancelled_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="progress_percentage", type="number", format="float", example=45.5),
 *     @OA\Property(property="last_accessed_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="Validation Error",
 *     description="Validation error response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(
 *             property="field_name",
 *             type="array",
 *             @OA\Items(type="string", example="The field name is required.")
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     description="Standard error response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="An error occurred"),
 *     @OA\Property(property="error_code", type="string", example="RESOURCE_NOT_FOUND")
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Success Response",
 *     description="Standard success response",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operation completed successfully"),
 *     @OA\Property(property="data", type="object", description="Response data")
 * )
 * 
 * @OA\Schema(
 *     schema="PaginatedResponse",
 *     type="object",
 *     title="Paginated Response",
 *     description="Paginated response structure",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Data retrieved successfully"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="total", type="integer", example=100),
 *         @OA\Property(property="last_page", type="integer", example=7),
 *         @OA\Property(property="from", type="integer", example=1),
 *         @OA\Property(property="to", type="integer", example=15),
 *         @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *     )
 * )
 */
class SwaggerController extends Controller
{
    // This controller exists solely for Swagger documentation
    // All actual API endpoints are defined in their respective controllers
}
