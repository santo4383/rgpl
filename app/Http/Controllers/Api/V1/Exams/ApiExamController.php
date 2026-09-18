<?php

namespace App\Http\Controllers\Api\V1\Exams;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiExamController extends BaseApiController
{
    /**
     * List mock exams available for practice.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Exam::where('is_published', true)->with('course');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $user = $request->user();
        $exams = $query->orderBy('id', 'desc')->get()->map(function ($exam) use ($user) {
            $latestAttempt = $user ? ExamAttempt::where('user_id', $user->id)
                ->where('exam_id', $exam->id)
                ->latest()
                ->first() : null;

            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'slug' => $exam->slug,
                'description' => $exam->description,
                'course_name' => $exam->course?->name,
                'course_id' => $exam->course_id,
                'duration_minutes' => $exam->duration_minutes,
                'total_marks' => $exam->total_marks,
                'pass_percentage' => $exam->pass_percentage,
                'is_premium' => (bool) $exam->is_premium,
                'question_count' => $exam->examQuestions()->count(),
                'user_attempt_status' => $latestAttempt?->status,
                'user_attempt_id' => $latestAttempt?->id,
                'user_score' => $latestAttempt?->score_obtained,
                'is_passed' => $latestAttempt?->is_passed,
            ];
        });

        return $this->successResponse($exams);
    }

    /**
     * Get exam details and instructions.
     */
    public function show(int $id): JsonResponse
    {
        $exam = Exam::with('course')->findOrFail($id);

        return $this->successResponse([
            'id' => $exam->id,
            'title' => $exam->title,
            'slug' => $exam->slug,
            'description' => $exam->description,
            'instructions' => $exam->instructions,
            'course_name' => $exam->course?->name,
            'course_id' => $exam->course_id,
            'duration_minutes' => $exam->duration_minutes,
            'total_marks' => $exam->total_marks,
            'pass_percentage' => $exam->pass_percentage,
            'has_negative_marking' => (bool) $exam->has_negative_marking,
            'default_negative_marks' => $exam->default_negative_marks,
            'is_premium' => (bool) $exam->is_premium,
            'question_count' => $exam->examQuestions()->count(),
        ]);
    }

    /**
     * Start an exam attempt. Server sets authoritative start time.
     */
    public function start(int $id, Request $request): JsonResponse
    {
        $exam = Exam::with(['examQuestions.question.options', 'examQuestions.question.images'])->findOrFail($id);
        $user = $request->user();

        // Check premium entitlement
        if ($exam->is_premium && ! $user->hasActiveSubscription($exam->course_id)) {
            return $this->errorResponse(
                'SUBSCRIPTION_REQUIRED',
                'This mock test requires an active Premium subscription.',
                403
            );
        }

        // Check if there is an existing in-progress attempt
        $existingInProgress = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'IN_PROGRESS')
            ->first();

        if ($existingInProgress) {
            if ($existingInProgress->isExpired()) {
                $existingInProgress->gradeAndSubmit(timedOut: true);
            } else {
                return $this->serveExamPayload($existingInProgress, $exam);
            }
        }

