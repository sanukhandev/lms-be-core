<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Tag(
 *     name="User Profile",
 *     description="User profile management endpoints"
 * )
 */
class UserController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get user profile",
     *     description="Get the authenticated user's profile information",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+1234567890"),
     *                 @OA\Property(property="bio", type="string", example="Software developer with 5 years experience"),
     *                 @OA\Property(property="avatar_url", type="string", example="https://example.com/avatars/user1.jpg"),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-15"),
     *                 @OA\Property(property="gender", type="string", example="male"),
     *                 @OA\Property(property="country", type="string", example="United States"),
     *                 @OA\Property(property="timezone", type="string", example="America/New_York"),
     *                 @OA\Property(property="language", type="string", example="en"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="stats",
     *                     type="object",
     *                     @OA\Property(property="total_enrollments", type="integer", example=5),
     *                     @OA\Property(property="completed_courses", type="integer", example=3),
     *                     @OA\Property(property="active_enrollments", type="integer", example=2),
     *                     @OA\Property(property="certificates_earned", type="integer", example=3)
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
    public function profile(): JsonResponse
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'total_enrollments' => $user->enrollments()->count(),
            'completed_courses' => $user->enrollments()->where('status', 'completed')->count(),
            'active_enrollments' => $user->enrollments()->where('status', 'active')->count(),
            'certificates_earned' => $user->certificates()->count() ?? 0, // Assuming certificates relationship exists
        ];

        $userData = $user->toArray();
        $userData['stats'] = $stats;

        return $this->successResponse($userData, 'Profile retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/profile",
     *     summary="Update user profile",
     *     description="Update the authenticated user's profile information",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe", description="Full name"),
     *             @OA\Property(property="phone", type="string", example="+1234567890", description="Phone number"),
     *             @OA\Property(property="bio", type="string", example="Software developer with 5 years experience", description="Biography"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-15", description="Date of birth"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other", "prefer_not_to_say"}, example="male", description="Gender"),
     *             @OA\Property(property="country", type="string", example="United States", description="Country"),
     *             @OA\Property(property="timezone", type="string", example="America/New_York", description="Timezone"),
     *             @OA\Property(property="language", type="string", example="en", description="Preferred language")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'bio' => 'sometimes|string|max:1000',
            'date_of_birth' => 'sometimes|date|before:today',
            'gender' => 'sometimes|in:male,female,other,prefer_not_to_say',
            'country' => 'sometimes|string|max:100',
            'timezone' => 'sometimes|string|max:50',
            'language' => 'sometimes|string|max:5',
        ]);

        $user->update($validatedData);

        return $this->successResponse($user->fresh(), 'Profile updated successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/profile/password",
     *     summary="Change password",
     *     description="Change the authenticated user's password",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="current_password", type="string", example="oldpassword123", description="Current password"),
     *             @OA\Property(property="password", type="string", example="newpassword123", description="New password"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword123", description="Confirm new password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password changed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or incorrect current password"
     *     )
     * )
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('Current password is incorrect', 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->successResponse(null, 'Password changed successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/profile/dashboard",
     *     summary="Get user dashboard data",
     *     description="Get dashboard statistics and recent activity for the authenticated user",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dashboard data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="stats",
     *                     type="object",
     *                     @OA\Property(property="total_enrollments", type="integer", example=5),
     *                     @OA\Property(property="completed_courses", type="integer", example=3),
     *                     @OA\Property(property="active_enrollments", type="integer", example=2),
     *                     @OA\Property(property="certificates_earned", type="integer", example=3),
     *                     @OA\Property(property="total_study_time_hours", type="integer", example=45),
     *                     @OA\Property(property="current_streak_days", type="integer", example=7)
     *                 ),
     *                 @OA\Property(
     *                     property="recent_enrollments",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="course_title", type="string", example="Introduction to Laravel"),
     *                         @OA\Property(property="progress_percentage", type="number", format="float", example=45.5),
     *                         @OA\Property(property="last_accessed_at", type="string", format="date-time"),
     *                         @OA\Property(property="course", ref="#/components/schemas/Course")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="recommended_courses",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Course")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();

        // Get comprehensive statistics
        $stats = [
            'total_enrollments' => $user->enrollments()->count(),
            'completed_courses' => $user->enrollments()->where('status', 'completed')->count(),
            'active_enrollments' => $user->enrollments()->where('status', 'active')->count(),
            'certificates_earned' => 0, // TODO: Implement certificates
            'total_study_time_hours' => 0, // TODO: Implement time tracking
            'current_streak_days' => 0, // TODO: Implement streak tracking
        ];

        // Get recent enrollments with progress
        $recentEnrollments = $user->enrollments()
            ->with(['course.instructor', 'course.category'])
            ->where('status', 'active')
            ->orderBy('last_accessed_at', 'desc')
            ->limit(5)
            ->get();

        // Get recommended courses (simple logic for now)
        $recommendedCourses = \App\Models\Course::published()
            ->with(['instructor', 'category'])
            ->whereNotIn('id', $user->enrollments()->pluck('course_id'))
            ->featured()
            ->limit(3)
            ->get();

        $dashboardData = [
            'stats' => $stats,
            'recent_enrollments' => $recentEnrollments,
            'recommended_courses' => $recommendedCourses,
        ];

        return $this->successResponse($dashboardData, 'Dashboard data retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/profile/activity",
     *     summary="Get user activity history",
     *     description="Get the authenticated user's learning activity history",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days to look back",
     *         required=false,
     *         @OA\Schema(type="integer", default=30)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activity history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Activity history retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="activity_timeline",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="date", type="string", format="date", example="2024-01-15"),
     *                         @OA\Property(property="activities", type="array", @OA\Items(
     *                             @OA\Property(property="type", type="string", example="course_enrolled"),
     *                             @OA\Property(property="description", type="string", example="Enrolled in Introduction to Laravel"),
     *                             @OA\Property(property="course_title", type="string", example="Introduction to Laravel"),
     *                             @OA\Property(property="timestamp", type="string", format="date-time")
     *                         ))
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function activity(Request $request): JsonResponse
    {
        $user = Auth::user();
        $days = $request->get('days', 30);

        $startDate = now()->subDays($days);

        // Get enrollments within the time period
        $enrollments = $user->enrollments()
            ->with('course')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // TODO: Implement comprehensive activity tracking
        // For now, we'll just show enrollment activities
        $activityTimeline = [];
        
        foreach ($enrollments as $enrollment) {
            $date = $enrollment->created_at->format('Y-m-d');
            
            if (!isset($activityTimeline[$date])) {
                $activityTimeline[$date] = [
                    'date' => $date,
                    'activities' => []
                ];
            }

            $activityTimeline[$date]['activities'][] = [
                'type' => 'course_enrolled',
                'description' => 'Enrolled in ' . $enrollment->course->title,
                'course_title' => $enrollment->course->title,
                'timestamp' => $enrollment->created_at->toISOString(),
            ];
        }

        // Convert to indexed array and sort by date descending
        $activityTimeline = array_values($activityTimeline);
        usort($activityTimeline, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $this->successResponse([
            'activity_timeline' => $activityTimeline
        ], 'Activity history retrieved successfully');
    }
}
