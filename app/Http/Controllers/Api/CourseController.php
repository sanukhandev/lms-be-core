<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/courses",
     *     summary="Get all courses",
     *     description="Retrieve a paginated list of courses for the current tenant",
     *     tags={"Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="level",
     *         in="query",
     *         description="Filter by course level",
     *         required=false,
     *         @OA\Schema(type="string", enum={"beginner", "intermediate", "advanced"})
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="is_free",
     *         in="query",
     *         description="Filter by free courses",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in title and description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Courses retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Courses retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);

        $query = Course::with(['category', 'instructor', 'tags'])
            ->where('tenant_id', $user->tenant_id)
            ->where('status', 'published');

        // Apply filters
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_free')) {
            $query->where('is_free', $request->boolean('is_free'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('sort_order')
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return $this->sendResponse($courses, 'Courses retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/courses/{id}",
     *     summary="Get course details",
     *     description="Retrieve detailed information about a specific course",
     *     tags={"Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Course ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Course retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $user = Auth::user();

        $course = Course::with([
            'category',
            'instructor',
            'tags',
            'modules.chapters.contentType',
            'learningObjectives',
            'prerequisites'
        ])
        ->where('tenant_id', $user->tenant_id)
        ->where('status', 'published')
        ->find($id);

        if (!$course) {
            return $this->sendError('Course not found');
        }

        // Check if user is enrolled
        $enrollment = $course->enrollments()
            ->where('user_id', $user->id)
            ->first();

        $courseData = $course->toArray();
        $courseData['is_enrolled'] = !is_null($enrollment);
        $courseData['enrollment'] = $enrollment;

        return $this->sendResponse($courseData, 'Course retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/courses/{id}/enroll",
     *     summary="Enroll in a course",
     *     description="Enroll the authenticated user in a course",
     *     tags={"Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Course ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enrolled in course successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrolled in course successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Already enrolled or course not available"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found"
     *     )
     * )
     */
    public function enroll($id): JsonResponse
    {
        $user = Auth::user();

        $course = Course::where('tenant_id', $user->tenant_id)
            ->where('status', 'published')
            ->find($id);

        if (!$course) {
            return $this->sendError('Course not found');
        }

        // Check if already enrolled
        $existingEnrollment = $course->enrollments()
            ->where('user_id', $user->id)
            ->first();

        if ($existingEnrollment) {
            return $this->sendError('Already enrolled in this course', [], 400);
        }

        // Calculate total chapters
        $totalChapters = $course->modules()
            ->join('chapters', 'modules.id', '=', 'chapters.module_id')
            ->where('chapters.is_published', true)
            ->count();

        // Create enrollment
        $enrollment = $course->enrollments()->create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'expires_at' => now()->addMonths(6), // 6 months access
            'progress_percentage' => 0,
            'completed_chapters' => 0,
            'total_chapters' => $totalChapters,
            'time_spent_minutes' => 0,
        ]);

        return $this->sendResponse($enrollment, 'Enrolled in course successfully');
    }

    /**
     * @OA\Get(
     *     path="/courses/my-courses",
     *     summary="Get user's enrolled courses",
     *     description="Retrieve courses the authenticated user is enrolled in",
     *     tags={"Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by enrollment status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "completed", "dropped", "expired", "suspended"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enrolled courses retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrolled courses retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function myCourses(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = $user->enrollments()
            ->with(['course.category', 'course.instructor', 'course.tags'])
            ->where('tenant_id', $user->tenant_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('enrolled_at', 'desc')->get();

        return $this->sendResponse($enrollments, 'Enrolled courses retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/courses/featured",
     *     summary="Get featured courses",
     *     description="Retrieve featured courses for the current tenant",
     *     tags={"Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of courses to return",
     *         required=false,
     *         @OA\Schema(type="integer", example=6)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Featured courses retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Featured courses retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function featured(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 6);

        $courses = Course::with(['category', 'instructor', 'tags'])
            ->where('tenant_id', $user->tenant_id)
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->sendResponse($courses, 'Featured courses retrieved successfully');
    }
}
