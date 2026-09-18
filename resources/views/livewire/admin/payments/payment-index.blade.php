<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Verify Student Payments') }}
            </h2>
            <div class="flex items-center space-x-2 text-xs">
                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full font-bold">
                    {{ $pendingCount }} {{ __('Pending Verification') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session()->has('message'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg text-sm flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <!-- Filter Controls -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Status Pills -->
                <div class="flex items-center space-x-2">
                    <button wire:click="$set('statusFilter', 'ALL')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $statusFilter === 'ALL' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ __('All') }}
                    </button>
                    <button wire:click="$set('statusFilter', 'PENDING')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $statusFilter === 'PENDING' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                        {{ __('Pending') }} ({{ $pendingCount }})
                    </button>
                    <button wire:click="$set('statusFilter', 'APPROVED')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $statusFilter === 'APPROVED' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-800 hover:bg-green-100' }}">
                        {{ __('Approved') }} ({{ $approvedCount }})
                    </button>
                    <button wire:click="$set('statusFilter', 'REJECTED')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $statusFilter === 'REJECTED' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-800 hover:bg-red-100' }}">
                        {{ __('Rejected') }} ({{ $rejectedCount }})
                    </button>
                </div>

                <!-- Search Input -->
                <div class="w-full md:w-72">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search student name, mobile, UTR..." class="w-full text-xs border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Payments Table -->
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-600 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5 text-left">{{ __('Student') }}</th>
                                <th class="px-6 py-3.5 text-left">{{ __('Plan & Amount') }}</th>
                                <th class="px-6 py-3.5 text-left">{{ __('Method & UTR') }}</th>
                                <th class="px-6 py-3.5 text-left">{{ __('Proof') }}</th>
                                <th class="px-6 py-3.5 text-left">{{ __('Date') }}</th>
                                <th class="px-6 py-3.5 text-left">{{ __('Status') }}</th>
                                <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 text-sm">{{ $payment->user->name ?? 'User #' . $payment->user_id }}</div>
                                        <div class="text-gray-500">{{ $payment->user->mobile_number ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800">{{ $payment->plan->name ?? 'Plan' }}</div>
                                        <div class="font-extrabold text-indigo-700">&#8377;{{ number_format($payment->amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-700">{{ $payment->payment_method }}</div>
                                        <div class="font-mono text-gray-900 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200 inline-block mt-0.5">
                                            {{ $payment->transaction_reference ?: 'N/A' }}
                                        </div>
                                        @if($payment->student_notes)
                                            <div class="text-[11px] text-gray-400 mt-1 italic max-w-xs">{{ $payment->student_notes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($payment->receipt_path)
                                            <button wire:click="viewReceipt({{ $payment->id }})" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-bold underline cursor-pointer">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                {{ __('Receipt') }}
                                            </button>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                        {{ $payment->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->status === 'APPROVED')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-green-100 text-green-800">
                                                ✓ {{ __('Approved') }}
                                            </span>
                                        @elseif($payment->status === 'REJECTED')
                                            <div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-red-100 text-red-800">
                                                    ✕ {{ __('Rejected') }}
                                                </span>
                                                @if($payment->rejection_reason)
                                                    <div class="text-[10px] text-red-600 mt-1 max-w-xs">{{ $payment->rejection_reason }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800">
                                                ⏳ {{ __('Pending') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        @if($payment->status === 'PENDING')
                                            <div class="flex items-center justify-end space-x-2">
                                                <button 
                                                    wire:click="approvePayment({{ $payment->id }})" 
                                                    wire:confirm="Are you sure you want to approve this payment and activate student's subscription?" 
                                                    class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-md font-bold shadow-2xs transition"
                                                >
                                                    {{ __('Approve') }}
                                                </button>
                                                <button 
                                                    wire:click="openRejectModal({{ $payment->id }})" 
                                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-md font-bold border border-red-200 transition"
                                                >
                                                    {{ __('Reject') }}
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-[11px] text-gray-400">
                                                {{ __('Reviewed by') }} {{ $payment->reviewer->name ?? 'Admin' }}<br>
                                                {{ $payment->reviewed_at ? $payment->reviewed_at->format('M d, H:i') : '' }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        {{ __('No payment submissions found matching your filters.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $payments->links() }}
                </div>
            </div>

            <!-- Receipt Modal -->
            @if($showReceiptModal && $selectedPayment)
                <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-200 overflow-hidden my-8">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">{{ __('Payment Receipt Proof') }}</h3>
                                <p class="text-xs text-gray-500">{{ $selectedPayment->user->name ?? 'Student' }} &bull; UTR: {{ $selectedPayment->transaction_reference }}</p>
                            </div>
                            <button wire:click="closeReceiptModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="p-6 flex items-center justify-center bg-gray-100">
                            @if($selectedPayment->receipt_path)
                                <img src="{{ asset('storage/' . $selectedPayment->receipt_path) }}" alt="Receipt" class="max-h-[70vh] rounded-lg shadow-sm object-contain">
                            @else
                                <p class="text-sm text-gray-500">No image file found.</p>
                            @endif
                        </div>
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <button wire:click="closeReceiptModal" class="px-4 py-1.5 bg-gray-800 text-white rounded-lg text-xs font-semibold">
                                {{ __('Close') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Reject Reason Modal -->
            @if($showRejectModal && $selectedPayment)
                <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-200 overflow-hidden my-8">
                        <div class="px-6 py-4 bg-red-50 border-b border-red-100 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-red-900">{{ __('Reject Payment Submission') }}</h3>
                            <button wire:click="closeRejectModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="rejectPayment" class="p-6 space-y-4">
                            <p class="text-xs text-gray-600">
                                {{ __('Please provide a clear reason for rejecting this payment submission so the student knows how to resolve it.') }}
                            </p>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">{{ __('Rejection Reason') }} <span class="text-red-500">*</span></label>
                                <textarea wire:model="rejectionReason" rows="3" placeholder="e.g. UTR number not found in bank records / Amount mismatch" class="w-full text-xs border-gray-300 rounded-lg shadow-xs focus:border-red-500 focus:ring-red-500"></textarea>
                                @error('rejectionReason') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex justify-end space-x-3 pt-2">
                                <button type="button" wire:click="closeRejectModal" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-xs">
                                    {{ __('Cancel') }}
                                </button>
                                <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold shadow-xs">
                                    {{ __('Confirm Rejection') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
