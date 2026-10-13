<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\SMS\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

    /**
     * Authenticate student using mobile number and password (OTP disabled).
     */
    public function store(Request $request)
    {
        $request->validate([
            'mobile_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $mobileNumber = trim($request->input('mobile_number'));
        $throttleKey = Str::transliterate(Str::lower($mobileNumber).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'mobile_number' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        if (! Auth::attempt(['mobile_number' => $mobileNumber, 'password' => $request->password], $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'mobile_number' => __('These credentials do not match our records.'),
            ]);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'mobile_number' => __('Your account has been deactivated. Please contact support.'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        AuditLog::record(
            action: 'LOGIN',
            module: 'AUTH',
            recordId: (string) $user->id,
            newValues: ['portal' => 'STUDENT', 'method' => 'PASSWORD', 'mobile' => $user->mobile_number],
            userId: $user->id,
        );

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => ['required', 'string', 'max:20'],
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
            'mobile_number' => ['required', 'string', 'max:20'],
            'otp' => ['required', 'string'],
        ]);

        if ($this->otpService->verifyOtp($request->mobile_number, $request->otp, allowStaticOtp: true)) {
            $user = User::where('mobile_number', $request->mobile_number)->first();

            if (! $user) {
                $user = User::create([
                    'name' => 'Aspirant '.substr($request->mobile_number, -4),
                    'mobile_number' => $request->mobile_number,
                    'dob' => '2000-01-01',
                    'address' => 'Student Portal Account',
                ]);

                $studentRole = Role::firstOrCreate(['name' => 'STUDENT']);
                $user->roles()->syncWithoutDetaching([$studentRole->id]);
            }

            if (! $user->is_active) {
                throw ValidationException::withMessages([
                    'otp' => __('Your account has been deactivated. Please contact support.'),
                ]);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            session()->forget(['otp_login_mobile', 'otp_login_sent_at']);

            AuditLog::record(
                action: 'LOGIN',
                module: 'AUTH',
                recordId: (string) $user->id,
                newValues: ['portal' => 'STUDENT', 'method' => 'OTP', 'mobile' => $user->mobile_number],
                userId: $user->id,
            );

            return redirect()->intended(route('dashboard', absolute: false));
        }

        throw ValidationException::withMessages([
            'otp' => __('The provided OTP is incorrect or expired.'),
        ]);
    }
}
