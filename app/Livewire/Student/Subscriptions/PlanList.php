<?php

namespace App\Livewire\Student\Subscriptions;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PlanList extends Component
{
    use WithFileUploads;

    public $selectedPlanId = null;

    public ?SubscriptionPlan $selectedPlan = null;

    public $showPaymentModal = false;

    // Payment Form Fields
    public $payment_method = 'UPI';

    public $transaction_reference = '';

    public $receipt;

    public $student_notes = '';

    protected $rules = [
        'payment_method' => 'required|string|in:UPI,QR,BANK_TRANSFER',
        'transaction_reference' => 'required|string|min:4|max:100',
        'receipt' => 'required|image|max:5120', // max 5MB image
        'student_notes' => 'nullable|string|max:500',
    ];

    public function selectPlan($planId)
    {
        $this->selectedPlanId = $planId;
        $this->selectedPlan = SubscriptionPlan::with('courses')->find($planId);
        $this->showPaymentModal = true;
        $this->reset(['transaction_reference', 'receipt', 'student_notes']);
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showPaymentModal = false;
        $this->selectedPlan = null;
        $this->selectedPlanId = null;
    }

    public function submitPayment()
    {
        $this->validate();

        if (! $this->selectedPlan) {
            return;
        }

        $receiptPath = $this->receipt->store('receipts', 'public');

        Payment::create([
            'user_id' => Auth::id(),
            'subscription_plan_id' => $this->selectedPlan->id,
            'amount' => $this->selectedPlan->price,
            'payment_method' => $this->payment_method,
            'transaction_reference' => $this->transaction_reference,
            'receipt_path' => $receiptPath,
            'student_notes' => $this->student_notes,
            'status' => 'PENDING',
        ]);

        session()->flash('message', 'Payment receipt submitted successfully! Our team will verify and activate your subscription shortly.');

        return redirect()->route('student.subscriptions');
    }

    public function render()
    {
        $plans = SubscriptionPlan::with('courses')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('livewire.student.subscriptions.plan-list', [
            'plans' => $plans,
        ])->layout('layouts.app');
    }
}
