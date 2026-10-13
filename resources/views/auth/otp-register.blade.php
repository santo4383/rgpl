<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (!session('otp_register_data'))
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Student Registration</h2>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('Register for a new student account to access free mock tests.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="e.g. Ramesh Patil" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
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
                    />
                </div>
                <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="dob" :value="__('Date of Birth')" />
                <x-text-input id="dob" class="block mt-1 w-full" type="date" name="dob" :value="old('dob')" required />
                <x-input-error :messages="$errors->get('dob')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="address" :value="__('Address / City')" />
                <textarea id="address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" name="address" rows="2" placeholder="e.g. Pune, Maharashtra" required>{{ old('address') }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            @if(isset($courses) && count($courses) > 0)
                <div class="mt-4">
                    <x-input-label :value="__('Select Target Stream(s) to Prepare')" />
                    <p class="text-xs text-gray-500 mb-2">You can also change or add streams later from your dashboard.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1 max-h-40 overflow-y-auto p-2 border rounded-md bg-gray-50">
                        @foreach($courses as $course)
                            <label class="inline-flex items-center space-x-2 text-xs text-gray-700 bg-white p-2 rounded border border-gray-200 cursor-pointer hover:bg-indigo-50">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ is_array(old('course_ids')) && in_array($course->id, old('course_ids')) ? 'checked' : '' }}>
                                <span class="font-medium">{{ $course->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('course_ids')" class="mt-2" />
                </div>
            @endif

            <div class="flex items-center justify-between mt-6">
                <a class="underline text-xs text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered? Sign in') }}
                </a>

                <x-primary-button>
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    @else
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Verify Registration OTP</h2>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('We sent a 6-digit verification code to') }}
                <strong class="text-gray-900 font-mono">+91 {{ session('otp_register_data.mobile_number') }}</strong>.
            </p>
        </div>

        <form method="POST" action="{{ route('register.verify') }}">
            @csrf
            <input type="hidden" name="name" value="{{ session('otp_register_data.name') }}">
            <input type="hidden" name="mobile_number" value="{{ session('otp_register_data.mobile_number') }}">
            <input type="hidden" name="dob" value="{{ session('otp_register_data.dob') }}">
            <input type="hidden" name="address" value="{{ session('otp_register_data.address') }}">
            @if(!empty(session('otp_register_data.course_ids')))
                @foreach(session('otp_register_data.course_ids') as $cid)
                    <input type="hidden" name="course_ids[]" value="{{ $cid }}">
                @endforeach
            @endif
            
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="otp" :value="__('Enter OTP')" />
                    <a href="{{ route('register', ['reset' => 1]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">
                        {{ __('Edit Details') }}
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
                    {{ __('Verify & Register') }}
                </x-primary-button>

                <div class="flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-100">
                    <span>{{ __("Didn't receive code?") }}</span>
                    <button type="submit" form="resend-register-otp-form" class="font-bold text-indigo-600 hover:text-indigo-800 underline">
                        {{ __('Resend OTP') }}
                    </button>
                </div>
            </div>
        </form>

        <form id="resend-register-otp-form" method="POST" action="{{ route('register.send-otp') }}" class="hidden">
            @csrf
            <input type="hidden" name="name" value="{{ session('otp_register_data.name') }}">
            <input type="hidden" name="mobile_number" value="{{ session('otp_register_data.mobile_number') }}">
            <input type="hidden" name="dob" value="{{ session('otp_register_data.dob') }}">
            <input type="hidden" name="address" value="{{ session('otp_register_data.address') }}">
            @if(!empty(session('otp_register_data.course_ids')))
                @foreach(session('otp_register_data.course_ids') as $cid)
                    <input type="hidden" name="course_ids[]" value="{{ $cid }}">
                @endforeach
            @endif
        </form>
    @endif
</x-guest-layout>
