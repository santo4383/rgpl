<?php

namespace App\Http\Controllers\Api\V1\Bookmarks;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Bookmark;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBookmarkController extends BaseApiController
{
    /**
     * List user bookmarks.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $bookmarks = Bookmark::where('user_id', $user->id)
            ->with(['question.course', 'question.category', 'question.options', 'question.images'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $formatted = $bookmarks->getCollection()->map(function ($b) {
            $q = $b->question;

            return [
                'bookmark_id' => $b->id,
                'bookmarked_at' => $b->created_at->toIso8601String(),
                'question' => [
                    'id' => $q->id,
                    'course_name' => $q->course?->name,
                    'category_name' => $q->category?->name,
                    'question_type' => $q->question_type ?? 'STANDARD_MCQ',
                    'question_text' => $q->question_text,
                    'question_html' => $q->question_html,
                    'difficulty' => $q->difficulty,
                    'options' => $q->options->map(fn ($opt) => [
                        'id' => $opt->id,
                        'option_key' => $opt->option_key,
                        'option_text' => $opt->option_text,
                        'option_html' => $opt->option_html,
                        'is_correct' => (bool) $opt->is_correct,
                    ]),
                    'explanation_text' => $q->explanation_text,
                    'explanation_html' => $q->explanation_html,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted,
            'pagination' => [
                'current_page' => $bookmarks->currentPage(),
                'per_page' => $bookmarks->perPage(),
                'total' => $bookmarks->total(),
                'last_page' => $bookmarks->lastPage(),
                'has_more' => $bookmarks->hasMorePages(),
            ],
        ]);
    }

    /**
     * Toggle bookmark state for a question.
     */
    public function toggle(int $questionId, Request $request): JsonResponse
    {
        $user = $request->user();
        $question = Question::findOrFail($questionId);

        $existing = Bookmark::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return $this->successResponse([
                'question_id' => $question->id,
                'is_bookmarked' => false,
            ], 'Bookmark removed.');
        }

        Bookmark::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
        ]);

        return $this->successResponse([
            'question_id' => $question->id,
            'is_bookmarked' => true,
        ], 'Question bookmarked successfully.');
    }
}
