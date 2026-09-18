<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contactPhone = Setting::get('contact_phone', '+91 98765 43210');
        $contactEmail = Setting::get('contact_email', 'support@parikshamitra.com');
        $contactAddress = Setting::get('contact_address', 'FC Road, Shivaji Nagar, Pune, Maharashtra 411005');

        return view('public.contact-us', compact('contactPhone', 'contactEmail', 'contactAddress'));
    }

    public function submit(Request $request)
    {
        // 1. Honeypot check
        if (! empty($request->input('website_hp'))) {
            ContactMessage::create([
                'name' => strip_tags((string) $request->input('name')),
                'mobile' => strip_tags((string) $request->input('mobile')),
                'email' => strip_tags((string) $request->input('email')),
                'subject' => 'SPAM BOT SUBMISSION',
                'message' => strip_tags((string) $request->input('message')),
                'status' => ContactMessage::STATUS_SPAM,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
            ]);

            return back()->with('success', 'Thank you. Your message has been received.');
        }

        // 2. Validation
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        // 3. Sanitization & Storage
        $contactMessage = ContactMessage::create([
            'name' => strip_tags($validated['name']),
            'mobile' => strip_tags($validated['mobile']),
            'email' => ! empty($validated['email']) ? strip_tags($validated['email']) : null,
            'subject' => strip_tags($validated['subject']),
            'message' => strip_tags($validated['message']),
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        // 4. Audit Log
        AuditLog::record(
            action: 'CONTACT_MESSAGE_SUBMITTED',
            module: 'CONTACT_MESSAGES',
            recordId: (string) $contactMessage->id,
            oldValues: null,
            newValues: ['name' => $contactMessage->name, 'subject' => $contactMessage->subject]
        );

        return back()->with('success', 'Thank you! Your message has been submitted. Our student helpdesk will reach out shortly.');
    }
}
