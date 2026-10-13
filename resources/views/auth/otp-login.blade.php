<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (!session('otp_login_mobile'))
        <!-- Password-based Login -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Student &amp; Aspirant Login</h2>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('Sign in with your registered mobile number and password.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div>
                <x-input-label for="mobile_number" :value="__('Mobile Number')" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 font-semibold text-sm">
                        +91
                    </div>
                    <x-text-input 
                        id="mobile_number" 
                        class="block w-full pl-12" 
                        type="tel" 
                        name="mobile_number" 
                        :value="old('mobile_number')" 
                        placeholder="9876543210" 
                        maxlength="10"
                        required 
                        autofocus 
                    />
                </div>
                <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input 
                    id="password" 
                    class="block mt-1 w-full" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password" 
                    placeholder="••••••••" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="mt-6 flex flex-col gap-3">
                <x-primary-button class="w-full justify-center py-2.5">
                    {{ __('Sign In') }}
                </x-primary-button>

                <div class="text-center text-xs text-gray-600 pt-2 border-t border-gray-100">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800 underline">
                        {{ __('Register here') }}
                    </a>
                </div>
            </div>
        </form>
    @else
        <!-- STEP 2: Enter OTP -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Verify One-Time Password</h2>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('Enter the 6-digit verification code sent to') }}
                <strong class="text-gray-900 font-mono">+91 {{ session('otp_login_mobile') }}</strong>.
            </p>
        </div>

        <form method="POST" action="{{ route('login.verify') }}">
            @csrf
            <input type="hidden" name="mobile_number" value="{{ session('otp_login_mobile') }}">
            
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="otp" :value="__('Enter OTP')" />
                    <a href="{{ route('login', ['reset' => 1]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">
                        {{ __('Change Mobile Number') }}
                    </a>
                </div>
                <x-text-input 
                    id="otp" 
                    class="block mt-1 w-full text-center tracking-widest text-lg font-bold" 
                    type="text" 
                    name="otp" 
                    placeholder="• • • • • •" 
                    maxlength="6" 
                    required 
                    autofocus 
                />
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col gap-3">
                <x-primary-button class="w-full justify-center py-2.5">
                    {{ __('Verify & Login') }}
                </x-primary-button>

                <div class="flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-100">
                    <span>{{ __("Didn't receive code?") }}</span>
                    <button type="submit" form="resend-otp-form" class="font-bold text-indigo-600 hover:text-indigo-800 underline">
                        {{ __('Resend OTP') }}
                    </button>
                </div>
            </div>
        </form>

        <form id="resend-otp-form" method="POST" action="{{ route('login.send-otp') }}" class="hidden">
            @csrf
            <input type="hidden" name="mobile_number" value="{{ session('otp_login_mobile') }}">
        </form>
    @endif
</x-guest-layout>
