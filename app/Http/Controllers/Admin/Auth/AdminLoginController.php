<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Services\SMS\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Display the admin login view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('SUPER_ADMIN') || $user->hasRole('ADMIN')) {
                return redirect()->route('admin.dashboard');
            }
        }

        // Allow administrator to reset/cancel ongoing OTP flow
        if ($request->has('reset') || $request->has('change')) {
            session()->forget(['admin_otp_mobile', 'admin_otp_sent_at', 'admin_dev_otp']);

            return redirect()->route('admin.login');
        }

        // Auto-expire OTP session if time limit passed
        if (session()->has('admin_otp_mobile')) {
            $sentAt = session('admin_otp_sent_at');
            $expirySeconds = (int) Setting::get('otp_expiry_minutes', 5) * 60;

            if (! $sentAt || (now()->timestamp - $sentAt) > $expirySeconds) {
                session()->forget(['admin_otp_mobile', 'admin_otp_sent_at', 'admin_dev_otp']);
            }
        }

        return view('admin.auth.login');
    }

    /**
     * Handle an incoming administrative authentication request via password.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($request->input('login'));

        // Allow authentication via email or mobile number
        $user = User::where('email', $login)
            ->orWhere('mobile_number', $login)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => __('These credentials do not match our records.'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => __('This account has been deactivated. Please contact an administrator.'),
            ]);
        }

        // Strict authorization: Only users with SUPER_ADMIN or ADMIN role may authenticate
        if (! $user->hasRole('SUPER_ADMIN') && ! $user->hasRole('ADMIN')) {
            AuditLog::record(
                action: 'UNAUTHORIZED_ADMIN_LOGIN',
                module: 'AUTH',
                recordId: (string) $user->id,
                oldValues: null,
                newValues: ['portal' => 'ADMIN', 'email' => $user->email, 'reason' => 'Non-admin user attempted admin login'],
                userId: $user->id,
            );

            throw ValidationException::withMessages([
                'login' => __('Access denied. This account does not have administrative privileges.'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        session()->forget(['admin_otp_mobile', 'admin_otp_sent_at', 'admin_dev_otp']);

        // Record security audit log
        AuditLog::record(
            action: 'LOGIN',
            module: 'AUTH',
            recordId: (string) $user->id,
            oldValues: null,
            newValues: ['portal' => 'ADMIN', 'method' => 'PASSWORD', 'email' => $user->email],
            userId: $user->id,
        );

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Send OTP to an authorized administrator's mobile number.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'mobile_number' => ['required', 'string'],
        ]);

        $mobile = trim($request->input('mobile_number'));

        $user = User::where('mobile_number', $mobile)->first();

        // Verify administrator exists and has administrative privileges
        if (! $user || (! $user->hasRole('SUPER_ADMIN') && ! $user->hasRole('ADMIN'))) {
            AuditLog::record(
                action: 'UNAUTHORIZED_ADMIN_OTP_REQUEST',
                module: 'AUTH',
                recordId: $user ? (string) $user->id : null,
                oldValues: null,
                newValues: ['portal' => 'ADMIN', 'mobile_number' => $mobile, 'reason' => 'Non-admin requested admin OTP'],
            );

            throw ValidationException::withMessages([
                'mobile_number' => __('Access denied. No administrator account is registered with this mobile number.'),
            ]);
        }

        $otp = $this->otpService->generateAndSendOtp($mobile);

        session([
            'admin_otp_mobile' => $mobile,
            'admin_otp_sent_at' => now()->timestamp,
        ]);

        if (app()->environment('local', 'testing') || config('app.debug')) {
            session(['admin_dev_otp' => $otp]);
        }

        return redirect()->route('admin.login', ['tab' => 'otp'])->with('status', "Verification OTP sent successfully to +91 {$mobile}.");
    }

    /**
     * Verify the admin OTP and authenticate.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'mobile_number' => ['required', 'string'],
            'otp' => ['required', 'string'],
        ]);

        $mobile = trim($request->input('mobile_number'));
        $otp = trim($request->input('otp'));

        $user = User::where('mobile_number', $mobile)->first();

        if (! $user || (! $user->hasRole('SUPER_ADMIN') && ! $user->hasRole('ADMIN'))) {
            throw ValidationException::withMessages([
                'otp' => __('Access denied. Administrative privileges required.'),
            ]);
        }

        if ($this->otpService->verifyOtp($mobile, $otp, allowStaticOtp: false)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            session()->forget(['admin_otp_mobile', 'admin_otp_sent_at', 'admin_dev_otp']);

            AuditLog::record(
                action: 'LOGIN',
                module: 'AUTH',
                recordId: (string) $user->id,
                oldValues: null,
                newValues: ['portal' => 'ADMIN', 'method' => 'OTP', 'mobile' => $mobile],
                userId: $user->id,
            );

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'otp' => __('The provided OTP is incorrect or expired.'),
        ]);
    }

    /**
     * Destroy an authenticated administrative session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        if ($userId) {
            AuditLog::record(
                action: 'LOGOUT',
                module: 'AUTH',
                recordId: (string) $userId,
                oldValues: null,
                newValues: ['portal' => 'ADMIN'],
                userId: $userId,
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have been signed out of the admin portal.');
    }
}
