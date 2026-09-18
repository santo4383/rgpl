<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // 1. MPSC Free Exam
        $mpsc = Course::where('name', 'like', '%MPSC%')->first();
        if ($mpsc) {
            $mpscQuestions = Question::where('course_id', $mpsc->id)->where('status', 'PUBLISHED')->get();
            $qCount = $mpscQuestions->count();
            $totalMarks = max(5.0, (float) $qCount);

            $mpscExam = Exam::firstOrCreate(
                ['slug' => 'mpsc-state-services-prelims-mock-1'],
                [
                    'course_id' => $mpsc->id,
                    'title' => 'MPSC State Services Prelims - Mock Test 1',
                    'description' => 'Full-length practice test covering History, Geography, Polity, and General Studies.',
                    'instructions' => "1. All questions are compulsory.\n2. Negative marking of 0.25 marks applies for each incorrect response.\n3. Timer will submit automatically on expiry.\n4. Retakes are NOT permitted.",
                    'duration_minutes' => 30,
                    'total_marks' => $totalMarks,
                    'pass_percentage' => 40.00,
                    'has_negative_marking' => true,
                    'default_negative_marks' => 0.25,
                    'is_published' => true,
                    'is_premium' => false,
                    'created_by' => $admin ? $admin->id : null,
                ]
            );

            // Assign questions
            $allocated = 0.0;
            foreach ($mpscQuestions as $idx => $q) {
                if ($allocated + 1.0 <= $totalMarks) {
                    ExamQuestion::firstOrCreate(
                        ['exam_id' => $mpscExam->id, 'question_id' => $q->id],
                        [
                            'marks' => 1.00,
                            'negative_marks' => 0.25,
                            'sort_order' => $idx + 1,
                        ]
                    );
                    $allocated += 1.0;
                }
            }

            // 2. MPSC Premium Exam
            $mpscPremiumExam = Exam::firstOrCreate(
                ['slug' => 'mpsc-officers-advanced-mock-test'],
                [
                    'course_id' => $mpsc->id,
                    'title' => 'MPSC Officers Advanced Mock Test (Premium)',
                    'description' => 'Advanced high-yield MCQs for serious MPSC aspirants. Requires active subscription pass.',
                    'instructions' => "1. Premium tier test.\n2. Negative marking: 0.25 marks per wrong answer.\n3. Single attempt only.",
                    'duration_minutes' => 45,
                    'total_marks' => $totalMarks,
                    'pass_percentage' => 50.00,
                    'has_negative_marking' => true,
                    'default_negative_marks' => 0.25,
                    'is_published' => true,
                    'is_premium' => true,
                    'created_by' => $admin ? $admin->id : null,
                ]
            );

            $allocated = 0.0;
            foreach ($mpscQuestions as $idx => $q) {
                if ($allocated + 1.0 <= $totalMarks) {
                    ExamQuestion::firstOrCreate(
                        ['exam_id' => $mpscPremiumExam->id, 'question_id' => $q->id],
                        [
                            'marks' => 1.00,
                            'negative_marks' => 0.25,
                            'sort_order' => $idx + 1,
                        ]
                    );
                    $allocated += 1.0;
                }
            }
        }

        // 3. Police Bharti Exam
        $police = Course::where('name', 'like', '%Police%')->first();
        if ($police) {
            $policeQuestions = Question::where('course_id', $police->id)->where('status', 'PUBLISHED')->get();
            $qCount = $policeQuestions->count();
            $totalMarks = max(5.0, (float) $qCount);

            $policeExam = Exam::firstOrCreate(
                ['slug' => 'police-bharti-constable-mock-1'],
                [
                    'course_id' => $police->id,
                    'title' => 'Maharashtra Police Bharti Constable Full Mock Test',
                    'description' => 'Comprehensive mock test with Marathi Grammar, GK, and Reasoning MCQs.',
                    'instructions' => "1. Read all questions thoroughly.\n2. No negative marking in this test.\n3. One attempt allowed.",
                    'duration_minutes' => 40,
                    'total_marks' => $totalMarks,
                    'pass_percentage' => 40.00,
                    'has_negative_marking' => false,
                    'default_negative_marks' => 0.00,
                    'is_published' => true,
                    'is_premium' => false,
                    'created_by' => $admin ? $admin->id : null,
                ]
            );

            $allocated = 0.0;
            foreach ($policeQuestions as $idx => $q) {
                if ($allocated + 1.0 <= $totalMarks) {
                    ExamQuestion::firstOrCreate(
                        ['exam_id' => $policeExam->id, 'question_id' => $q->id],
                        [
                            'marks' => 1.00,
                            'negative_marks' => 0.00,
                            'sort_order' => $idx + 1,
                        ]
                    );
                    $allocated += 1.0;
                }
            }
        }
    }
}
