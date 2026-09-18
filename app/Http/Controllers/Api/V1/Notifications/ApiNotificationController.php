<?php

namespace App\Http\Controllers\Api\V1\Notifications;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Announcement;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiNotificationController extends BaseApiController
{
    /**
     * Register or update client FCM device token.
     */
    public function registerToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'platform' => ['nullable', 'string', 'in:android,ios'],
            'device_id' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        DB::table('device_tokens')->updateOrInsert(
            [
                'user_id' => $user->id,
                'token' => $request->token,
            ],
            [
                'platform' => $request->platform,
                'device_id' => $request->device_id,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return $this->successResponse(null, 'FCM token registered successfully.');
    }

    /**
     * In-App notification center feed: combines payment updates, announcements, and alerts.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = collect();

        // 1. Payment status notifications
        $payments = Payment::where('user_id', $user->id)->latest()->take(5)->get();
        foreach ($payments as $p) {
            if ($p->status === 'APPROVED') {
                $notifications->push([
                    'id' => 'pay_'.$p->id,
                    'type' => 'PAYMENT_APPROVED',
                    'title' => 'Payment Approved!',
                    'message' => "Your manual payment for {$p->plan?->name} has been verified and approved. Your premium subscription is now active.",
                    'created_at' => $p->reviewed_at?->toIso8601String() ?? $p->updated_at->toIso8601String(),
                    'is_read' => true,
                ]);
            } elseif ($p->status === 'REJECTED') {
                $notifications->push([
                    'id' => 'pay_'.$p->id,
                    'type' => 'PAYMENT_REJECTED',
                    'title' => 'Payment Verification Failed',
                    'message' => 'Your payment was rejected: '.($p->rejection_reason ?: 'Invalid UTR or receipt proof. Please review and re-submit.'),
                    'created_at' => $p->reviewed_at?->toIso8601String() ?? $p->updated_at->toIso8601String(),
                    'is_read' => true,
                ]);
            }
        }

        // 2. High priority announcements
        $announcements = Announcement::where('status', 'PUBLISHED')
            ->where('priority', 'HIGH')
            ->latest()
            ->take(5)
            ->get();

        foreach ($announcements as $a) {
            $notifications->push([
                'id' => 'ann_'.$a->id,
                'type' => 'ANNOUNCEMENT',
                'title' => $a->title,
                'message' => $a->short_description,
                'created_at' => $a->created_at->toIso8601String(),
                'is_read' => false,
            ]);
        }

        return $this->successResponse($notifications->sortByDesc('created_at')->values());
    }
}
