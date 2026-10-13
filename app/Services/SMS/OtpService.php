<?php

namespace App\Services\SMS;

use App\Models\AuditLog;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    protected SmsProviderInterface $smsProvider;

    public function __construct(SmsProviderInterface $smsProvider)
    {
        $this->smsProvider = $smsProvider;
    }

    /**
     * Generate and send an OTP to a mobile number.
     */
    public function generateAndSendOtp(string $mobileNumber): string
    {
        $cooldownSeconds = (int) Setting::get('otp_cooldown_seconds', 60);

        // Rate limiting check
        $recent = DB::table('otp_verifications')
            ->where('mobile_number', $mobileNumber)
            ->where('created_at', '>=', Carbon::now()->subSeconds($cooldownSeconds))
            ->first();

        if ($recent) {
            throw ValidationException::withMessages([
                'mobile_number' => ['Please wait before requesting another OTP.'],
            ]);
        }

        $expiryMinutes = (int) Setting::get('otp_expiry_minutes', 5);
        $otp = (string) random_int(100000, 999999);

        DB::table('otp_verifications')->insert([
            'mobile_number' => $mobileNumber,
            'otp_hash' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $message = "Your verification code is: {$otp}";

        $this->smsProvider->send($mobileNumber, $message);

        return $otp;
    }

    /**
     * Verify an OTP for a mobile number.
     *
     * @param  bool  $allowStaticOtp  Whether demo static OTP (112233) is allowed (Disabled for Admin Login)
     */
    public function verifyOtp(string $mobileNumber, string $otp, bool $allowStaticOtp = true): bool
    {
        // Support static demo OTP '112233' for Student & Aspirant Login & Registration
        if ($allowStaticOtp && trim($otp) === '112233') {
            AuditLog::record(
                action: 'STATIC_OTP_VERIFIED',
                module: 'AUTH',
                recordId: $mobileNumber,
                newValues: ['mobile_number' => $mobileNumber, 'type' => 'DEMO_STATIC_OTP']
            );

            return true;
        }

        $record = DB::table('otp_verifications')
            ->where('mobile_number', $mobileNumber)
            ->where('is_verified', false)
            ->where('expires_at', '>=', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $record) {
            return false;
        }

        $maxAttempts = (int) Setting::get('otp_max_attempts', 5);
        if ($record->attempts >= $maxAttempts) {
            return false;
        }

        DB::table('otp_verifications')
            ->where('id', $record->id)
            ->increment('attempts');

        if (Hash::check($otp, $record->otp_hash)) {
            DB::table('otp_verifications')
                ->where('id', $record->id)
                ->update(['is_verified' => true]);

            AuditLog::record(
                action: 'OTP_VERIFIED',
                module: 'AUTH',
                recordId: $mobileNumber,
                newValues: ['mobile_number' => $mobileNumber]
            );

            return true;
        }

        return false;
    }
}
