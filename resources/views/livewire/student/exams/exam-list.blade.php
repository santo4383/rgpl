<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Examination Center') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Simulate real competitive exams with timed conditions, full question palettes, and in-depth performance analysis.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('student.practice') }}" wire:navigate class="px-3.5 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-lg shadow-xs transition">
                    &larr; {{ __('Practice Mode') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash alerts -->
            @if (session()->has('message'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Navigation Tabs & Course Filter -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Tabs -->
                <div class="flex items-center space-x-2">
                    <button 
                        wire:click="$set('activeTab', 'exams')" 
                        class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $activeTab === 'exams' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        {{ __('Available Mock Tests') }}
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'history')" 
                        class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $activeTab === 'history' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        {{ __('My Test History') }} ({{ count($attempts) }})
                    </button>
                </div>

                <!-- Course Selector -->
                @if($activeTab === 'exams' && $enrolledCourses->count() > 0)
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Course:') }}</label>
                        <select wire:model.live="course_id" class="text-xs border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('All My Courses') }}</option>
                            @foreach($enrolledCourses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            @if($activeTab === 'exams')
                <!-- Exam Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($exams as $item)
                        @php
                            $exam = $item['model'];
                            $isCompleted = $item['is_completed'];
                            $isInProgress = $item['is_in_progress'];
                            $canAttempt = $item['can_attempt'];
                        @endphp
                        <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition">
                            <div>
                                <!-- Badges -->
                                <div class="flex justify-between items-start mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $exam->course->name }}
                                    </span>
                                    @if($exam->is_premium)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                            ★ PREMIUM
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-600">
                                            FREE MOCK
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-bold text-gray-900 leading-snug mb-1">
                                    {{ $exam->title }}
                                </h3>

                                @if($exam->description)
                                    <p class="text-xs text-gray-500 line-clamp-2 mb-4">{{ $exam->description }}</p>
                                @else
                                    <div class="h-4 mb-2"></div>
                                @endif

                                <!-- Exam Spec Grid -->
                                <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded-lg border border-gray-100 mb-4 text-xs">
                                    <div>
                                        <span class="text-gray-400 block text-[10px] uppercase font-bold">{{ __('Duration') }}</span>
                                        <span class="font-bold text-gray-800">{{ $exam->duration_minutes }} {{ __('minutes') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block text-[10px] uppercase font-bold">{{ __('Total Marks') }}</span>
                                        <span class="font-bold text-gray-800">{{ $exam->total_marks }} {{ __('Marks') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block text-[10px] uppercase font-bold">{{ __('Questions') }}</span>
                                        <span class="font-bold text-gray-800">{{ $exam->questions_count }} {{ __('MCQs') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block text-[10px] uppercase font-bold">{{ __('Negative Marking') }}</span>
                                        <span class="font-bold {{ $exam->has_negative_marking ? 'text-rose-600' : 'text-gray-600' }}">
                                            {{ $exam->has_negative_marking ? '-' . $exam->default_negative_marks : __('None') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Single Attempt Call to Action -->
                            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                                @if($isCompleted)
                                    <span class="text-[11px] font-semibold text-emerald-700 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        {{ __('Attempt Completed') }}
                                    </span>
                                    <a href="{{ route('student.exams.result', $item['attempt']->id) }}" wire:navigate class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition">
                                        {{ __('View Scorecard') }} &rarr;
                                    </a>
                                @elseif($isInProgress)
                                    <span class="text-[11px] font-semibold text-amber-700 flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                        {{ __('In Progress') }}
                                    </span>
                                    <a href="{{ route('student.exams.take', $item['attempt']->id) }}" wire:navigate class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow-xs transition">
                                        {{ __('Resume Exam') }} &rarr;
                                    </a>
                                @elseif(!$canAttempt)
                                    <span class="text-[11px] font-semibold text-amber-800">
                                        {{ __('Premium Access Required') }}
                                    </span>
                                    <a href="{{ route('student.plans') }}" wire:navigate class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-lg shadow-xs transition">
                                        {{ __('Upgrade Plan') }} &rarr;
                                    </a>
                                @else
                                    <span class="text-[11px] text-gray-500 font-medium">
                                        {{ __('1 Attempt Allowed') }}
                                    </span>
                                    <button wire:click="openInstructions({{ $exam->id }})" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-xs transition">
                                        {{ __('Start Exam') }} &rarr;
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 bg-white rounded-xl border border-gray-200 p-12 text-center shadow-xs">
                            <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">{{ __('No Mock Exams Found') }}</h3>
                            <p class="text-sm text-gray-500 max-w-md mx-auto mt-1">
                                {{ __('There are currently no published exams for the selected course filter. Check back soon or select a different course.') }}
                            </p>
                        </div>
                    @endforelse
                </div>

            @else
                <!-- History Tab -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">{{ __('My Completed Exam Attempts') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Review your past test scores, accuracy metrics, and detailed question solutions.') }}</p>
                    </div>

                    @if($attempts->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3.5">{{ __('Exam & Course') }}</th>
                                    <th class="px-6 py-3.5">{{ __('Score Obtained') }}</th>
                                    <th class="px-6 py-3.5">{{ __('Percentage') }}</th>
                                    <th class="px-6 py-3.5">{{ __('Breakdown (C / I / U)') }}</th>
                                    <th class="px-6 py-3.5">{{ __('Time Spent') }}</th>
                                    <th class="px-6 py-3.5">{{ __('Date') }}</th>
                                    <th class="px-6 py-3.5 text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($attempts as $att)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $att->exam->title }}</div>
                                            <div class="text-xs text-gray-500">{{ $att->exam->course->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-black text-gray-900">{{ $att->score_obtained }}</span>
                                            <span class="text-xs text-gray-500">/ {{ $att->total_marks }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-xs font-bold">{{ $att->percentage }}%</span>
                                            @if($att->status === 'SUBMITTED' || $att->status === 'TIMED_OUT')
                                                @if($att->is_passed)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                                        {{ __('PASSED') }}
                                                    </span>
                                                @else
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">
                                                        {{ __('FAILED') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                    {{ $att->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                                            <span class="text-emerald-600 font-bold">{{ $att->correct_answers }}</span> /
                                            <span class="text-rose-600 font-bold">{{ $att->incorrect_answers }}</span> /
                                            <span class="text-gray-500">{{ $att->unanswered_questions }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                            {{ gmdate('H:i:s', $att->time_spent_seconds) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $att->started_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            @if($att->status === 'IN_PROGRESS')
                                                <a href="{{ route('student.exams.take', $att->id) }}" wire:navigate class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition">
                                                    {{ __('Resume') }}
                                                </a>
                                            @else
                                                <a href="{{ route('student.exams.result', $att->id) }}" wire:navigate class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg transition">
                                                    {{ __('Scorecard') }} &rarr;
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-12 text-center text-gray-500">
                            <p class="font-bold text-sm text-gray-700">{{ __('No past test attempts found.') }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ __('Take your first mock examination from the "Available Mock Tests" tab above.') }}</p>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <!-- Exam Instructions Modal -->
    @if($showInstructionModal && $selectedExam)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-xl w-full p-6 sm:p-8 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex justify-between items-start border-b pb-4">
                    <div>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wider mb-1">
                            {{ $selectedExam->course->name }}
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ $selectedExam->title }}</h3>
                    </div>
                    <button wire:click="closeInstructions" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                </div>

                <!-- Exam Metrics Grid -->
                <div class="grid grid-cols-3 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200 text-center text-xs">
                    <div>
                        <span class="text-gray-500 block uppercase text-[10px] font-bold">{{ __('Duration') }}</span>
                        <span class="text-base font-extrabold text-gray-900">{{ $selectedExam->duration_minutes }} {{ __('mins') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block uppercase text-[10px] font-bold">{{ __('Total Marks') }}</span>
                        <span class="text-base font-extrabold text-gray-900">{{ $selectedExam->total_marks }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block uppercase text-[10px] font-bold">{{ __('Negative Marks') }}</span>
                        <span class="text-base font-extrabold {{ $selectedExam->has_negative_marking ? 'text-rose-600' : 'text-gray-700' }}">
                            {{ $selectedExam->has_negative_marking ? '-' . $selectedExam->default_negative_marks : __('None') }}
                        </span>
                    </div>
                </div>

                <!-- Instructions Text -->
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">{{ __('Examination Guidelines & Rules:') }}</h4>
                    <div class="text-xs text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2 max-h-48 overflow-y-auto whitespace-pre-line leading-relaxed">
                        {!! nl2br(e($selectedExam->instructions ?: "1. This mock exam consists of multiple-choice questions.\n2. Once started, the countdown timer will begin immediately.\n3. You may navigate between questions using the Question Palette.\n4. You can only attempt this exam ONCE. Retakes are not allowed.\n5. When the time expires, your exam will be automatically submitted.")) !!}
                    </div>
                </div>

                <!-- Single Attempt Confirmation Notice -->
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-start gap-2">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span><strong>{{ __('Notice:') }}</strong> {{ __('Each student is granted only ONE attempt for this exam. You cannot retake this exam once submitted.') }}</span>
                </div>

                <!-- Confirmation Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox" id="terms" wire:model.live="termsAccepted" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="terms" class="ml-2 text-xs font-semibold text-gray-700 cursor-pointer">
                        {{ __('I have read and agree to follow all the instructions and rules.') }}
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <button wire:click="closeInstructions" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                        {{ __('Cancel') }}
                    </button>
                    <button 
                        wire:click="startExam" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ !$termsAccepted ? 'disabled' : '' }}
                    >
                        {{ __('Begin Examination Now') }} &rarr;
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
