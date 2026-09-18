<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $mpsc = Course::where('name', 'like', '%MPSC%')->first();
        $police = Course::where('name', 'like', '%Police%')->first();

        // 1. MPSC Targeted Plan
        $mpscPlan = SubscriptionPlan::firstOrCreate([
            'name' => 'MPSC Rajyaseva 90-Day Full Access',
        ], [
            'description' => 'Unlimited question practice, complete answer explanations, and syllabus mock tests for MPSC.',
            'price' => 499.00,
            'duration_days' => 90,
            'is_all_courses' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        if ($mpsc) {
            $mpscPlan->courses()->syncWithoutDetaching([$mpsc->id]);
        }

        // 2. Police Bharti Targeted Plan
        $policePlan = SubscriptionPlan::firstOrCreate([
            'name' => 'Police Bharti 60-Day Sprint Pass',
        ], [
            'description' => 'Targeted high-yield question sets and daily practice for Maharashtra Police recruitment.',
            'price' => 299.00,
            'duration_days' => 60,
            'is_all_courses' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        if ($police) {
            $policePlan->courses()->syncWithoutDetaching([$police->id]);
        }

        // 3. Mega All-Access Pass
        SubscriptionPlan::firstOrCreate([
            'name' => 'All-Access Maharashtra Exam Pass (365 Days)',
        ], [
            'description' => '1 Year of unlimited practice across ALL current and future courses including MPSC, Police Bharti, and more.',
            'price' => 999.00,
            'duration_days' => 365,
            'is_all_courses' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
