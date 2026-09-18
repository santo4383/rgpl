<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Online MCQ Practice & Examination Platform', 'group' => 'general', 'type' => 'string'],
            ['key' => 'contact_email', 'value' => 'support@mcqplatform.test', 'group' => 'general', 'type' => 'string'],
            ['key' => 'contact_phone', 'value' => '+91 98765 43210', 'group' => 'general', 'type' => 'string'],

            // Practice
            ['key' => 'default_free_limit', 'value' => '500', 'group' => 'practice', 'type' => 'integer'],

            // OTP
            ['key' => 'otp_expiry_minutes', 'value' => '5', 'group' => 'otp', 'type' => 'integer'],
            ['key' => 'otp_cooldown_seconds', 'value' => '60', 'group' => 'otp', 'type' => 'integer'],
            ['key' => 'otp_max_attempts', 'value' => '5', 'group' => 'otp', 'type' => 'integer'],

            // Payment
            ['key' => 'payment_upi_id', 'value' => 'mcqexam@okaxis', 'group' => 'payment', 'type' => 'string'],
            ['key' => 'payment_bank_name', 'value' => 'State Bank of India', 'group' => 'payment', 'type' => 'string'],
            ['key' => 'payment_account_name', 'value' => 'Online MCQ Examination Hub Ltd', 'group' => 'payment', 'type' => 'string'],
            ['key' => 'payment_account_number', 'value' => '345678901234', 'group' => 'payment', 'type' => 'string'],
            ['key' => 'payment_ifsc', 'value' => 'SBIN0001234', 'group' => 'payment', 'type' => 'string'],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'group' => $item['group'],
                    'type' => $item['type'],
                ]
            );
        }
    }
}
