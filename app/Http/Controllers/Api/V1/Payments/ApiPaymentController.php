<?php

namespace App\Http\Controllers\Api\V1\Payments;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiPaymentController extends BaseApiController
{
    /**
     * Get payment instructions, QR details, and bank account credentials.
     */
    public function instructions(Request $request): JsonResponse
    {
        $planId = $request->input('plan_id');
        $plan = $planId ? SubscriptionPlan::find($planId) : null;

        $upiId = Setting::get('payment_upi_id', 'mcqexam@okaxis');
        $accountName = Setting::get('payment_account_name', 'ParikshaMitra Education');
        $amount = $plan ? $plan->price : null;

        // Construct standard UPI deep link / QR string
        $upiUrl = "upi://pay?pa={$upiId}&pn=".urlencode($accountName);
        if ($amount) {
            $upiUrl .= "&am={$amount}&cu=INR";
        }

        return $this->successResponse([
            'upi_id' => $upiId,
            'upi_url' => $upiUrl,
            'qr_image_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($upiUrl),
            'bank_name' => Setting::get('payment_bank_name', 'State Bank of India'),
            'account_name' => $accountName,
            'account_number' => Setting::get('payment_account_number', '345678901234'),
            'ifsc' => Setting::get('payment_ifsc', 'SBIN0001234'),
            'plan' => $plan ? [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'duration_days' => $plan->duration_days,
            ] : null,
        ]);
    }

    /**
     * Submit payment details with UTR number and receipt upload (JPG, JPEG, PNG, PDF).
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'transaction_reference' => ['required', 'string', 'min:6', 'max:50'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // max 5MB
            'student_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);

        // Check if transaction_reference is already used
        $existing = Payment::where('transaction_reference', $request->transaction_reference)
            ->where('status', '!=', 'REJECTED')
            ->first();

        if ($existing) {
            return $this->errorResponse(
                'DUPLICATE_UTR',
                'This UTR / Transaction reference has already been submitted and is currently being processed or approved.',
                422
            );
        }

        $receiptPath = $request->file('receipt')->store('receipts', 'public');

        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price,
            'payment_method' => 'MANUAL_UPI_BANK',
            'transaction_reference' => strtoupper(trim($request->transaction_reference)),
            'receipt_path' => $receiptPath,
            'student_notes' => $request->student_notes,
            'status' => 'PENDING',
        ]);

        AuditLog::record(
            action: 'PAYMENT_SUBMITTED',
            module: 'PAYMENT',
            recordId: (string) $payment->id,
            newValues: [
                'amount' => $payment->amount,
                'reference' => $payment->transaction_reference,
            ]
        );

        return $this->successResponse([
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'transaction_reference' => $payment->transaction_reference,
            'amount' => (float) $payment->amount,
            'receipt_url' => asset('storage/'.$receiptPath),
        ], 'Payment submitted successfully. Your subscription will be activated upon admin verification.', 201);
    }

    /**
     * List user payments with current approval statuses.
     */
    public function myPayments(Request $request): JsonResponse
    {
        $user = $request->user();

        $payments = $user->payments()
            ->with('plan')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'plan_name' => $p->plan?->name,
                    'amount' => (float) $p->amount,
                    'transaction_reference' => $p->transaction_reference,
                    'payment_method' => $p->payment_method,
                    'receipt_url' => $p->receipt_path ? asset('storage/'.$p->receipt_path) : null,
                    'status' => $p->status, // PENDING, UNDER_REVIEW, APPROVED, REJECTED
                    'rejection_reason' => $p->rejection_reason,
                    'created_at' => $p->created_at->format('Y-m-d H:i'),
                    'reviewed_at' => $p->reviewed_at?->format('Y-m-d H:i'),
                ];
            });

        return $this->successResponse($payments);
    }
}
