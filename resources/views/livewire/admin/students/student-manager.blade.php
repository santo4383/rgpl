<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Student Accounts & Activity Management') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('Monitor registered students, manage activation status, track subscription plans, question usage quotas, and live activities.') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-xs rounded-xl shadow-xs transition">
                    &larr; {{ __('Admin Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @if (session()->has('message'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-xs text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- KPI METRIC CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-5">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Total Registered') }}</span>
                    <div class="mt-2 text-2xl font-black text-gray-900">{{ $totalCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ __('Registered student accounts') }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-5">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Active Accounts') }}</span>
                    <div class="mt-2 text-2xl font-black text-emerald-600">{{ $activeCount }}</div>
                    <div class="text-xs text-emerald-700 mt-1 font-medium">{{ __('Permitted to login & practice') }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-5">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Deactivated Accounts') }}</span>
                    <div class="mt-2 text-2xl font-black text-rose-600">{{ $inactiveCount }}</div>
                    <div class="text-xs text-rose-700 mt-1 font-medium">{{ __('Login strictly blocked') }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-5">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Active Subscribers') }}</span>
                    <div class="mt-2 text-2xl font-black text-indigo-600">{{ $subscribedCount }}</div>
                    <div class="text-xs text-indigo-700 mt-1 font-medium">{{ __('Unlimited practice access') }}</div>
                </div>
            </div>

            <!-- SEARCH & FILTERS BAR -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-4 sm:p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Search') }}</label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, mobile, email..." class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Account Status') }}</label>
                        <select wire:model.live="statusFilter" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active Only') }}</option>
                            <option value="inactive">{{ __('Deactivated Only') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Subscription') }}</label>
                        <select wire:model.live="subscriptionFilter" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">{{ __('All Students') }}</option>
                            <option value="subscribed">{{ __('Active Subscribed') }}</option>
                            <option value="expired">{{ __('Expired Subscription') }}</option>
                            <option value="free">{{ __('Free Tier / None') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Course Stream') }}</label>
                        <select wire:model.live="courseFilter" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('All Courses') }}</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- STUDENTS TABLE -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-500 uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3.5">{{ __('Student') }}</th>
                                <th class="px-6 py-3.5">{{ __('Target Stream(s)') }}</th>
                                <th class="px-6 py-3.5">{{ __('Subscription') }}</th>
                                <th class="px-6 py-3.5">{{ __('Free Quota / Used') }}</th>
                                <th class="px-6 py-3.5">{{ __('Performance') }}</th>
                                <th class="px-6 py-3.5 text-center">{{ __('Account Status') }}</th>
                                <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($students as $student)
                                @php
                                    $activeSub = $student->activeSubscription();
                                    $freeQuota = $student->totalFreeQuota();
                                    $usedQuestions = $student->totalQuestionsUsed();
                                    $remainingQuota = $student->totalQuotaRemaining();
                                    $accuracy = $student->accuracyPercentage();
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition">
                                    <!-- Student Profile -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full {{ $student->is_active ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-500' }} font-black text-xs flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($student->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 text-sm">{{ $student->name }}</div>
                                                <div class="text-[11px] text-gray-500 font-mono">+91 {{ $student->mobile_number }}</div>
                                                @if($student->email)
                                                    <div class="text-[11px] text-gray-400">{{ $student->email }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Courses -->
                                    <td class="px-6 py-4">
                                        @if($student->courses->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                @foreach($student->courses as $c)
                                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded text-[11px] font-medium">
                                                        {{ $c->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">{{ __('None selected') }}</span>
                                        @endif
                                    </td>

                                    <!-- Subscription -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($activeSub)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg font-bold text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ $activeSub->plan ? $activeSub->plan->name : 'Active Plan' }}
                                            </span>
                                            <div class="text-[10px] text-gray-400 mt-1">
                                                {{ __('Expires:') }} {{ $activeSub->expires_at->format('d M Y') }}
                                            </div>
                                        @elseif($student->subscriptions->where('expires_at', '<=', now())->isNotEmpty())
                                            <span class="inline-flex items-center px-2 py-0.5 bg-rose-50 border border-rose-200 text-rose-700 rounded text-[11px] font-semibold">
                                                {{ __('Expired') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[11px] font-medium">
                                                {{ __('Free Tier') }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Quota / Usage -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($activeSub)
                                            <div class="font-bold text-emerald-700">{{ __('Unlimited Access') }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $usedQuestions }} {{ __('MCQs practiced') }}</div>
                                        @else
                                            <div class="font-bold text-gray-800">
                                                {{ $usedQuestions }} / {{ $freeQuota }} <span class="font-normal text-gray-500">{{ __('used') }}</span>
                                            </div>
                                            <div class="text-[11px] {{ $remainingQuota === 0 ? 'text-rose-600 font-bold' : 'text-gray-500' }}">
                                                {{ $remainingQuota }} {{ __('remaining') }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Performance -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $student->practice_attempts_count }} {{ __('MCQs') }}</div>
                                        <div class="text-[11px] text-gray-500">
                                            <span class="font-semibold text-indigo-700">{{ $accuracy }}%</span> {{ __('accuracy') }} &bull; {{ $student->exam_attempts_count }} {{ __('exams') }}
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($student->is_active)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                                {{ __('Deactivated') }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- View Details / Inspector -->
                                            <button 
                                                wire:click="viewStudent({{ $student->id }})" 
                                                class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition"
                                                title="{{ __('View Activities & Full Details') }}"
                                            >
                                                {{ __('Details') }}
                                            </button>

                                            <!-- Toggle Active / Deactivate -->
                                            <button 
                                                wire:click="toggleStatus({{ $student->id }})" 
                                                wire:confirm="{{ $student->is_active ? 'Are you sure you want to deactivate this student? They will be immediately blocked from logging in.' : 'Are you sure you want to activate this student account?' }}" 
                                                class="px-2.5 py-1 {{ $student->is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }} font-bold rounded-lg transition"
                                            >
                                                {{ $student->is_active ? __('Deactivate') : __('Activate') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="text-sm font-bold text-gray-700">{{ __('No student accounts found') }}</div>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('Try adjusting your search criteria or filters.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($students->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- STUDENT ACTIVITY & DETAILS INSPECTOR MODAL -->
    @if($selectedStudent)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $selectedStudent->is_active ? 'bg-indigo-600 text-white' : 'bg-rose-600 text-white' }} font-black text-sm flex items-center justify-center">
                            {{ strtoupper(substr($selectedStudent->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-gray-900 text-lg leading-snug">{{ $selectedStudent->name }}</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $selectedStudent->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $selectedStudent->is_active ? 'Active' : 'Deactivated' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 font-mono">+91 {{ $selectedStudent->mobile_number }} &bull; Registered {{ $selectedStudent->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            wire:click="toggleStatus({{ $selectedStudent->id }})" 
                            class="px-3 py-1.5 text-xs font-bold rounded-lg border transition {{ $selectedStudent->is_active ? 'border-rose-300 text-rose-700 bg-rose-50 hover:bg-rose-100' : 'border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}"
                        >
                            {{ $selectedStudent->is_active ? __('Deactivate Account') : __('Activate Account') }}
                        </button>
                        <button wire:click="closeStudentModal" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="px-6 bg-white border-b border-gray-200 flex space-x-6 text-xs font-bold overflow-x-auto">
                    <button wire:click="setTab('overview')" class="py-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'overview' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('👤 Overview & Quotas') }}
                    </button>
                    <button wire:click="setTab('subscriptions')" class="py-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'subscriptions' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('💳 Subscriptions & Payments') }} ({{ $studentSubscriptions->count() }})
                    </button>
                    <button wire:click="setTab('practice')" class="py-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'practice' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('📝 Practice Usage') }} ({{ $selectedStudent->practiceAttempts()->count() }})
                    </button>
                    <button wire:click="setTab('exams')" class="py-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'exams' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('🎯 Exam Attempts') }} ({{ $studentExamAttempts->count() }})
                    </button>
                    <button wire:click="setTab('logins')" class="py-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'logins' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('🔐 Login History') }} ({{ $studentLogins->count() }})
                    </button>
                </div>

                <!-- Modal Body Content -->
                <div class="p-6 overflow-y-auto flex-grow text-xs space-y-5">
                    
                    <!-- TAB 1: OVERVIEW & QUOTAS -->
                    @if($activeTab === 'overview')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Student Profile Information -->
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-3">{{ __('Personal Profile') }}</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                        <span class="text-gray-500">{{ __('Full Name') }}</span>
                                        <span class="font-bold text-gray-900">{{ $selectedStudent->name }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                        <span class="text-gray-500">{{ __('Mobile Number') }}</span>
                                        <span class="font-bold font-mono text-gray-900">+91 {{ $selectedStudent->mobile_number }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                        <span class="text-gray-500">{{ __('Email Address') }}</span>
                                        <span class="font-medium text-gray-900">{{ $selectedStudent->email ?? 'Not provided' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                        <span class="text-gray-500">{{ __('Date of Birth') }}</span>
                                        <span class="font-medium text-gray-900">{{ $selectedStudent->dob ? \Carbon\Carbon::parse($selectedStudent->dob)->format('d M Y') : 'Not provided' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-500">{{ __('Address / City') }}</span>
                                        <span class="font-medium text-gray-900 text-right">{{ $selectedStudent->address ?? 'Not provided' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Free Quota & Question Limit Breakdown -->
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-3">{{ __('Question Usage & Quota') }}</h4>
                                @php
                                    $activeSub = $selectedStudent->activeSubscription();
                                    $totalFree = $selectedStudent->totalFreeQuota();
                                    $used = $selectedStudent->totalQuestionsUsed();
                                    $remaining = $selectedStudent->totalQuotaRemaining();
                                    $pct = $totalFree > 0 ? min(100, round(($used / $totalFree) * 100)) : 0;
                                @endphp
                                <div class="space-y-3">
                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                        <span class="text-gray-500">{{ __('Active Subscription') }}</span>
                                        <span class="font-bold {{ $activeSub ? 'text-emerald-700' : 'text-gray-600' }}">
                                            {{ $activeSub ? $activeSub->plan->name : 'None (Free Tier)' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                        <span class="text-gray-500">{{ __('Free Quota Limit') }}</span>
                                        <span class="font-bold text-gray-900">{{ $totalFree }} questions</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                        <span class="text-gray-500">{{ __('Questions Practiced / Used') }}</span>
                                        <span class="font-bold text-indigo-700">{{ $used }} questions</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-500">{{ __('Remaining Free Quota') }}</span>
                                        <span class="font-bold {{ $remaining === 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                            {{ $activeSub ? 'Unlimited (Subscribed)' : ($remaining.' remaining') }}
                                        </span>
                                    </div>
                                    
                                    @if(!$activeSub)
                                        <div class="pt-2">
                                            <div class="flex justify-between text-[11px] text-gray-500 mb-1">
                                                <span>{{ __('Quota Consumption') }}</span>
                                                <span class="font-bold">{{ $pct }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="h-2 rounded-full {{ $pct >= 100 ? 'bg-rose-500' : ($pct >= 75 ? 'bg-amber-500' : 'bg-indigo-600') }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Enrolled Courses -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-2">{{ __('Enrolled Target Courses') }}</h4>
                            @if($selectedStudent->courses->isNotEmpty())
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($selectedStudent->courses as $c)
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200 flex justify-between items-center">
                                            <span class="font-bold text-gray-800">{{ $c->name }}</span>
                                            <span class="text-[10px] text-gray-500">{{ $c->free_question_limit }} free limit</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-400 italic">{{ __('No courses explicitly assigned.') }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- TAB 2: SUBSCRIPTIONS & PURCHASES -->
                    @if($activeTab === 'subscriptions')
                        <div>
                            <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-3">{{ __('Subscription History') }}</h4>
                            @if($studentSubscriptions->isNotEmpty())
                                <div class="border rounded-xl overflow-hidden divide-y divide-gray-100 bg-white">
                                    @foreach($studentSubscriptions as $sub)
                                        <div class="p-3.5 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-gray-900 text-sm">{{ $sub->plan ? $sub->plan->name : 'Custom Plan' }}</div>
                                                <div class="text-[11px] text-gray-500">
                                                    {{ __('Valid:') }} {{ $sub->starts_at ? $sub->starts_at->format('d M Y') : 'N/A' }} &rarr; {{ $sub->expires_at->format('d M Y') }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2.5 py-1 rounded text-[10px] font-bold {{ $sub->status === 'ACTIVE' && $sub->expires_at->isFuture() ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                                    {{ $sub->status }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-6 text-center text-gray-400 bg-gray-50 rounded-xl border border-gray-200">
                                    {{ __('No subscriptions recorded for this student.') }}
                                </div>
                            @endif

                            <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mt-6 mb-3">{{ __('Payment Records') }}</h4>
                            @if($studentPayments->isNotEmpty())
                                <div class="border rounded-xl overflow-hidden divide-y divide-gray-100 bg-white">
                                    @foreach($studentPayments as $pay)
                                        <div class="p-3.5 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-gray-900">₹{{ number_format($pay->amount, 2) }} &bull; {{ $pay->plan ? $pay->plan->name : 'N/A' }}</div>
                                                <div class="text-[11px] text-gray-500 font-mono">Ref: {{ $pay->transaction_reference }} &bull; {{ $pay->payment_method }}</div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $pay->status === 'APPROVED' ? 'bg-emerald-100 text-emerald-800' : ($pay->status === 'PENDING' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                                    {{ $pay->status }}
                                                </span>
                                                <div class="text-[10px] text-gray-400 mt-1">{{ $pay->created_at->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-6 text-center text-gray-400 bg-gray-50 rounded-xl border border-gray-200">
                                    {{ __('No payment records found.') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- TAB 3: PRACTICE USAGE HISTORY -->
                    @if($activeTab === 'practice')
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider">{{ __('Recent Practice Questions') }}</h4>
                                <span class="text-gray-500 text-[11px]">{{ __('Showing latest 25 attempts') }}</span>
                            </div>
                            @if($studentPracticeAttempts->isNotEmpty())
                                <div class="border rounded-xl overflow-hidden divide-y divide-gray-100 bg-white">
                                    @foreach($studentPracticeAttempts as $attempt)
                                        <div class="p-3.5 flex items-start justify-between gap-4">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $attempt->is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                        {{ $attempt->is_correct ? '✓ Correct' : '✕ Incorrect' }}
                                                    </span>
                                                    <span class="font-bold text-gray-700">{{ $attempt->course ? $attempt->course->name : 'Practice' }}</span>
                                                    @if($attempt->question && $attempt->question->question_code)
                                                        <span class="font-mono text-[10px] text-gray-400">({{ $attempt->question->question_code }})</span>
                                                    @endif
                                                </div>
                                                <p class="text-gray-800 line-clamp-2">
                                                    {{ $attempt->question ? $attempt->question->question_text : 'Question #'.$attempt->question_id }}
                                                </p>
                                            </div>
                                            <span class="text-[10px] text-gray-400 shrink-0 whitespace-nowrap">
                                                {{ $attempt->last_attempted_at ? $attempt->last_attempted_at->diffForHumans() : 'N/A' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-gray-200">
                                    {{ __('No practice question attempts recorded yet.') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- TAB 4: EXAM ATTEMPTS -->
                    @if($activeTab === 'exams')
                        <div>
                            <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-3">{{ __('Mock Examination History') }}</h4>
                            @if($studentExamAttempts->isNotEmpty())
                                <div class="border rounded-xl overflow-hidden divide-y divide-gray-100 bg-white">
                                    @foreach($studentExamAttempts as $examAttempt)
                                        <div class="p-3.5 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-gray-900 text-sm">{{ $examAttempt->exam ? $examAttempt->exam->title : 'Mock Exam' }}</div>
                                                <div class="text-[11px] text-gray-500">
                                                    {{ __('Score:') }} <strong>{{ $examAttempt->score }}</strong> / {{ $examAttempt->total_marks }} &bull; {{ $examAttempt->percentage }}%
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2.5 py-1 rounded text-[10px] font-bold {{ $examAttempt->is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                    {{ $examAttempt->is_passed ? 'Passed' : 'Failed' }}
                                                </span>
                                                <div class="text-[10px] text-gray-400 mt-1">{{ $examAttempt->created_at->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-gray-200">
                                    {{ __('No mock exam attempts recorded yet.') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- TAB 5: LOGIN & SECURITY HISTORY -->
                    @if($activeTab === 'logins')
                        <div>
                            <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-3">{{ __('Authentication & Login Activity') }}</h4>
                            @if($studentLogins->isNotEmpty())
                                <div class="border rounded-xl overflow-hidden divide-y divide-gray-100 bg-white">
                                    @foreach($studentLogins as $login)
                                        <div class="p-3.5 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-gray-900 flex items-center gap-2">
                                                    <span>{{ __('Logged in via') }} {{ $login->new_values['method'] ?? 'Password' }}</span>
                                                    <span class="font-mono text-gray-500 text-[11px]">IP: {{ $login->ip_address ?? 'Unknown' }}</span>
                                                </div>
                                                <div class="text-[10px] text-gray-400 truncate max-w-md">
                                                    {{ $login->user_agent }}
                                                </div>
                                            </div>
                                            <span class="text-[11px] text-gray-400 whitespace-nowrap">
                                                {{ $login->created_at->format('d M Y, h:i A') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-gray-200">
                                    {{ __('No login events logged yet for this account.') }}
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button wire:click="closeStudentModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-xl text-xs transition">
                        {{ __('Close Inspector') }}
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
