<?php

namespace App\Livewire\Admin\Settings;

use App\Models\AuditLog;
use App\Models\Setting;
use Livewire\Component;

class SettingsManager extends Component
{
    public string $activeTab = 'general'; // general, practice, otp, payment

    // General
    public string $site_name = '';

    public string $contact_email = '';

    public string $contact_phone = '';

    // Practice
    public int $default_free_limit = 500;

    // OTP
    public int $otp_expiry_minutes = 5;

    public int $otp_cooldown_seconds = 60;

    public int $otp_max_attempts = 5;

    // Payment
    public string $payment_upi_id = '';

    public string $payment_bank_name = '';

    public string $payment_account_name = '';

    public string $payment_account_number = '';

    public string $payment_ifsc = '';

    public function mount()
    {
        $this->site_name = (string) Setting::get('site_name', 'Online MCQ Practice & Examination Platform');
        $this->contact_email = (string) Setting::get('contact_email', 'support@mcqplatform.test');
        $this->contact_phone = (string) Setting::get('contact_phone', '+91 98765 43210');

        $this->default_free_limit = (int) Setting::get('default_free_limit', 500);

        $this->otp_expiry_minutes = (int) Setting::get('otp_expiry_minutes', 5);
        $this->otp_cooldown_seconds = (int) Setting::get('otp_cooldown_seconds', 60);
        $this->otp_max_attempts = (int) Setting::get('otp_max_attempts', 5);

        $this->payment_upi_id = (string) Setting::get('payment_upi_id', 'mcqexam@okaxis');
        $this->payment_bank_name = (string) Setting::get('payment_bank_name', 'State Bank of India');
        $this->payment_account_name = (string) Setting::get('payment_account_name', 'Online MCQ Examination Hub Ltd');
        $this->payment_account_number = (string) Setting::get('payment_account_number', '345678901234');
        $this->payment_ifsc = (string) Setting::get('payment_ifsc', 'SBIN0001234');
    }

    public function save()
    {
        $this->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'default_free_limit' => 'required|integer|min:1|max:100000',
            'otp_expiry_minutes' => 'required|integer|min:1|max:60',
            'otp_cooldown_seconds' => 'required|integer|min:10|max:600',
            'otp_max_attempts' => 'required|integer|min:1|max:20',
            'payment_upi_id' => 'required|string|max:100',
            'payment_bank_name' => 'required|string|max:150',
            'payment_account_name' => 'required|string|max:150',
            'payment_account_number' => 'required|string|max:50',
            'payment_ifsc' => 'required|string|max:30',
        ]);

        $oldValues = [
            'site_name' => Setting::get('site_name'),
            'default_free_limit' => Setting::get('default_free_limit'),
            'payment_upi_id' => Setting::get('payment_upi_id'),
        ];

        // General
        Setting::set('site_name', $this->site_name, 'general', 'string');
        Setting::set('contact_email', $this->contact_email, 'general', 'string');
        Setting::set('contact_phone', $this->contact_phone, 'general', 'string');

        // Practice
        Setting::set('default_free_limit', $this->default_free_limit, 'practice', 'integer');

        // OTP
        Setting::set('otp_expiry_minutes', $this->otp_expiry_minutes, 'otp', 'integer');
        Setting::set('otp_cooldown_seconds', $this->otp_cooldown_seconds, 'otp', 'integer');
        Setting::set('otp_max_attempts', $this->otp_max_attempts, 'otp', 'integer');

        // Payment
        Setting::set('payment_upi_id', $this->payment_upi_id, 'payment', 'string');
        Setting::set('payment_bank_name', $this->payment_bank_name, 'payment', 'string');
        Setting::set('payment_account_name', $this->payment_account_name, 'payment', 'string');
        Setting::set('payment_account_number', $this->payment_account_number, 'payment', 'string');
        Setting::set('payment_ifsc', $this->payment_ifsc, 'payment', 'string');

        // Record Audit Log
        AuditLog::record(
            action: 'SETTINGS_UPDATED',
            module: 'SETTINGS',
            recordId: 'GLOBAL',
            oldValues: $oldValues,
            newValues: [
                'site_name' => $this->site_name,
                'default_free_limit' => $this->default_free_limit,
                'payment_upi_id' => $this->payment_upi_id,
            ]
        );

        session()->flash('message', 'Application settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.settings-manager')->layout('layouts.app');
    }
}
