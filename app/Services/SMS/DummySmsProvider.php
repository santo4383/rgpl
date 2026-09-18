<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Log;

class DummySmsProvider implements SmsProviderInterface
{
    public function send(string $mobileNumber, string $message): bool
    {
        Log::info("DUMMY SMS to {$mobileNumber}: {$message}");

        return true;
    }
}
