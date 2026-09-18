<?php

namespace App\Livewire\Admin\Payments;

use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentIndex extends Component
{
    use WithPagination;

    public $statusFilter = 'ALL';

    public $search = '';

    public $selectedPayment = null;

    public $showReceiptModal = false;

    public $showRejectModal = false;

    public $rejectionReason = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewReceipt($paymentId)
    {
        $this->selectedPayment = Payment::with(['user', 'plan'])->find($paymentId);
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->selectedPayment = null;
    }

    public function openRejectModal($paymentId)
    {
        $this->selectedPayment = Payment::find($paymentId);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->selectedPayment = null;
        $this->rejectionReason = '';
    }

    public function approvePayment($paymentId)
    {
        $payment = Payment::with('plan')->find($paymentId);

        if (! $payment || $payment->status !== 'PENDING') {
            return;
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'APPROVED',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $durationDays = $payment->plan ? $payment->plan->duration_days : 30;

            Subscription::create([
                'user_id' => $payment->user_id,
                'subscription_plan_id' => $payment->subscription_plan_id,
                'payment_id' => $payment->id,
                'starts_at' => now(),
                'expires_at' => now()->addDays($durationDays),
                'status' => 'ACTIVE',
            ]);

            AuditLog::record(
                action: 'PAYMENT_APPROVED',
                module: 'PAYMENTS',
                recordId: (string) $payment->id,
                oldValues: ['status' => 'PENDING'],
                newValues: [
                    'status' => 'APPROVED',
                    'student_id' => $payment->user_id,
                    'student_name' => $payment->user ? $payment->user->name : null,
                    'amount' => $payment->amount,
                    'plan' => $payment->plan ? $payment->plan->name : null,
                ]
            );
        });

        session()->flash('message', "Payment #{$payment->id} approved and subscription activated for {$payment->user->name}!");
    }

    public function rejectPayment()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:3|max:500',
        ]);

        if (! $this->selectedPayment || $this->selectedPayment->status !== 'PENDING') {
            return;
        }

        $this->selectedPayment->update([
            'status' => 'REJECTED',
            'rejection_reason' => $this->rejectionReason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::record(
            action: 'PAYMENT_REJECTED',
            module: 'PAYMENTS',
            recordId: (string) $this->selectedPayment->id,
            oldValues: ['status' => 'PENDING'],
            newValues: [
                'status' => 'REJECTED',
                'reason' => $this->rejectionReason,
                'student_id' => $this->selectedPayment->user_id,
            ]
        );

        $this->closeRejectModal();
        session()->flash('message', 'Payment rejected with note.');
    }

    public function render()
    {
        $query = Payment::with(['user', 'plan', 'reviewer'])->latest('created_at');

        if ($this->statusFilter && $this->statusFilter !== 'ALL') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('transaction_reference', 'like', $search)
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', $search)
                            ->orWhere('mobile_number', 'like', $search);
                    });
            });
        }

        $payments = $query->paginate(15);

        return view('livewire.admin.payments.payment-index', [
            'payments' => $payments,
            'pendingCount' => Payment::where('status', 'PENDING')->count(),
            'approvedCount' => Payment::where('status', 'APPROVED')->count(),
            'rejectedCount' => Payment::where('status', 'REJECTED')->count(),
        ])->layout('layouts.app');
    }
}
