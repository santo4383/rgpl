<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('My Subscriptions & Payment History') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Review your active exam access and check the status of submitted payment receipts.') }}</p>
            </div>
            <a href="{{ route('student.plans') }}" wire:navigate class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                + {{ __('View All Plans') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session()->has('message'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-xs flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <!-- Active Subscriptions Section -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900">{{ __('Active Subscriptions') }}</h3>

                @if(count($subscriptions) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($subscriptions as $sub)
                            @php
                                $isActive = $sub->isActive();
                                $daysRemaining = $isActive ? max(0, (int) now()->diffInDays($sub->expires_at, false)) : 0;
                            @endphp
                            <div class="bg-white rounded-xl border {{ $isActive ? 'border-green-300 ring-1 ring-green-200' : 'border-gray-200 opacity-75' }} p-5 shadow-xs flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-lg font-bold text-gray-900">{{ $sub->plan->name ?? 'Custom Subscription' }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $isActive ? __('ACTIVE') : __('EXPIRED') }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-500 mb-4">
                                        @if($sub->plan && $sub->plan->is_all_courses)
                                            <span class="font-semibold text-indigo-700">★ {{ __('All Courses & Exams Unlocked') }}</span>
                                        @elseif($sub->plan)
                                            <span>{{ __('Covered:') }} {{ $sub->plan->courses->pluck('name')->join(', ') }}</span>
                                        @endif
                                    </div>

                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 text-xs space-y-1">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('Started On:') }}</span>
                                            <span class="font-medium text-gray-800">{{ $sub->starts_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">{{ __('Valid Until:') }}</span>
                                            <span class="font-medium text-gray-800">{{ $sub->expires_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-xs text-gray-500">
                                        @if($isActive)
                                            <strong class="text-green-600">{{ $daysRemaining }}</strong> {{ __('days remaining') }}
                                        @else
                                            <span class="text-red-500 font-semibold">{{ __('Access expired') }}</span>
                                        @endif
                                    </span>
                                    <a href="{{ route('student.practice') }}" wire:navigate class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-2xs">
                                        {{ __('Practice Now') }} &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-8 text-center">
                        <p class="text-sm text-gray-500 mb-3">{{ __('You do not have any active subscriptions yet.') }}</p>
                        <a href="{{ route('student.plans') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-xs">
                            {{ __('Explore Available Plans') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Payment Submission History Section -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900">{{ __('Payment History & Receipt Status') }}</h3>

                @if(count($payments) > 0)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50 text-gray-600 uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-left">{{ __('Date') }}</th>
                                        <th class="px-6 py-3 text-left">{{ __('Plan') }}</th>
                                        <th class="px-6 py-3 text-left">{{ __('Amount') }}</th>
                                        <th class="px-6 py-3 text-left">{{ __('Method') }}</th>
                                        <th class="px-6 py-3 text-left">{{ __('UTR / Reference') }}</th>
                                        <th class="px-6 py-3 text-left">{{ __('Receipt') }}</th>
                                        <th class="px-6 py-3 text-left">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($payments as $payment)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                                {{ $payment->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 font-bold text-gray-900">
                                                {{ $payment->plan->name ?? 'Subscription Plan' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-extrabold text-gray-900">
                                                &#8377;{{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                                {{ $payment->payment_method }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-800">
                                                {{ $payment->transaction_reference ?: 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($payment->receipt_path)
                                                    <a href="{{ asset('storage/' . $payment->receipt_path) }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold underline">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        {{ __('View Receipt') }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">{{ __('None') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($payment->status === 'APPROVED')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                        ✓ {{ __('APPROVED') }}
                                                    </span>
                                                @elseif($payment->status === 'REJECTED')
                                                    <div>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                            ✕ {{ __('REJECTED') }}
                                                        </span>
                                                        @if($payment->rejection_reason)
                                                            <div class="text-[11px] text-red-600 mt-1 max-w-xs">
                                                                {{ $payment->rejection_reason }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse">
                                                        ⏳ {{ __('UNDER REVIEW') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-xs text-gray-500">
                        {{ __('No payment records found.') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
