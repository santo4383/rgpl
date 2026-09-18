<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Subscription Plans') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Choose a preparation plan to unlock unlimited practice questions and detailed explanations.') }}</p>
            </div>
            <a href="{{ route('student.subscriptions') }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition">
                {{ __('My Subscriptions & History') }} &rarr;
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session()->has('message'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-xs flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <!-- Pricing Grid -->
            @if(count($plans) > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($plans as $plan)
                        <div class="bg-white rounded-2xl border {{ $plan->is_all_courses ? 'border-indigo-500 ring-2 ring-indigo-500/20 shadow-md' : 'border-gray-200 shadow-xs' }} p-6 flex flex-col justify-between hover:shadow-lg transition">
                            <div>
                                @if($plan->is_all_courses)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 mb-3">
                                        ★ {{ __('ALL COURSES ACCESS') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 mb-3">
                                        {{ count($plan->courses) > 1 ? __('COMBO PLAN') : __('TARGETED PLAN') }}
                                    </span>
                                @endif

                                <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                                @if($plan->description)
                                    <p class="text-xs text-gray-500 mt-1 mb-4">{{ $plan->description }}</p>
                                @endif

                                <div class="mt-4 mb-6">
                                    <span class="text-3xl font-extrabold text-gray-900">&#8377;{{ number_format($plan->price, 0) }}</span>
                                    <span class="text-xs text-gray-500">/ {{ $plan->duration_days }} {{ __('days access') }}</span>
                                </div>

                                <!-- Included Courses -->
                                <div class="mb-5 pb-5 border-b border-gray-100">
                                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Included Coverage:') }}</div>
                                    @if($plan->is_all_courses)
                                        <div class="flex items-center text-xs font-semibold text-indigo-700">
                                            <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            {{ __('Unlimited access to ALL courses & exams') }}
                                        </div>
                                    @else
                                        <div class="space-y-1">
                                            @foreach($plan->courses as $c)
                                                <div class="flex items-center text-xs text-gray-700">
                                                    <svg class="w-3.5 h-3.5 mr-1.5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                    <span>{{ $c->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Key Features -->
                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        {{ __('Unlimited Practice Questions (no 500 limit)') }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        {{ __('Full Rich-Text & Step-by-Step Explanations') }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        {{ __('Performance Tracking & Accuracy Analytics') }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        {{ __('Bookmark & Review Mistakes Mode') }}
                                    </div>
                                </div>
                            </div>

                            <button 
                                wire:click="selectPlan({{ $plan->id }})" 
                                class="w-full py-3 px-4 rounded-xl text-sm font-bold text-center transition {{ $plan->is_all_courses ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm' : 'bg-gray-900 hover:bg-black text-white shadow-xs' }}"
                            >
                                {{ __('Subscribe Now') }}
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <p class="text-sm text-gray-500">{{ __('No active subscription plans available at the moment.') }}</p>
                </div>
            @endif

            <!-- Manual Payment Modal -->
            @if($showPaymentModal && $selectedPlan)
                <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-gray-100 overflow-hidden my-8">
                        <!-- Modal Header -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('Complete Subscription Payment') }}</h3>
                                <p class="text-xs text-gray-500">{{ $selectedPlan->name }} &bull; &#8377;{{ number_format($selectedPlan->price, 0) }} for {{ $selectedPlan->duration_days }} days</p>
                            </div>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <form wire:submit.prevent="submitPayment" class="p-6 space-y-5">
                            <!-- Payment Instructions & Bank Details Box -->
                            <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-4 text-xs text-gray-800 space-y-2">
                                <div class="font-bold text-indigo-950 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                    {{ __('Official Payment Instructions (UPI / QR / Bank)') }}
                                </div>
                                <p>{{ __('Pay the exact plan amount using any UPI app (GPay, PhonePe, Paytm) or NetBanking to our official account below, then upload the transaction receipt screenshot.') }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 pt-2 border-t border-indigo-200/60 font-mono text-xs">
                                    <div><strong>UPI ID:</strong> <span class="bg-white px-2 py-0.5 rounded border border-indigo-200">{{ \App\Models\Setting::get('payment_upi_id', 'mcqexam@okaxis') }}</span></div>
                                    <div><strong>Account No:</strong> <span class="bg-white px-2 py-0.5 rounded border border-indigo-200">{{ \App\Models\Setting::get('payment_account_number', '345678901234') }}</span></div>
                                    <div><strong>IFSC Code:</strong> <span class="bg-white px-2 py-0.5 rounded border border-indigo-200">{{ \App\Models\Setting::get('payment_ifsc', 'SBIN0001234') }}</span></div>
                                    <div><strong>Bank Name:</strong> {{ \App\Models\Setting::get('payment_bank_name', 'State Bank of India') }} ({{ \App\Models\Setting::get('payment_account_name', 'Online MCQ Examination Hub Ltd') }})</div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">{{ __('Payment Method Used') }} <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach(['UPI' => 'UPI Apps', 'QR' => 'Scanned QR', 'BANK_TRANSFER' => 'Net Banking'] as $key => $label)
                                        <label class="flex items-center justify-center p-2.5 rounded-lg border text-xs font-semibold cursor-pointer transition {{ $payment_method === $key ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'bg-white border-gray-200 hover:bg-gray-50' }}">
                                            <input type="radio" wire:model.live="payment_method" value="{{ $key }}" class="sr-only">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- UTR / Reference Number -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                                    {{ __('Transaction Reference / UTR Number') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="transaction_reference" placeholder="e.g. 423589123456 or UPI Reference ID" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                @error('transaction_reference') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Payment Receipt Upload -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                                    {{ __('Upload Payment Receipt Screenshot') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="file" wire:model="receipt" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                
                                <div wire:loading wire:target="receipt" class="text-xs text-indigo-600 mt-1">
                                    {{ __('Uploading receipt preview...') }}
                                </div>

                                @if ($receipt)
                                    <div class="mt-3 p-2 bg-gray-50 border rounded-lg max-w-xs">
                                        <span class="text-[11px] text-gray-500 font-semibold block mb-1">{{ __('Receipt Preview:') }}</span>
                                        <img src="{{ $receipt->temporaryUrl() }}" class="rounded max-h-36 object-contain">
                                    </div>
                                @endif

                                @error('receipt') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Student Notes (Optional) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">{{ __('Additional Notes (Optional)') }}</label>
                                <textarea wire:model="student_notes" rows="2" placeholder="Account holder name or payment remarks" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                @error('student_notes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-xs">
                                    {{ __('Cancel') }}
                                </button>
                                <button type="submit" wire:loading.attr="disabled" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-sm flex items-center">
                                    <span wire:loading.remove wire:target="submitPayment">{{ __('Submit for Verification') }}</span>
                                    <span wire:loading wire:target="submitPayment">{{ __('Submitting...') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
