@props([
    'title' => null,
    'description' => null,
    'ogImage' => null,
    'canonical' => null,
])

@php
    $siteName = \App\Models\Setting::get('site_name', 'RGPL Academy');
    $siteTagline = \App\Models\Setting::get('tagline', 'Your Exam Preparation Partner');
    $pageTitle = $title ? "{$title} | {$siteName}" : "{$siteName} - {$siteTagline}";
    $pageDescription = $description ?? "Prepare for MPSC, Maharashtra Police Bharti, and Commerce examinations with RGPL Academy. Access curated bilingual MCQs, timed mock exams, and in-depth explanations.";
    $canonicalUrl = $canonical ?? url()->current();
    $topAnnouncement = \App\Models\Announcement::published()->where('featured', true)->first() ?? \App\Models\Announcement::published()->first();
    $coursesNav = \App\Models\Course::active()->get();
    $contactPhone = \App\Models\Setting::get('contact_phone', '+91 98765 43210');
    $contactEmail = \App\Models\Setting::get('contact_email', 'support@rgplacademy.com');
    $contactAddress = \App\Models\Setting::get('contact_address', 'Pune, Maharashtra');
    $fbLink = \App\Models\Setting::get('social_facebook', 'https://facebook.com');
    $instaLink = \App\Models\Setting::get('social_instagram', 'https://instagram.com');
    $ytLink = \App\Models\Setting::get('social_youtube', 'https://youtube.com');
    $tgLink = \App\Models\Setting::get('social_telegram', 'https://t.me');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/parikshamitra-logo.svg') }}">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/parikshamitra-favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="font-sans text-slate-800 bg-slate-50 antialiased flex flex-col min-h-screen selection:bg-amber-500 selection:text-white">

    <!-- 1. TOP ANNOUNCEMENT BAR (Managed from Admin) -->
    @if($topAnnouncement)
        <div x-data="{ dismissed: false }" x-show="!dismissed" class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white text-xs sm:text-sm py-2 px-4 sticky top-0 z-50 border-b border-blue-800 shadow-sm transition-all duration-300">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-hidden">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-slate-950 uppercase tracking-wider shrink-0">
                        {{ $topAnnouncement->category ?? 'Notice' }}
                    </span>
                    <span class="font-medium truncate">
                        {{ $topAnnouncement->title }}
                    </span>
                    @if($topAnnouncement->short_description)
                        <span class="hidden md:inline text-blue-200 text-xs truncate">
                            — {{ $topAnnouncement->short_description }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @if($topAnnouncement->link)
                        <a href="{{ $topAnnouncement->link }}" class="inline-flex items-center gap-1 font-semibold text-amber-400 hover:text-amber-300 text-xs underline decoration-amber-400/50 hover:decoration-amber-300">
                            {{ $topAnnouncement->button_text ?? 'View Details' }} &rarr;
                        </a>
                    @endif
                    <button @click="dismissed = true" aria-label="Close Announcement" class="text-blue-300 hover:text-white p-0.5 rounded transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. STICKY MAIN NAVIGATION -->
    <header x-data="{ mobileMenuOpen: false }" class="sticky {{ $topAnnouncement ? 'top-[37px]' : 'top-0' }} z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="group focus:outline-none">
                        <x-parikshamitra-logo class="transition-transform group-hover:scale-[1.01]" />
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center space-x-1 xl:space-x-2 font-medium text-sm text-slate-700">
                    <a href="{{ url('/') }}" class="px-3 py-2 rounded-lg transition {{ request()->is('/') ? 'text-blue-700 font-bold bg-blue-50' : 'hover:text-blue-700 hover:bg-slate-100' }}">Home</a>
                    <a href="{{ url('/courses') }}" class="px-3 py-2 rounded-lg transition {{ request()->is('courses*') ? 'text-blue-700 font-bold bg-blue-50' : 'hover:text-blue-700 hover:bg-slate-100' }}">Courses</a>
                    <a href="{{ url('/practice') }}" class="px-3 py-2 rounded-lg transition {{ request()->is('practice*') ? 'text-blue-700 font-bold bg-blue-50' : 'hover:text-blue-700 hover:bg-slate-100' }}">Practice</a>
                    <a href="{{ url('/exams') }}" class="px-3 py-2 rounded-lg transition {{ request()->is('exams*') ? 'text-blue-700 font-bold bg-blue-50' : 'hover:text-blue-700 hover:bg-slate-100' }}">Exams</a>
                    <a href="{{ url('/announcements-and-news') }}" class="px-3 py-2 rounded-lg transition {{ request()->is('announcements*') || request()->is('news*') ? 'text-blue-700 font-bold bg-blue-50' : 'hover:text-blue-700 hover:bg-slate-100' }}">Announcements &amp; News</a>
                    <a href="{{ url('/pricing') }}" class="px-3 py-2 rounded-lg transition {{ request()->is('pricing') ? 'text-blue-700 font-bold bg-blue-50' : 'hover:text-blue-700 hover:bg-slate-100' }}">Pricing</a>
                    <a href="{{ url('/about-us') }}" class="px-3 py-2 rounded-lg transition {{ request()->is('about-us') ? 'text-blue-700 font-bold bg-blue-50' : 'hover:text-blue-700 hover:bg-slate-100' }}">About Us</a>
                    <a href="{{ url('/contact-us') }}" class="px-3 py-2 rounded-lg transition {{ request()->is('contact-us') ? 'text-blue-700 font-bold bg-blue-50' : 'hover:text-blue-700 hover:bg-slate-100' }}">Contact Us</a>
                </nav>

                <!-- Right Side CTA / Auth -->
                <div class="hidden lg:flex items-center space-x-3">
                    <!-- Search Icon Button -->
                    <a href="{{ url('/search') }}" class="p-2 text-slate-500 hover:text-blue-700 hover:bg-slate-100 rounded-lg transition" title="Search Courses & News">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white bg-blue-700 hover:bg-blue-800 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-slate-700 hover:text-blue-700 hover:bg-slate-100 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition">
                            Register
                        </a>
                    @endauth
                </div>

                <!-- Hamburger Button for Mobile -->
                <div class="flex items-center lg:hidden gap-2">
                    <a href="{{ url('/search') }}" class="p-2 text-slate-600 hover:text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle navigation menu" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer / Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 -translate-y-2" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 translate-y-0" 
             x-transition:leave-end="opacity-0 -translate-y-2" 
             class="lg:hidden bg-white border-b border-slate-200 shadow-xl px-4 pt-3 pb-6 space-y-1">
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('/') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">Home</a>
            <a href="{{ url('/courses') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('courses*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">Courses</a>
            <a href="{{ url('/practice') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('practice*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">Practice</a>
            <a href="{{ url('/exams') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('exams*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">Exams</a>
            <a href="{{ url('/announcements-and-news') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('announcements*') || request()->is('news*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">Announcements &amp; News</a>
            <a href="{{ url('/pricing') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('pricing') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">Pricing</a>
            <a href="{{ url('/about-us') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('about-us') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">About Us</a>
            <a href="{{ url('/contact-us') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('contact-us') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">Contact Us</a>
            <a href="{{ url('/faq') }}" class="block px-3 py-2 rounded-md font-medium text-base {{ request()->is('faq') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">FAQ</a>

            <div class="pt-4 border-t border-slate-200 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center px-4 py-2.5 rounded-lg text-white font-semibold bg-blue-700 hover:bg-blue-800">
                        Go to Student Dashboard
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="text-center px-4 py-2.5 rounded-lg text-slate-700 font-semibold border border-slate-300 hover:bg-slate-50">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="text-center px-4 py-2.5 rounded-lg text-white font-semibold bg-amber-600 hover:bg-amber-700">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN BODY CONTENT -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- 16. FOOTER -->
    <footer class="bg-slate-900 text-slate-300 border-t border-slate-800 pt-16 pb-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-8 pb-12 border-b border-slate-800">
                
                <!-- Col 1: Brand & Tagline -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-md">
                            PM
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white tracking-tight">Pariksha<span class="text-amber-500">Mitra</span></div>
                            <div class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Your Exam Preparation Partner</div>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
                        An enterprise-grade online MCQ practice and examination platform engineered for Maharashtra competitive exam aspirants and commerce students.
                    </p>
                    <div class="flex items-center space-x-3 pt-2">
                        <a href="{{ $fbLink }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-lg bg-slate-800 hover:bg-blue-600 hover:text-white text-slate-400 transition" aria-label="Facebook">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95C18.05 21.45 22 17.19 22 12z"/></svg>
                        </a>
                        <a href="{{ $instaLink }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-lg bg-slate-800 hover:bg-pink-600 hover:text-white text-slate-400 transition" aria-label="Instagram">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="{{ $ytLink }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-lg bg-slate-800 hover:bg-red-600 hover:text-white text-slate-400 transition" aria-label="YouTube">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a href="{{ $tgLink }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-lg bg-slate-800 hover:bg-sky-500 hover:text-white text-slate-400 transition" aria-label="Telegram">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Courses -->
                <div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Courses &amp; Fields</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ url('/courses/mpsc') }}" class="hover:text-amber-400 transition">MPSC State Services</a></li>
                        <li><a href="{{ url('/courses/police-bharti') }}" class="hover:text-amber-400 transition">Maharashtra Police Bharti</a></li>
                        <li><a href="{{ url('/courses/commerce') }}" class="hover:text-amber-400 transition">11th &amp; 12th Commerce</a></li>
                        <li><a href="{{ url('/courses/commerce') }}" class="hover:text-amber-400 transition">CA Foundation &amp; CS</a></li>
                        <li><a href="{{ url('/courses/commerce') }}" class="hover:text-amber-400 transition">ICWA / CMA Prep</a></li>
                        <li><a href="{{ url('/courses') }}" class="text-blue-400 hover:text-blue-300 font-medium inline-flex items-center gap-1">All Programs &rarr;</a></li>
                    </ul>
                </div>

                <!-- Col 3: Quick Links -->
                <div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Quick Links</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ url('/practice') }}" class="hover:text-amber-400 transition">Practice Mode</a></li>
                        <li><a href="{{ url('/exams') }}" class="hover:text-amber-400 transition">Mock Exams</a></li>
                        <li><a href="{{ url('/pricing') }}" class="hover:text-amber-400 transition">Subscription Plans</a></li>
                        <li><a href="{{ url('/announcements-and-news') }}" class="hover:text-amber-400 transition">Announcements &amp; News</a></li>
                        <li><a href="{{ url('/faq') }}" class="hover:text-amber-400 transition">FAQs &amp; Helpdesk</a></li>
                        <li><a href="{{ url('/about-us') }}" class="hover:text-amber-400 transition">About RGPL Academy</a></li>
                        <li><a href="{{ url('/contact-us') }}" class="hover:text-amber-400 transition">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Col 4: Legal & Support -->
                <div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Legal &amp; Support</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ url('/privacy-policy') }}" class="hover:text-amber-400 transition">Privacy Policy</a></li>
                        <li><a href="{{ url('/terms-and-conditions') }}" class="hover:text-amber-400 transition">Terms &amp; Conditions</a></li>
                        <li><a href="{{ url('/refund-policy') }}" class="hover:text-amber-400 transition">Refund &amp; Cancellation</a></li>
                    </ul>

                    <div class="mt-6 pt-4 border-t border-slate-800">
                        <div class="text-xs font-bold text-white uppercase tracking-wider mb-2">Helpdesk</div>
                        <div class="text-xs text-slate-400 space-y-1">
                            <p class="flex items-center gap-1.5"><span class="text-slate-500">Call:</span> <span class="text-slate-300">{{ $contactPhone }}</span></p>
                            <p class="flex items-center gap-1.5"><span class="text-slate-500">Mail:</span> <a href="mailto:{{ $contactEmail }}" class="text-amber-400 hover:underline">{{ $contactEmail }}</a></p>
                            <p class="text-slate-400 text-[11px] pt-1 leading-snug">{{ $contactAddress }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} RGPL Academy. All Rights Reserved.</p>
                <p class="flex items-center gap-4">
                    <span>Designed for Competitive Aspirants across Maharashtra</span>
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
