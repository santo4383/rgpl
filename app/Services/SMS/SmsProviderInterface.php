<?php

namespace App\Services\SMS;

interface SmsProviderInterface
{
    /**
     * Send an SMS message.
     */
    public function send(string $mobileNumber, string $message): bool;
}
