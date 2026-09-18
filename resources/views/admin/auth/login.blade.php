<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login - {{ config('app.name', 'RGPL Academy') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        <!-- Brand Logo with Inverted Styling for High Contrast on Dark Background -->
        <div class="flex flex-col items-center justify-center">
            <a href="{{ route('public.home') }}" class="transition transform hover:scale-105" title="RGPL Academy">
                <x-rgpl-logo :inverted="true" class="scale-110 justify-center" />
            </a>
            
            <!-- High-Contrast Administrative Badge with Explicit Styles -->
            <div class="mt-4 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide shadow-sm" style="background-color: #1e293b; color: #c7d2fe; border: 1px solid rgba(99, 102, 241, 0.45);">
                <svg class="w-3.5 h-3.5 shrink-0" style="color: #818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span style="color: #e0e7ff;">Authorized Administrative Access Only</span>
            </div>
        </div>

        <!-- Login Card -->
        <div class="mt-8 bg-white py-8 px-6 sm:px-10 shadow-2xl shadow-black/40 rounded-2xl border border-slate-200">
            <div class="mb-6">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Admin Login</h2>
                <p class="text-xs text-slate-500 mt-1">Sign in with your administrative credentials to manage RGPL Academy.</p>
            </div>

            <!-- Status Notification -->
            @if (session('status'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Dev / Local Simulated OTP Notice -->
            @if (session('admin_dev_otp'))
                <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border-2 border-amber-400 text-amber-950 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div>Simulated SMS Verification Code:</div>
                            <div class="font-mono text-base font-black tracking-widest text-amber-900 mt-0.5">{{ session('admin_dev_otp') }}</div>
                        </div>
                    </div>
                    <span class="text-[10px] uppercase font-bold text-amber-800 bg-amber-200 px-2.5 py-1 rounded-md">Local Test Mode</span>
                </div>
            @endif

            <!-- Global Error Notification -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if (session('admin_otp_mobile'))
                <!-- STEP 2: Verify OTP -->
                <div class="space-y-5">
                    <div class="p-3.5 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-indigo-900">OTP Sent to Mobile</div>
                            <div class="text-sm font-mono font-bold text-indigo-700">+91 {{ session('admin_otp_mobile') }}</div>
                        </div>
                        <a href="{{ route('admin.login', ['reset' => 1]) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                            Change Number
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.login.verify-otp') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="mobile_number" value="{{ session('admin_otp_mobile') }}">

                        <div>
                            <label for="otp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                6-Digit Verification Code
                            </label>
                            <input
                                id="otp"
                                type="text"
                                name="otp"
                                required
                                autofocus
                                maxlength="6"
                                placeholder="• • • • • •"
                                class="block w-full py-3 text-center text-xl tracking-widest font-black rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition placeholder:text-slate-300"
                            />
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="rounded border-slate-300 text-indigo-600 shadow-xs focus:ring-indigo-500 w-4 h-4"
                                />
                                <span class="text-xs text-slate-600 font-medium">Keep me signed in</span>
                            </label>

                            <button type="submit" form="admin-resend-otp-form" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                Resend OTP
                            </button>
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="w-full flex justify-center items-center gap-2 py-2.5 px-4 rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition active:scale-[0.99]"
                            >
                                <span>Verify & Sign In</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>

                        <div class="pt-2 text-center">
                            <a href="{{ route('admin.login', ['reset' => 1]) }}" class="text-xs font-medium text-slate-500 hover:text-slate-800 underline">
                                Cancel and sign in with Password instead
                            </a>
                        </div>
                    </form>

                    <form id="admin-resend-otp-form" method="POST" action="{{ route('admin.login.send-otp') }}" class="hidden">
                        @csrf
                        <input type="hidden" name="mobile_number" value="{{ session('admin_otp_mobile') }}">
                    </form>
                </div>
            @else
                @php
                    $initialTab = (old('mobile_number') || request('tab') === 'otp') ? 'otp' : 'password';
                @endphp

                <!-- STEP 1: Tabs for Password Login & Mobile OTP Login -->
                <div class="space-y-5">
                    <!-- Tab Switcher (Native JS + Accessible) -->
                    <div class="flex rounded-xl bg-slate-100 p-1 border border-slate-200">
                        <button
                            type="button"
                            id="tab-btn-password"
                            onclick="switchAdminTab('password')"
                            class="flex-1 py-2 text-xs rounded-lg transition text-center flex items-center justify-center gap-1.5 {{ $initialTab === 'password' ? 'font-bold bg-white text-slate-900 shadow-sm' : 'font-medium text-slate-500 hover:text-slate-900' }}"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <span>Password Login</span>
                        </button>
                        <button
                            type="button"
                            id="tab-btn-otp"
                            onclick="switchAdminTab('otp')"
                            class="flex-1 py-2 text-xs rounded-lg transition text-center flex items-center justify-center gap-1.5 {{ $initialTab === 'otp' ? 'font-bold bg-white text-slate-900 shadow-sm' : 'font-medium text-slate-500 hover:text-slate-900' }}"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>Mobile OTP Login</span>
                        </button>
                    </div>

                    <!-- Password Login Form (Explicit inline style avoids race conditions) -->
                    <div id="admin-password-section" style="display: {{ $initialTab === 'password' ? 'block' : 'none' }};">
                        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
                            @csrf

                            <!-- Email or Mobile Number -->
                            <div>
                                <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Email or Mobile Number
                                </label>
                                <div class="relative rounded-xl shadow-xs">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input
                                        id="login"
                                        type="text"
                                        name="login"
                                        value="{{ old('login', 'admin@example.com') }}"
                                        required
                                        autofocus
                                        placeholder="admin@example.com or 1234567890"
                                        class="block w-full pl-10 pr-3.5 py-2.5 text-sm rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition placeholder:text-slate-400"
                                    />
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Password
                                </label>
                                <div class="relative rounded-xl shadow-xs">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="••••••••"
                                        class="block w-full pl-10 pr-3.5 py-2.5 text-sm rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition placeholder:text-slate-400"
                                    />
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="flex items-center justify-between pt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        class="rounded border-slate-300 text-indigo-600 shadow-xs focus:ring-indigo-500 w-4 h-4"
                                    />
                                    <span class="text-xs text-slate-600 font-medium">Keep me signed in</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button
                                    type="submit"
                                    class="w-full flex justify-center items-center gap-2 py-2.5 px-4 rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition active:scale-[0.99]"
                                >
                                    <span>Sign In to Admin Panel</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Mobile OTP Form (Explicit inline style avoids race conditions) -->
                    <div id="admin-otp-section" style="display: {{ $initialTab === 'otp' ? 'block' : 'none' }};">
                        <form method="POST" action="{{ route('admin.login.send-otp') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label for="mobile_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Admin Mobile Number
                                </label>
                                <div class="relative rounded-xl shadow-xs">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 font-bold text-sm">
                                        +91
                                    </div>
                                    <input
                                        id="mobile_number"
                                        type="tel"
                                        name="mobile_number"
                                        value="{{ old('mobile_number', '1234567890') }}"
                                        required
                                        maxlength="10"
                                        placeholder="1234567890"
                                        class="block w-full pl-12 pr-3.5 py-2.5 text-sm rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition placeholder:text-slate-400 font-medium"
                                    />
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1.5">
                                    A 6-digit verification code will be sent to your registered admin mobile number.
                                </p>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    class="w-full flex justify-center items-center gap-2 py-2.5 px-4 rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition active:scale-[0.99]"
                                >
                                    <span>Send Verification OTP</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-center">
                <a href="{{ route('public.home') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Return to Public Website</span>
                </a>
            </div>
        </div>

        <!-- Security Disclaimer -->
        <p class="mt-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} RGPL Academy. All rights reserved. Secure Administrative System.
        </p>
    </div>

    @livewireScripts

    <script>
        function switchAdminTab(mode) {
            var pwdSection = document.getElementById('admin-password-section');
            var otpSection = document.getElementById('admin-otp-section');
            var btnPwd = document.getElementById('tab-btn-password');
            var btnOtp = document.getElementById('tab-btn-otp');

            if (!pwdSection || !otpSection || !btnPwd || !btnOtp) return;

            if (mode === 'otp') {
                pwdSection.style.display = 'none';
                otpSection.style.display = 'block';

                btnPwd.className = 'flex-1 py-2 text-xs rounded-lg transition text-center flex items-center justify-center gap-1.5 font-medium text-slate-500 hover:text-slate-900';
                btnOtp.className = 'flex-1 py-2 text-xs rounded-lg transition text-center flex items-center justify-center gap-1.5 font-bold bg-white text-slate-900 shadow-sm';
            } else {
                pwdSection.style.display = 'block';
                otpSection.style.display = 'none';

                btnPwd.className = 'flex-1 py-2 text-xs rounded-lg transition text-center flex items-center justify-center gap-1.5 font-bold bg-white text-slate-900 shadow-sm';
                btnOtp.className = 'flex-1 py-2 text-xs rounded-lg transition text-center flex items-center justify-center gap-1.5 font-medium text-slate-500 hover:text-slate-900';
            }
        }
    </script>
</body>
</html>
