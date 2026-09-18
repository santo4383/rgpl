<?php

namespace App\Http\Controllers\Api\V1\Progress;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Course;
use App\Models\ExamAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiProgressController extends BaseApiController
{
    /**
     * Get comprehensive overall and stream-wise progress analytics.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Overall Practice metrics
        $totalAttempted = $user->uniqueQuestionsAttemptedCount();
        $totalCorrect = $user->correctAttemptsCount();
        $totalIncorrect = max(0, $totalAttempted - $totalCorrect);
        $accuracy = $user->accuracyPercentage();

        // 2. Overall Exam metrics
        $examAttempts = ExamAttempt::where('user_id', $user->id)
            ->whereIn('status', ['SUBMITTED', 'TIMED_OUT'])
            ->get();

        $examCount = $examAttempts->count();
        $examAvgScore = $examCount > 0 ? round($examAttempts->avg('percentage'), 1) : 0.0;
        $examsPassed = $examAttempts->where('is_passed', true)->count();

        // 3. Field-wise breakdown
        $courses = $user->courses()->where('is_active', true)->get();
        $fieldBreakdown = $courses->map(function (Course $course) use ($user) {
            $attempted = $user->uniqueQuestionsAttemptedCount($course->id);
            $correct = $user->correctAttemptsCount($course->id);
            $courseAccuracy = $user->accuracyPercentage($course->id);

            return [
                'course_id' => $course->id,
                'course_name' => $course->name,
                'slug' => $course->slug,
                'icon' => $course->icon,
                'questions_attempted' => $attempted,
                'correct_count' => $correct,
                'incorrect_count' => max(0, $attempted - $correct),
                'accuracy_percentage' => $courseAccuracy,
                'free_quota_remaining' => $user->freeQuotaRemaining($course->id),
                'has_active_subscription' => $user->hasActiveSubscription($course->id),
            ];
        });

        return $this->successResponse([
            'overall' => [
                'questions_attempted' => $totalAttempted,
                'correct_count' => $totalCorrect,
                'incorrect_count' => $totalIncorrect,
                'accuracy_percentage' => $accuracy,
                'exam_attempts_count' => $examCount,
                'exams_passed_count' => $examsPassed,
                'average_exam_percentage' => $examAvgScore,
            ],
            'fields_progress' => $fieldBreakdown,
        ]);
    }
}
