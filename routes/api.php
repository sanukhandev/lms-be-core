<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Public course browsing (for marketing pages)
Route::get('/courses/featured', [CourseController::class, 'featured']);

// Public category routes
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/tree', [CategoryController::class, 'tree']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::get('/{id}/courses', [CategoryController::class, 'courses']);
});

// Protected routes
Route::middleware(['auth:api'])->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Course routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/my-courses', [CourseController::class, 'myCourses']);
        Route::get('/{id}', [CourseController::class, 'show']);
        Route::post('/{id}/enroll', [CourseController::class, 'enroll']);
    });

    // Enrollment routes
    Route::prefix('enrollments')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index']);
        Route::get('/{id}', [EnrollmentController::class, 'show']);
        Route::put('/{id}/progress', [EnrollmentController::class, 'updateProgress']);
        Route::delete('/{id}', [EnrollmentController::class, 'cancel']);
    });

    // Alternative enrollment route
    Route::post('/courses/{courseId}/enroll', [EnrollmentController::class, 'enroll']);

    // User profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'profile']);
        Route::put('/', [UserController::class, 'updateProfile']);
        Route::put('/password', [UserController::class, 'changePassword']);
        Route::get('/dashboard', [UserController::class, 'dashboard']);
        Route::get('/activity', [UserController::class, 'activity']);
    });

    // Health check route
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'version' => '1.0.0',
            'user' => auth()->user()->only(['id', 'name', 'email']),
        ]);
    });
});

// Fallback for undefined routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found',
        'error' => 'The requested endpoint does not exist.'
    ], 404);
});
