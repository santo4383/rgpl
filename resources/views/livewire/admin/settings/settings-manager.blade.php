<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Platform Settings') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('Manage dynamic application parameters, practice limits, OTP cooldowns, and payment credentials.') }}</p>
            </div>
            <button wire:click="save" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition">
                {{ __('Save All Settings') }}
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session()->has('message'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('message') }}
                </div>
            @endif

            <!-- Tabs -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-2 flex flex-wrap gap-2">
                <button 
                    wire:click="$set('activeTab', 'general')" 
                    class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $activeTab === 'general' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    {{ __('General') }}
                </button>
                <button 
                    wire:click="$set('activeTab', 'practice')" 
                    class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $activeTab === 'practice' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    {{ __('Practice & Quotas') }}
                </button>
                <button 
                    wire:click="$set('activeTab', 'otp')" 
                    class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $activeTab === 'otp' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    {{ __('OTP & Auth') }}
                </button>
                <button 
                    wire:click="$set('activeTab', 'payment')" 
                    class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $activeTab === 'payment' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    {{ __('Bank & Payment') }}
                </button>
            </div>

            <!-- Settings Form Card -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6 sm:p-8">
                <form wire:submit="save" class="space-y-6">

                    <!-- TAB 1: General -->
                    @if($activeTab === 'general')
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2">{{ __('General Platform Information') }}</h3>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Platform Name') }} *</label>
                                <input type="text" wire:model="site_name" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                @error('site_name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Support Email') }} *</label>
                                    <input type="email" wire:model="contact_email" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('contact_email') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Support Phone / Helpline') }}</label>
                                    <input type="text" wire:model="contact_phone" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('contact_phone') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- TAB 2: Practice -->
                    @if($activeTab === 'practice')
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2">{{ __('Practice Mode & Free Quota Policy') }}</h3>

                            <div class="max-w-md">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Default Free Question Limit per Course') }} *</label>
                                <input type="number" min="1" wire:model="default_free_limit" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="text-[11px] text-gray-500 mt-1 block">{{ __('Number of unique questions a student can practice for free in each course before requiring a subscription.') }}</span>
                                @error('default_free_limit') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <!-- TAB 3: OTP -->
                    @if($activeTab === 'otp')
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2">{{ __('OTP Verification & Security Settings') }}</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Expiry Duration (Minutes)') }} *</label>
                                    <input type="number" min="1" max="60" wire:model="otp_expiry_minutes" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="text-[11px] text-gray-500 mt-1 block">{{ __('Time before generated OTP expires.') }}</span>
                                    @error('otp_expiry_minutes') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Resend Cooldown (Seconds)') }} *</label>
                                    <input type="number" min="10" max="600" wire:model="otp_cooldown_seconds" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="text-[11px] text-gray-500 mt-1 block">{{ __('Minimum wait before requesting another OTP.') }}</span>
                                    @error('otp_cooldown_seconds') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Maximum Attempts per Code') }} *</label>
                                    <input type="number" min="1" max="20" wire:model="otp_max_attempts" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="text-[11px] text-gray-500 mt-1 block">{{ __('Incorrect attempts before code invalidates.') }}</span>
                                    @error('otp_max_attempts') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- TAB 4: Payment -->
                    @if($activeTab === 'payment')
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2">{{ __('Manual Payment Credentials & Bank Details') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('These details are rendered inside the student subscription payment modal for UPI and NetBanking.') }}</p>

                            <div class="max-w-md">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Official UPI ID (VPA)') }} *</label>
                                <input type="text" wire:model="payment_upi_id" placeholder="e.g. mcqhub@okaxis" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                                @error('payment_upi_id') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Bank Name') }} *</label>
                                    <input type="text" wire:model="payment_bank_name" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('payment_bank_name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Account Beneficiary Name') }} *</label>
                                    <input type="text" wire:model="payment_account_name" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('payment_account_name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Account Number') }} *</label>
                                    <input type="text" wire:model="payment_account_number" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                                    @error('payment_account_number') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Bank IFSC Code') }} *</label>
                                    <input type="text" wire:model="payment_ifsc" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 font-mono uppercase">
                                    @error('payment_ifsc') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                            {{ __('Save All Settings') }}
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
