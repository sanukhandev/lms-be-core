<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding orders...');

        $students = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'student')
            ->where('users.tenant_id', 'demo')
            ->pluck('users.id')
            ->toArray();

        $paidCourses = DB::table('courses')
            ->where('tenant_id', 'demo')
            ->where('price', '>', 0)
            ->get();

        foreach ($students as $studentId) {
            // Create 1-2 orders per student
            $orderCount = rand(1, 2);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $course = $paidCourses->random();
                $orderNumber = 'ORD-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                
                $subtotal = $course->price;
                $taxAmount = round($subtotal * 0.08, 2); // 8% tax
                $discountAmount = 0.00;
                $totalAmount = $subtotal + $taxAmount - $discountAmount;

                $orderId = DB::table('orders')->insertGetId([
                    'tenant_id' => 'demo',
                    'user_id' => $studentId,
                    'order_number' => $orderNumber,
                    'status' => 'paid',
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'currency' => 'USD',
                    'payment_method' => 'stripe',
                    'stripe_payment_intent_id' => 'pi_demo_' . uniqid(),
                    'paid_at' => now()->subDays(rand(1, 30)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create order item
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'course_id' => $course->id,
                    'price' => $course->price,
                    'quantity' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Orders seeded successfully');
    }
}
