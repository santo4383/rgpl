<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Services\SMS\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpLoginController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function create(Request $request)
    {
        // Allow user to reset / restart login flow
        if ($request->has('reset') || $request->has('change')) {
            session()->forget(['otp_login_mobile', 'otp_login_sent_at']);

            return redirect()->route('login');
        }

        // Check if OTP session has expired or is legacy without timestamp
        if (session()->has('otp_login_mobile')) {
            $sentAt = session('otp_login_sent_at');
            $expirySeconds = (int) Setting::get('otp_expiry_minutes', 5) * 60;

            if (! $sentAt || (now()->timestamp - $sentAt) > $expirySeconds) {
                session()->forget(['otp_login_mobile', 'otp_login_sent_at']);
            }
        }

        return view('auth.otp-login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => ['required', 'string', 'exists:users,mobile_number'],
        ]);

        $this->otpService->generateAndSendOtp($request->mobile_number);

        session([
            'otp_login_mobile' => $request->mobile_number,
            'otp_login_sent_at' => now()->timestamp,
        ]);

        return back()->with('status', 'OTP sent successfully. Please check your mobile.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => ['required', 'string', 'exists:users,mobile_number'],
            'otp' => ['required', 'string'],
        ]);

        if ($this->otpService->verifyOtp($request->mobile_number, $request->otp)) {
            $user = User::where('mobile_number', $request->mobile_number)->first();
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            session()->forget(['otp_login_mobile', 'otp_login_sent_at']);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        throw ValidationException::withMessages([
            'otp' => __('The provided OTP is incorrect or expired.'),
        ]);
    }
}
