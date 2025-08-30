<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding certificates...');

        // Get completed enrollments
        $completedEnrollments = DB::table('enrollments')
            ->where('tenant_id', 'demo')
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get();

        foreach ($completedEnrollments as $enrollment) {
            $course = DB::table('courses')->find($enrollment->course_id);
            $student = DB::table('users')->find($enrollment->student_id);
            
            $certificateNumber = 'CERT-' . strtoupper(uniqid());
            $verificationUrl = "https://demo.lms.local/verify/{$certificateNumber}";

            DB::table('certificates')->insert([
                'tenant_id' => 'demo',
                'enrollment_id' => $enrollment->id,
                'certificate_number' => $certificateNumber,
                'issued_at' => $enrollment->completed_at,
                'expires_at' => null, // No expiration
                'template_data' => json_encode([
                    'student_name' => $student->first_name . ' ' . $student->last_name,
                    'course_title' => $course->title,
                    'completion_date' => $enrollment->completed_at,
                    'instructor_name' => 'Course Instructor',
                    'grade' => 'Pass',
                ]),
                'pdf_path' => "/certificates/{$certificateNumber}.pdf",
                'verification_url' => $verificationUrl,
                'is_revoked' => false,
                'revoked_at' => null,
                'revoked_reason' => null,
                'created_at' => $enrollment->completed_at,
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Certificates seeded successfully');
    }
}
