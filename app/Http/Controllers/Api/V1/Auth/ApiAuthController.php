<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Role;
use App\Models\User;
use App\Services\SMS\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends BaseApiController
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Send OTP for login.
     */
    public function sendLoginOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile_number' => ['required', 'string', 'max:20'],
        ]);

        try {
            $this->otpService->generateAndSendOtp($request->mobile_number);

            return $this->successResponse(
                ['mobile_number' => $request->mobile_number],
                'OTP sent successfully.'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse('RATE_LIMITED', $e->getMessage(), 429);
        }
    }

    /**
     * Verify OTP and login user.
     */
    public function verifyLoginOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile_number' => ['required', 'string', 'max:20'],
            'otp' => ['required', 'string'],
        ]);

        if (! $this->otpService->verifyOtp($request->mobile_number, $request->otp, allowStaticOtp: true)) {
            return $this->errorResponse('OTP_INVALID', 'The provided OTP is invalid or has expired.', 422);
        }

        $user = User::with('courses')->where('mobile_number', $request->mobile_number)->first();

        if (! $user) {
            $user = User::create([
                'name' => 'Aspirant '.substr($request->mobile_number, -4),
                'mobile_number' => $request->mobile_number,
                'dob' => '2000-01-01',
                'address' => 'Student Mobile App Account',
            ]);

            $studentRole = Role::firstOrCreate(['name' => 'STUDENT']);
            $user->roles()->syncWithoutDetaching([$studentRole->id]);
            $user->load('courses');
        }

        $token = $user->createToken('parikshamitra_mobile')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatUser($user),
        ], 'Login successful.');
    }

    /**
     * Send OTP for registration.
     */
    public function sendRegisterOtp(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'unique:users,mobile_number'],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string'],
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => ['exists:courses,id'],
        ]);

        try {
            $this->otpService->generateAndSendOtp($request->mobile_number);

            return $this->successResponse(
                ['mobile_number' => $request->mobile_number],
                'Registration OTP sent successfully.'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse('RATE_LIMITED', $e->getMessage(), 429);
        }
    }

    /**
     * Verify OTP and register user.
     */
    public function verifyRegisterOtp(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'unique:users,mobile_number'],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string'],
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => ['exists:courses,id'],
            'otp' => ['required', 'string'],
        ]);

        if (! $this->otpService->verifyOtp($request->mobile_number, $request->otp)) {
            return $this->errorResponse('OTP_INVALID', 'The provided OTP is invalid or has expired.', 422);
        }

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'mobile_number' => $request->mobile_number,
                'dob' => $request->dob,
                'address' => $request->address,
            ]);

            $user->courses()->sync($request->course_ids);

            return $user;
        });

        $user->load('courses');
        $token = $user->createToken('parikshamitra_mobile')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatUser($user),
        ], 'Registration successful.', 201);
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully.');
    }

    /**
     * Get current authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['courses' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }, 'subscriptions' => function ($q) {
            $q->where('status', 'ACTIVE')->where('expires_at', '>', now())->with('plan');
        }]);

        return $this->successResponse($this->formatUser($user));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'dob' => ['sometimes', 'date'],
            'address' => ['sometimes', 'string'],
            'course_ids' => ['sometimes', 'array', 'min:1'],
            'course_ids.*' => ['exists:courses,id'],
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('dob')) {
            $user->dob = $request->dob;
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        $user->save();

        if ($request->has('course_ids')) {
            $user->courses()->sync($request->course_ids);
        }

        $user->load('courses');

        return $this->successResponse($this->formatUser($user), 'Profile updated successfully.');
    }

    /**
     * Send OTP for updating mobile number.
     */
    public function sendMobileChangeOtp(Request $request): JsonResponse
    {
        $request->validate([
            'new_mobile_number' => ['required', 'string', 'unique:users,mobile_number'],
        ]);

        try {
            $this->otpService->generateAndSendOtp($request->new_mobile_number);

            return $this->successResponse(
                ['new_mobile_number' => $request->new_mobile_number],
                'Verification OTP sent to new mobile number.'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse('RATE_LIMITED', $e->getMessage(), 429);
        }
    }

    /**
     * Verify OTP and update mobile number.
     */
    public function verifyMobileChange(Request $request): JsonResponse
    {
        $request->validate([
            'new_mobile_number' => ['required', 'string', 'unique:users,mobile_number'],
            'otp' => ['required', 'string'],
        ]);

        if (! $this->otpService->verifyOtp($request->new_mobile_number, $request->otp)) {
            return $this->errorResponse('OTP_INVALID', 'The provided OTP is invalid or expired.', 422);
        }

        $user = $request->user();
        $user->mobile_number = $request->new_mobile_number;
        $user->save();

        return $this->successResponse($this->formatUser($user), 'Mobile number updated successfully.');
    }

    /**
     * Format user array for client.
     */
    protected function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'mobile_number' => $user->mobile_number,
            'dob' => $user->dob ? ($user->dob instanceof \DateTimeInterface ? $user->dob->format('Y-m-d') : (string) $user->dob) : null,
            'address' => $user->address,
            'selected_courses' => $user->courses->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'icon' => $c->icon,
                ];
            }),
            'has_active_subscription' => $user->hasActiveSubscription(),
        ];
    }
}
