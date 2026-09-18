<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 - Page Expired | {{ config('app.name', 'MCQ Platform') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 bg-slate-50 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center sm:p-12">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-50 text-amber-600 rounded-full mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <div class="inline-block px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold tracking-wider uppercase rounded-full mb-3">
            Error 419
        </div>
        
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl mb-3">Session Expired</h1>
        <p class="text-gray-600 text-sm sm:text-base mb-8 leading-relaxed">
            Your security token or active session has timed out due to inactivity. Please refresh the page and continue your work.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.location.reload();" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Page
            </button>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">
                Return to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