        // Check if completed attempt exists and exam is single attempt
        $completedAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['SUBMITTED', 'TIMED_OUT'])
            ->first();

        if ($completedAttempt) {
            return $this->errorResponse(
                'SINGLE_ATTEMPT_ONLY',
                'You have already attempted this exam. Retakes are not permitted.',
                403,
                ['attempt_id' => $completedAttempt->id]
            );
        }

        // Create new attempt
        $attempt = DB::transaction(function () use ($user, $exam) {
            $totalQ = $exam->examQuestions()->count();

            $newAttempt = ExamAttempt::create([
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'started_at' => now(),
                'status' => 'IN_PROGRESS',
                'total_questions' => $totalQ,
                'total_marks' => $exam->total_marks,
            ]);

            // Pre-seed answer slots
            foreach ($exam->examQuestions as $eq) {
                ExamAnswer::create([
                    'exam_attempt_id' => $newAttempt->id,
                    'question_id' => $eq->question_id,
                    'selected_option_id' => null,
                    'is_marked_for_review' => false,
                ]);
            }

            return $newAttempt;
        });

        return $this->serveExamPayload($attempt, $exam);
    }

    /**
     * Save an answer choice or mark for review during exam.
     */
    public function saveAnswer(int $attemptId, Request $request): JsonResponse
    {
        $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'selected_option_id' => ['nullable', 'exists:question_options,id'],
            'is_marked_for_review' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $attempt = ExamAttempt::where('id', $attemptId)->where('user_id', $user->id)->firstOrFail();

        if ($attempt->status !== 'IN_PROGRESS') {
            return $this->errorResponse('EXAM_NOT_ACTIVE', 'This exam attempt is no longer active.', 400);
        }

        if ($attempt->isExpired()) {
            $attempt->gradeAndSubmit(timedOut: true);

            return $this->errorResponse('EXAM_EXPIRED', 'Exam duration has expired. Test has been auto-submitted.', 410);
        }

        $answer = ExamAnswer::where('exam_attempt_id', $attempt->id)
            ->where('question_id', $request->question_id)
            ->first();

        if (! $answer) {
            return $this->errorResponse('QUESTION_NOT_IN_EXAM', 'Question not found in this exam.', 404);
        }

        $updates = [];
        if ($request->has('selected_option_id')) {
            $updates['selected_option_id'] = $request->selected_option_id;
            $updates['is_answered'] = ! empty($request->selected_option_id);
        }
        if ($request->has('is_marked_for_review')) {
            $updates['is_marked_for_review'] = $request->boolean('is_marked_for_review');
        }

        $answer->update($updates);

        return $this->successResponse([
            'question_id' => $request->question_id,
            'selected_option_id' => $answer->selected_option_id,
            'is_marked_for_review' => (bool) $answer->is_marked_for_review,
            'remaining_seconds' => $attempt->remainingSeconds(),
        ]);
    }

    /**
     * Submit and grade the exam attempt.
     */
    public function submit(int $attemptId, Request $request): JsonResponse
    {
        $user = $request->user();
        $attempt = ExamAttempt::where('id', $attemptId)->where('user_id', $user->id)->firstOrFail();

        if ($attempt->status === 'SUBMITTED' || $attempt->status === 'TIMED_OUT') {
            return $this->errorResponse('ALREADY_SUBMITTED', 'This exam attempt has already been submitted.', 400);
        }

        $isTimedOut = $attempt->isExpired() || $request->boolean('timed_out');
        $attempt->gradeAndSubmit(timedOut: $isTimedOut);

        return $this->successResponse([
            'attempt_id' => $attempt->id,
            'status' => $attempt->status,
            'score_obtained' => $attempt->score_obtained,
            'total_marks' => $attempt->total_marks,
            'percentage' => $attempt->percentage,
            'is_passed' => (bool) $attempt->is_passed,
        ], 'Exam submitted successfully.');
    }

    /**
     * Get detailed exam result scorecard and question review.
     */
    public function result(int $attemptId, Request $request): JsonResponse
    {
        $user = $request->user();
        $attempt = ExamAttempt::with(['exam.course', 'answers.question.options', 'answers.question.images'])
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($attempt->status === 'IN_PROGRESS') {
            return $this->errorResponse('EXAM_IN_PROGRESS', 'Exam attempt is still in progress.', 400);
        }

        $exam = $attempt->exam;
        $examQuestions = $exam->examQuestions()->get()->keyBy('question_id');

        $questionsReview = $attempt->answers->map(function ($ans) use ($examQuestions) {
            $q = $ans->question;
            $eq = $examQuestions->get($ans->question_id);
            $correctOpt = $q->options->firstWhere('is_correct', true);

            return [
                'question_id' => $q->id,
                'question_type' => $q->question_type ?? 'STANDARD_MCQ',
                'question_text' => $q->question_text,
                'question_html' => $q->question_html,
                'marks' => $eq?->marks ?? 1.0,
                'user_selected_option_id' => $ans->selected_option_id,
                'correct_option_id' => $correctOpt?->id,
                'is_correct' => (bool) $ans->is_correct,
                'marks_awarded' => (float) $ans->marks_awarded,
                'explanation_text' => $q->explanation_text,
                'explanation_html' => $q->explanation_html,
                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'option_key' => $opt->option_key,
                        'option_text' => $opt->option_text,
                        'option_html' => $opt->option_html,
                        'is_correct' => (bool) $opt->is_correct,
                    ];
                }),
            ];
        });

        return $this->successResponse([
            'attempt_id' => $attempt->id,
            'exam_title' => $exam->title,
            'course_name' => $exam->course?->name,
            'status' => $attempt->status,
            'started_at' => $attempt->started_at?->toIso8601String(),
            'submitted_at' => $attempt->submitted_at?->toIso8601String(),
            'time_spent_seconds' => $attempt->time_spent_seconds,
            'total_questions' => $attempt->total_questions,
            'attempted_questions' => $attempt->attempted_questions,
            'correct_answers' => $attempt->correct_answers,
            'incorrect_answers' => $attempt->incorrect_answers,
            'unanswered_questions' => $attempt->unanswered_questions,
            'score_obtained' => $attempt->score_obtained,
            'total_marks' => $attempt->total_marks,
            'percentage' => $attempt->percentage,
            'is_passed' => (bool) $attempt->is_passed,
            'pass_percentage' => $exam->pass_percentage,
            'questions_review' => $questionsReview,
        ]);
    }

    /**
     * Helper to build live exam payload without revealing correct answers.
     */
    protected function serveExamPayload(ExamAttempt $attempt, Exam $exam): JsonResponse
    {
        $answers = $attempt->answers()->get()->keyBy('question_id');
        $examQuestions = $exam->examQuestions()->with(['question.options', 'question.images'])->orderBy('sort_order')->get();

        $questions = $examQuestions->map(function ($eq, $idx) use ($answers) {
            $q = $eq->question;
            $userAns = $answers->get($q->id);

            return [
                'index' => $idx + 1,
                'question_id' => $q->id,
                'question_type' => $q->question_type ?? 'STANDARD_MCQ',
                'question_text' => $q->question_text,
                'question_html' => $q->question_html,
                'marks' => (float) $eq->marks,
                'negative_marks' => (float) $eq->negative_marks,
                'user_selected_option_id' => $userAns?->selected_option_id,
                'is_marked_for_review' => (bool) $userAns?->is_marked_for_review,
                'images' => $q->images->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'file_path' => asset('storage/'.$img->file_path),
                        'file_name' => $img->file_name,
                    ];
                }),
                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'option_key' => $opt->option_key,
                        'option_text' => $opt->option_text,
                        'option_html' => $opt->option_html,
                        // NEVER expose is_correct in live exam!
                    ];
                }),
            ];
        });

        return $this->successResponse([
            'attempt_id' => $attempt->id,
            'exam_id' => $exam->id,
            'exam_title' => $exam->title,
            'duration_minutes' => $exam->duration_minutes,
            'remaining_seconds' => $attempt->remainingSeconds(),
            'started_at' => $attempt->started_at->toIso8601String(),
            'questions' => $questions,
        ]);
    }
}
