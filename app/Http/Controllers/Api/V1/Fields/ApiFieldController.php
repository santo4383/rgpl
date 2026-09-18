<?php

namespace App\Http\Controllers\Api\V1\Fields;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFieldController extends BaseApiController
{
    /**
     * List all active fields (courses) with their categories and subcategories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Course::query()->where('is_active', true)->orderBy('sort_order');

        // Optional filter: only user's enrolled fields
        if ($request->boolean('enrolled_only') && $request->user()) {
            $userCourseIds = $request->user()->courses()->pluck('courses.id')->toArray();
            $query->whereIn('id', $userCourseIds);
        }

        $fields = $query->with([
            'categories' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->with([
                    'subcategories' => function ($sq) {
                        $sq->where('is_active', true)->orderBy('sort_order');
                    },
                ]);
            },
        ])->get();

        $data = $fields->map(function ($course) use ($request) {
            $user = $request->user();

            return [
                'id' => $course->id,
                'name' => $course->name,
                'slug' => $course->slug,
                'description' => $course->description,
                'icon' => $course->icon,
                'free_question_limit' => $course->free_question_limit ?? 500,
                'is_enrolled' => $user ? $user->courses()->where('courses.id', $course->id)->exists() : false,
                'has_active_subscription' => $user ? $user->hasActiveSubscription($course->id) : false,
                'free_quota_remaining' => $user ? $user->freeQuotaRemaining($course->id) : ($course->free_question_limit ?? 500),
                'categories' => $course->categories->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                        'description' => $cat->description,
                        'icon' => $cat->icon,
                        'subcategories' => $cat->subcategories->map(function ($sub) {
                            return [
                                'id' => $sub->id,
                                'name' => $sub->name,
                                'slug' => $sub->slug,
                                'description' => $sub->description,
                            ];
                        }),
                    ];
                }),
            ];
        });

        return $this->successResponse($data);
    }

    /**
     * Get specific field details.
     */
    public function show(int $id): JsonResponse
    {
        $course = Course::with([
            'categories' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->with([
                    'subcategories' => function ($sq) {
                        $sq->where('is_active', true)->orderBy('sort_order');
                    },
                ]);
            },
        ])->find($id);

        if (! $course || ! $course->is_active) {
            return $this->errorResponse('FIELD_NOT_FOUND', 'The requested field was not found.', 404);
        }

        return $this->successResponse($course);
    }
}
