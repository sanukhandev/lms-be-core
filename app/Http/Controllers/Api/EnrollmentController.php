<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Enrollments",
 *     description="Course enrollment management endpoints"
 * )
 */
class EnrollmentController extends BaseApiController
{
    /**
     * @OA\Post(
     *     path="/api/courses/{courseId}/enroll",
     *     summary="Enroll in a course",
     *     description="Enroll the authenticated user in a specific course",
     *     tags={"Enrollments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         description="Course ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully enrolled in course",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully enrolled in course"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="course_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="enrolled_at", type="string", format="date-time"),
     *                 @OA\Property(property="progress_percentage", type="number", format="float", example=0),
     *                 @OA\Property(property="course", ref="#/components/schemas/Course")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Already enrolled or course not available",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are already enrolled in this course")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function enroll(Request $request, int $courseId): JsonResponse
    {
        $user = Auth::user();
        $course = Course::published()->find($courseId);

        if (!$course) {
            return $this->errorResponse('Course not found or not available', 404);
        }

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->first();

        if ($existingEnrollment) {
            return $this->errorResponse('You are already enrolled in this course', 400);
        }

        // For paid courses, check if user has purchased
        if (!$course->isFree()) {
            // TODO: Implement payment verification logic
            // For now, we'll allow enrollment in paid courses for demo purposes
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        $enrollment->load('course.instructor');

        return $this->successResponse($enrollment, 'Successfully enrolled in course', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/enrollments",
     *     summary="Get user enrollments",
     *     description="Get all enrollments for the authenticated user",
     *     tags={"Enrollments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by enrollment status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "completed", "suspended", "cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enrollments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrollments retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=5),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="status", type="string", example="active"),
     *                         @OA\Property(property="enrolled_at", type="string", format="date-time"),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true),
     *                         @OA\Property(property="progress_percentage", type="number", format="float", example=45.5),
     *                         @OA\Property(property="last_accessed_at", type="string", format="date-time", nullable=true),
     *                         @OA\Property(property="course", ref="#/components/schemas/Course")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = Enrollment::where('user_id', $user->id)
            ->with(['course.instructor', 'course.category']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('enrolled_at', 'desc');

        $perPage = $request->get('per_page', 10);
        $enrollments = $query->paginate($perPage);

        return $this->paginatedResponse($enrollments, 'Enrollments retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/enrollments/{id}",
     *     summary="Get enrollment details",
     *     description="Get detailed information about a specific enrollment",
     *     tags={"Enrollments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Enrollment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enrollment details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrollment details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="enrolled_at", type="string", format="date-time"),
     *                 @OA\Property(property="completed_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="progress_percentage", type="number", format="float", example=45.5),
     *                 @OA\Property(property="last_accessed_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="course", ref="#/components/schemas/Course"),
     *                 @OA\Property(
     *                     property="progress_details",
     *                     type="object",
     *                     @OA\Property(property="completed_chapters", type="integer", example=5),
     *                     @OA\Property(property="total_chapters", type="integer", example=12),
     *                     @OA\Property(property="completed_assignments", type="integer", example=2),
     *                     @OA\Property(property="total_assignments", type="integer", example=4),
     *                     @OA\Property(property="time_spent_minutes", type="integer", example=180)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not your enrollment"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Enrollment not found"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::with(['course.instructor', 'course.category', 'course.modules.chapters'])
            ->find($id);

        if (!$enrollment) {
            return $this->errorResponse('Enrollment not found', 404);
        }

        if ($enrollment->user_id !== $user->id) {
            return $this->errorResponse('Forbidden - This enrollment does not belong to you', 403);
        }

        // Calculate progress details
        $course = $enrollment->course;
        $totalChapters = $course->modules->sum(function ($module) {
            return $module->chapters->count();
        });

        // TODO: Implement actual progress tracking
        $completedChapters = 0; // This would come from user progress records
        $totalAssignments = $course->assignments()->count();
        $completedAssignments = 0; // This would come from assignment submissions
        $timeSpentMinutes = 0; // This would come from user activity tracking

        $enrollment->progress_details = [
            'completed_chapters' => $completedChapters,
            'total_chapters' => $totalChapters,
            'completed_assignments' => $completedAssignments,
            'total_assignments' => $totalAssignments,
            'time_spent_minutes' => $timeSpentMinutes,
        ];

        return $this->successResponse($enrollment, 'Enrollment details retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/enrollments/{id}/progress",
     *     summary="Update enrollment progress",
     *     description="Update the progress of a specific enrollment",
     *     tags={"Enrollments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Enrollment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="chapter_id", type="integer", example=1, description="ID of the completed chapter"),
     *             @OA\Property(property="progress_percentage", type="number", format="float", example=65.5, description="Overall course progress percentage"),
     *             @OA\Property(property="time_spent_minutes", type="integer", example=30, description="Time spent in this session (minutes)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Progress updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Progress updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="progress_percentage", type="number", format="float", example=65.5),
     *                 @OA\Property(property="last_accessed_at", type="string", format="date-time"),
     *                 @OA\Property(property="status", type="string", example="active")
     *             )
     *         )
     *     )
     * )
     */
    public function updateProgress(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return $this->errorResponse('Enrollment not found', 404);
        }

        if ($enrollment->user_id !== $user->id) {
            return $this->errorResponse('Forbidden - This enrollment does not belong to you', 403);
        }

        $request->validate([
            'chapter_id' => 'sometimes|integer|exists:chapters,id',
            'progress_percentage' => 'sometimes|numeric|min:0|max:100',
            'time_spent_minutes' => 'sometimes|integer|min:0',
        ]);

        // Update enrollment
        if ($request->has('progress_percentage')) {
            $enrollment->progress_percentage = $request->progress_percentage;
            
            // Mark as completed if 100%
            if ($request->progress_percentage >= 100) {
                $enrollment->status = 'completed';
                $enrollment->completed_at = now();
            }
        }

        $enrollment->last_accessed_at = now();
        $enrollment->save();

        // TODO: Implement chapter progress tracking
        if ($request->has('chapter_id')) {
            // Create or update chapter progress record
        }

        // TODO: Implement time tracking
        if ($request->has('time_spent_minutes')) {
            // Add to total time spent
        }

        return $this->successResponse([
            'progress_percentage' => $enrollment->progress_percentage,
            'last_accessed_at' => $enrollment->last_accessed_at,
            'status' => $enrollment->status,
        ], 'Progress updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/enrollments/{id}",
     *     summary="Cancel enrollment",
     *     description="Cancel/withdraw from a course enrollment",
     *     tags={"Enrollments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Enrollment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enrollment cancelled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrollment cancelled successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Cannot cancel completed enrollment"
     *     )
     * )
     */
    public function cancel(int $id): JsonResponse
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return $this->errorResponse('Enrollment not found', 404);
        }

        if ($enrollment->user_id !== $user->id) {
            return $this->errorResponse('Forbidden - This enrollment does not belong to you', 403);
        }

        if ($enrollment->status === 'completed') {
            return $this->errorResponse('Cannot cancel a completed enrollment', 403);
        }

        $enrollment->status = 'cancelled';
        $enrollment->cancelled_at = now();
        $enrollment->save();

        return $this->successResponse(null, 'Enrollment cancelled successfully');
    }
}
