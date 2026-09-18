<?php

namespace App\Http\Controllers\Api\V1\Subscriptions;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiSubscriptionController extends BaseApiController
{
    /**
     * List all active subscription plans.
     */
    public function plans(): JsonResponse
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->with('courses')
            ->orderBy('price')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'price' => (float) $plan->price,
                    'duration_days' => (int) $plan->duration_days,
                    'is_all_courses' => (bool) $plan->is_all_courses,
                    'features' => $plan->features ?? [],
                    'covered_courses' => $plan->courses->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'name' => $c->name,
                            'slug' => $c->slug,
                        ];
                    }),
                ];
            });

        return $this->successResponse($plans);
    }

    /**
     * Get student active subscription and history.
     */
    public function mySubscriptions(Request $request): JsonResponse
    {
        $user = $request->user();

        $subs = $user->subscriptions()
            ->with(['plan.courses'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'plan_name' => $sub->plan?->name,
                    'status' => $sub->status,
                    'is_active' => $sub->status === 'ACTIVE' && $sub->expires_at > now(),
                    'starts_at' => $sub->starts_at?->format('Y-m-d'),
                    'expires_at' => $sub->expires_at?->format('Y-m-d'),
                    'days_remaining' => $sub->expires_at ? max(0, (int) now()->diffInDays($sub->expires_at, false)) : 0,
                    'plan' => $sub->plan,
                ];
            });

        return $this->successResponse($subs);
    }
}
