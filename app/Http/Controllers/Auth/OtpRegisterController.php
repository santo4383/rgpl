<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Setting;
use App\Models\User;
use App\Services\SMS\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpRegisterController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function create(Request $request)
    {
        // Allow user to reset / edit registration details
        if ($request->has('reset') || $request->has('change')) {
            session()->forget(['otp_register_data', 'otp_register_sent_at']);

            return redirect()->route('register');
        }

        // Check if OTP session has expired or is legacy without timestamp
        if (session()->has('otp_register_data')) {
            $sentAt = session('otp_register_sent_at');
            $expirySeconds = (int) Setting::get('otp_expiry_minutes', 5) * 60;

            if (! $sentAt || (now()->timestamp - $sentAt) > $expirySeconds) {
                session()->forget(['otp_register_data', 'otp_register_sent_at']);
            }
        }

        $courses = Course::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('auth.otp-register', compact('courses'));
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'unique:users,mobile_number'],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['exists:courses,id'],
        ]);

        $this->otpService->generateAndSendOtp($request->mobile_number);

        session([
            'otp_register_data' => $request->only('name', 'mobile_number', 'dob', 'address', 'course_ids'),
            'otp_register_sent_at' => now()->timestamp,
        ]);

        return back()->with('status', 'OTP sent successfully. Please check your mobile.');
    }

    public function verifyOtpAndRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'unique:users,mobile_number'],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['exists:courses,id'],
            'otp' => ['required', 'string'],
        ]);

        if ($this->otpService->verifyOtp($request->mobile_number, $request->otp)) {
            $user = User::create([
                'name' => $request->name,
                'mobile_number' => $request->mobile_number,
                'dob' => $request->dob,
                'address' => $request->address,
            ]);

            if (! empty($request->course_ids)) {
                $user->courses()->sync($request->course_ids);
            }

            Auth::login($user);

            session()->forget(['otp_register_data', 'otp_register_sent_at']);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        throw ValidationException::withMessages([
            'otp' => __('The provided OTP is incorrect or expired.'),
        ]);
    }
}
