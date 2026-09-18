<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Executive Administration & Analytics') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('Real-time platform overview, student activities, financial collections, and content statistics.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.payments.index') }}" wire:navigate class="px-3.5 py-2 bg-amber-50 border border-amber-300 hover:bg-amber-100 text-amber-900 font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ __('Pending Payments') }} ({{ $pendingPaymentsCount }})
                </a>
                <a href="{{ route('admin.questions.create') }}" wire:navigate class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    + {{ __('New Question') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPI METRIC CARDS (4 COLUMNS) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Revenue -->
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Approved Revenue') }}</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                            ₹
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl sm:text-3xl font-black text-gray-900">
                            &#8377;{{ number_format($approvedRevenue, 2) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                            <span class="text-emerald-700 font-semibold">{{ $approvedPaymentsCount }} {{ __('approved') }}</span>
                            <span>&bull;</span>
                            <span class="text-amber-700 font-semibold">&#8377;{{ number_format($pendingPaymentsAmount, 0) }} {{ __('pending') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Students -->
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Total Students') }}</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl sm:text-3xl font-black text-gray-900">
                            {{ $totalStudents }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                            <span class="text-emerald-700 font-semibold">+{{ $newStudentsThisWeek }} {{ __('this week') }}</span>
                            <span>&bull;</span>
                            <span class="text-indigo-700 font-semibold">{{ $activeSubscriptions }} {{ __('subscribed') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Question Bank -->
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Question Bank') }}</span>
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl sm:text-3xl font-black text-gray-900">
                            {{ $totalQuestions }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                            <span class="text-emerald-700 font-semibold">{{ $publishedQuestions }} {{ __('published') }}</span>
                            <span>&bull;</span>
                            <span class="text-gray-500">{{ $draftQuestions }} {{ __('drafts') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Exam & Practice Activity -->
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Exam & Practice') }}</span>
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl sm:text-3xl font-black text-gray-900">
                            {{ $totalExamAttempts }} <span class="text-sm font-medium text-gray-400">{{ __('attempts') }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                            <span class="text-purple-700 font-bold">{{ $examPassRate }}% {{ __('pass rate') }}</span>
                            <span>&bull;</span>
                            <span>{{ $totalPracticeAttempts }} {{ __('practice MCQs') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECOND ROW: PENDING PAYMENTS & AUDIT STREAM -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left: Pending Payments Table (7 cols) -->
                <div class="lg:col-span-7 bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">{{ __('Pending Payment Verifications') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Latest student payment receipts awaiting administrative review.') }}</p>
                        </div>
                        <a href="{{ route('admin.payments.index') }}" wire:navigate class="text-xs font-bold text-indigo-600 hover:underline">
                            {{ __('View All') }} &rarr;
                        </a>
                    </div>

                    @if($pendingPayments->count() > 0)
                        <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
                            <thead class="bg-gray-50/60 text-gray-500 uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3">{{ __('Student') }}</th>
                                    <th class="px-6 py-3">{{ __('Plan') }}</th>
                                    <th class="px-6 py-3">{{ __('Amount') }}</th>
                                    <th class="px-6 py-3">{{ __('UTR / Ref') }}</th>
                                    <th class="px-6 py-3 text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pendingPayments as $p)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-6 py-3.5">
                                            <div class="font-bold text-gray-900">{{ $p->user->name }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $p->user->mobile_number }}</div>
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap">
                                            <span class="font-semibold text-gray-800">{{ $p->plan ? $p->plan->name : 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap font-bold text-gray-900">
                                            &#8377;{{ number_format($p->amount, 0) }}
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap font-mono text-gray-600">
                                            {{ $p->transaction_reference }}
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                            <a href="{{ route('admin.payments.index') }}" wire:navigate class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-lg transition">
                                                {{ __('Review') }} &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center text-gray-500 text-xs">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2 font-bold">✓</div>
                            <p class="font-bold text-gray-700">{{ __('All caught up!') }}</p>
                            <p class="text-gray-400 mt-0.5">{{ __('There are no pending payments awaiting verification.') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Right: Recent Activity / Audit Stream (5 cols) -->
                <div class="lg:col-span-5 bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">{{ __('Audit & Security Feed') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Live stream of latest platform events.') }}</p>
                        </div>
                        <a href="{{ route('admin.audit-logs.index') }}" wire:navigate class="text-xs font-bold text-indigo-600 hover:underline">
                            {{ __('All Logs') }} &rarr;
                        </a>
                    </div>

                    @if($recentAuditLogs->count() > 0)
                        <div class="divide-y divide-gray-100 text-xs">
                            @foreach($recentAuditLogs as $log)
                                @php
                                    $color = match($log->module) {
                                        'AUTH' => 'text-blue-700 bg-blue-50 border-blue-200',
                                        'PAYMENTS' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                                        'CONTENT' => 'text-indigo-700 bg-indigo-50 border-indigo-200',
                                        'EXAMS' => 'text-purple-700 bg-purple-50 border-purple-200',
                                        'SETTINGS' => 'text-amber-700 bg-amber-50 border-amber-200',
                                        default => 'text-gray-700 bg-gray-50 border-gray-200',
                                    };
                                @endphp
                                <div class="p-4 flex items-start justify-between gap-3 hover:bg-gray-50/70 transition">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $color }}">
                                                {{ $log->module }}
                                            </span>
                                            <span class="font-bold text-gray-900">{{ $log->action }}</span>
                                        </div>
                                        <div class="text-gray-500 text-[11px]">
                                            {{ __('by') }} <strong>{{ $log->user ? $log->user->name : 'System' }}</strong>
                                            @if($log->record_id)
                                                &bull; ID: <span class="font-mono">{{ $log->record_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-gray-400 shrink-0">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center text-gray-500 text-xs">
                            <p class="font-bold text-gray-700">{{ __('No audit events recorded yet.') }}</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- QUICK NAVIGATION SHORTCUTS -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider mb-4">{{ __('Quick Administrative Access') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-center text-xs">
                    <a href="{{ route('admin.courses.index') }}" wire:navigate class="p-4 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                        <div class="font-bold text-gray-900">{{ __('Courses') }}</div>
                        <span class="text-[11px] text-gray-500">{{ $totalCourses }} {{ __('total') }}</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" wire:navigate class="p-4 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                        <div class="font-bold text-gray-900">{{ __('Categories') }}</div>
                        <span class="text-[11px] text-gray-500">{{ __('Manage subjects') }}</span>
                    </a>
                    <a href="{{ route('admin.questions.index') }}" wire:navigate class="p-4 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                        <div class="font-bold text-gray-900">{{ __('Questions') }}</div>
                        <span class="text-[11px] text-gray-500">{{ $publishedQuestions }} {{ __('published') }}</span>
                    </a>
                    <a href="{{ route('admin.exams.index') }}" wire:navigate class="p-4 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                        <div class="font-bold text-gray-900">{{ __('Mock Exams') }}</div>
                        <span class="text-[11px] text-gray-500">{{ $totalExams }} {{ __('exams') }}</span>
                    </a>
                    <a href="{{ route('admin.plans.index') }}" wire:navigate class="p-4 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                        <div class="font-bold text-gray-900">{{ __('Plans & Pricing') }}</div>
                        <span class="text-[11px] text-gray-500">{{ __('Subscription tiers') }}</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" wire:navigate class="p-4 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/40 transition">
                        <div class="font-bold text-gray-900">{{ __('Settings') }}</div>
                        <span class="text-[11px] text-gray-500">{{ __('Site & quotas') }}</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
