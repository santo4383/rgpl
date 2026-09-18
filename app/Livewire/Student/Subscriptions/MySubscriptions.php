<?php

namespace App\Livewire\Student\Subscriptions;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MySubscriptions extends Component
{
    public function render()
    {
        $user = Auth::user();

        $subscriptions = Subscription::with(['plan.courses'])
            ->where('user_id', $user->id)
            ->latest('starts_at')
            ->get();

        $payments = Payment::with(['plan'])
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->get();

        return view('livewire.student.subscriptions.my-subscriptions', [
            'subscriptions' => $subscriptions,
            'payments' => $payments,
        ])->layout('layouts.app');
    }
}
