<?php

namespace App\Http\Controllers\Api\V1\Practice;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Bookmark;
use App\Models\Course;
use App\Models\PracticeAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiPracticeController extends BaseApiController
{
    /**
     * Get paginated practice questions for a course/category/subcategory.
     */
    public function questions(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'difficulty' => ['nullable', 'string'],
            'status_filter' => ['nullable', 'in:all,unattempted,incorrect,bookmarked'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $user = $request->user();
        $courseId = $request->course_id;

        $query = Question::where('course_id', $courseId)
            ->where('status', 'PUBLISHED')
            ->with(['options' => function ($q) {
                $q->orderBy('sort_order')->orderBy('option_key');
            }, 'images' => function ($q) {
                $q->orderBy('sort_order');
            }]);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        if ($request->filled('difficulty') && $request->difficulty !== 'all') {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->status_filter === 'unattempted') {
            $query->whereDoesntHave('practiceAttempts', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($request->status_filter === 'incorrect') {
            $query->whereHas('practiceAttempts', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('is_correct', false);
            });
        } elseif ($request->status_filter === 'bookmarked') {
            $query->whereHas('bookmarks', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $perPage = $request->input('per_page', 20);
        $paginated = $query->orderBy('id')->paginate($perPage);

        // Fetch user attempts and bookmarks for these questions in bulk
        $questionIds = $paginated->pluck('id')->toArray();
        $attempts = PracticeAttempt::where('user_id', $user->id)
            ->whereIn('question_id', $questionIds)
            ->get()
            ->keyBy('question_id');

        $bookmarkedIds = Bookmark::where('user_id', $user->id)
            ->whereIn('question_id', $questionIds)
            ->pluck('question_id')
            ->toArray();

        $formatted = $paginated->getCollection()->map(function ($q) use ($attempts, $bookmarkedIds) {
            $attempt = $attempts->get($q->id);
            $hasAttempted = $attempt !== null;

            return [
                'id' => $q->id,
                'course_id' => $q->course_id,
                'category_id' => $q->category_id,
                'subcategory_id' => $q->subcategory_id,
                'question_type' => $q->question_type ?? 'STANDARD_MCQ',
                'language' => $q->language ?? 'en',
                'question_text' => $q->question_text,
                'question_html' => $q->question_html,
                'difficulty' => $q->difficulty,
                'is_bookmarked' => in_array($q->id, $bookmarkedIds),
                'has_attempted' => $hasAttempted,
                'user_selected_option_id' => $attempt?->selected_option_id,
                'is_user_correct' => $attempt?->is_correct,
                // Only reveal explanation and correct answer if already attempted
                'explanation_text' => $hasAttempted ? $q->explanation_text : null,
                'explanation_html' => $hasAttempted ? $q->explanation_html : null,
                'images' => $q->images->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'file_path' => asset('storage/'.$img->file_path),
                        'file_name' => $img->file_name,
                        'mime_type' => $img->mime_type,
                    ];
                }),
                'options' => $q->options->map(function ($opt) use ($hasAttempted) {
                    return [
                        'id' => $opt->id,
                        'option_key' => $opt->option_key,
                        'option_text' => $opt->option_text,
                        'option_html' => $opt->option_html,
                        // NEVER expose is_correct before submission
                        'is_correct' => $hasAttempted ? (bool) $opt->is_correct : null,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
                'has_more' => $paginated->hasMorePages(),
            ],
            'quota' => [
                'free_quota_remaining' => $user->freeQuotaRemaining($courseId),
                'has_active_subscription' => $user->hasActiveSubscription($courseId),
            ],
        ]);
    }

    /**
     * Submit an answer to a practice question.
     */
    public function submitAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'selected_option_id' => ['required', 'exists:question_options,id'],
        ]);

        $user = $request->user();
        $question = Question::with('options')->findOrFail($request->question_id);
        $courseId = $question->course_id;

        $existingAttempt = PracticeAttempt::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->first();

        // Server-authoritative Free Quota Enforcement
        if (! $existingAttempt && $user->hasExceededFreeQuota($courseId)) {
            return $this->errorResponse(
                'FREE_LIMIT_REACHED',
                'You have reached your 500 free question quota for this stream. Please subscribe to Premium to continue.',
                403,
                [
                    'course_id' => $courseId,
                    'free_limit' => Course::find($courseId)->free_question_limit ?? 500,
                    'attempted' => $user->uniqueQuestionsAttemptedCount($courseId),
                ]
            );
        }

        $selectedOption = QuestionOption::where('question_id', $question->id)
            ->where('id', $request->selected_option_id)
            ->first();

        if (! $selectedOption) {
            return $this->errorResponse('INVALID_OPTION', 'The selected option does not belong to this question.', 422);
        }

        $isCorrect = (bool) $selectedOption->is_correct;
        $correctOption = $question->options->firstWhere('is_correct', true);

        if ($existingAttempt) {
            $existingAttempt->update([
                'selected_option_id' => $selectedOption->id,
                'is_correct' => $isCorrect,
                'attempt_count' => $existingAttempt->attempt_count + 1,
                'last_attempted_at' => now(),
            ]);
        } else {
            PracticeAttempt::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'question_id' => $question->id,
                'selected_option_id' => $selectedOption->id,
                'is_correct' => $isCorrect,
                'attempt_count' => 1,
                'last_attempted_at' => now(),
            ]);
        }

        return $this->successResponse([
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'selected_option_id' => $selectedOption->id,
            'correct_option_id' => $correctOption?->id,
            'correct_option_key' => $correctOption?->option_key,
            'explanation_text' => $question->explanation_text,
            'explanation_html' => $question->explanation_html,
            'free_quota_remaining' => $user->freeQuotaRemaining($courseId),
        ], 'Answer evaluated successfully.');
    }

    /**
     * Get user practice summary stats.
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $courseId = $request->input('course_id');

        $totalAttempted = $user->uniqueQuestionsAttemptedCount($courseId);
        $correct = $user->correctAttemptsCount($courseId);
        $accuracy = $user->accuracyPercentage($courseId);

        return $this->successResponse([
            'total_attempted' => $totalAttempted,
            'correct_count' => $correct,
            'incorrect_count' => max(0, $totalAttempted - $correct),
            'accuracy_percentage' => $accuracy,
            'has_active_subscription' => $user->hasActiveSubscription($courseId),
            'free_quota_remaining' => $courseId ? $user->freeQuotaRemaining($courseId) : null,
        ]);
    }
}
