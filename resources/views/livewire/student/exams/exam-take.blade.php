<div 
    x-data="{
        seconds: {{ $remainingSeconds }},
        formatTime(sec) {
            let h = Math.floor(sec / 3600);
            let m = Math.floor((sec % 3600) / 60);
            let s = sec % 60;
            return (h > 0 ? String(h).padStart(2, '0') + ':' : '') + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        },
        init() {
            let timer = setInterval(() => {
                if (this.seconds > 0) {
                    this.seconds--;
                } else {
                    clearInterval(timer);
                    @this.call('submit', true);
                }
            }, 1000);
        }
    }"
    class="min-h-screen bg-gray-100 flex flex-col"
>
    <!-- TOP EXAM BAR -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <!-- Exam Title & Info -->
            <div class="flex items-center space-x-3 truncate">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0">
                    {{ $exam->course->name }}
                </span>
                <h1 class="font-bold text-sm sm:text-base text-gray-900 truncate">
                    {{ $exam->title }}
                </h1>
            </div>

            <!-- Timer & Actions -->
            <div class="flex items-center space-x-4 shrink-0">
                <!-- Countdown Timer -->
                <div 
                    class="flex items-center space-x-2 px-3.5 py-1.5 rounded-xl border text-sm font-mono font-bold shadow-xs transition"
                    :class="seconds < 300 ? 'bg-rose-50 border-rose-300 text-rose-700 animate-pulse' : 'bg-gray-50 border-gray-200 text-gray-800'"
                >
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-text="formatTime(seconds)">00:00</span>
                </div>

                <!-- Submit Exam Button -->
                <button 
                    wire:click="openSubmitModal" 
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ __('Submit Exam') }}</span>
                </button>
            </div>
        </div>
    </header>

    <!-- EXAM BODY -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT: Current Question Area (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                @if($currentQuestion)
                    @php
                        $currentQId = $currentQuestion->id;
                        $selectedOptId = $userAnswers[$currentQId] ?? null;
                        $isMarked = !empty($reviewFlags[$currentQId]);
                    @endphp

                    <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden flex flex-col justify-between min-h-[520px]">
                        <div>
                            <!-- Question Bar -->
                            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 bg-indigo-600 text-white font-black text-xs rounded-lg shadow-xs">
                                        {{ __('Question') }} {{ $currentIndex + 1 }} / {{ $totalCount }}
                                    </span>
                                    @if($currentQuestion->category)
                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs font-semibold rounded">
                                            {{ $currentQuestion->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-3 text-xs">
                                    <!-- Marks Pill -->
                                    <div class="font-bold flex items-center space-x-1.5">
                                        <span class="text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                                            +{{ $questionMarks }} {{ __('marks') }}
                                        </span>
                                        @if($exam->has_negative_marking && $questionNegMarks > 0)
                                            <span class="text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded">
                                                -{{ $questionNegMarks }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Mark for review checkbox button -->
                                    <button 
                                        wire:click="toggleReview" 
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $isMarked ? 'bg-purple-100 text-purple-800 border border-purple-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-600' }}"
                                    >
                                        <svg class="w-3.5 h-3.5 {{ $isMarked ? 'text-purple-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                                        <span>{{ $isMarked ? __('Marked for Review') : __('Mark for Review') }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Question Content -->
                            <div class="p-6 sm:p-8 space-y-6">
                                <div class="text-base sm:text-lg text-gray-900 leading-relaxed font-medium">
                                    @if($currentQuestion->question_html)
                                        <div class="math-content prose max-w-none text-gray-900">
                                            {!! $currentQuestion->question_html !!}
                                        </div>
                                    @else
                                        <p class="whitespace-pre-line">{{ $currentQuestion->question_text }}</p>
                                    @endif
                                </div>

                                <!-- Question Images -->
                                @if($currentQuestion->images->count() > 0)
                                    <div class="flex flex-wrap gap-4 pt-2">
                                        @foreach($currentQuestion->images as $img)
                                            <div class="rounded-xl border border-gray-200 p-2 bg-gray-50">
                                                <img src="{{ Storage::url($img->image_path) }}" alt="Question diagram" class="max-h-60 rounded-lg object-contain">
                                                @if($img->caption)
                                                    <p class="text-[11px] text-gray-500 text-center mt-1">{{ $img->caption }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Options List (Radio Buttons) -->
                                <div class="space-y-3 pt-2">
                                    @foreach($currentQuestion->options->sortBy('sort_order') as $opt)
                                        @php
                                            $isSelected = ($selectedOptId == $opt->id);
                                        @endphp
                                        <label 
                                            wire:click="selectOption({{ $opt->id }})" 
                                            class="w-full flex items-center p-4 rounded-xl border-2 transition cursor-pointer select-none {{ $isSelected ? 'border-indigo-600 bg-indigo-50/60 shadow-xs' : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50/50' }}"
                                        >
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs mr-3 shrink-0 transition {{ $isSelected ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $opt->option_key }}
                                            </div>

                                            <div class="flex-1 text-sm font-medium text-gray-800">
                                                @if($opt->option_html)
                                                    <div class="math-content prose prose-sm max-w-none">{!! $opt->option_html !!}</div>
                                                @else
                                                    {{ $opt->option_text }}
                                                @endif
                                            </div>

                                            <div class="ml-3 shrink-0">
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $isSelected ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300' }}">
                                                    @if($isSelected)
                                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Navigation Buttons -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center space-x-2">
                                <button 
                                    wire:click="previousQuestion" 
                                    class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-xl shadow-xs transition disabled:opacity-40 disabled:cursor-not-allowed"
                                    {{ $currentIndex === 0 ? 'disabled' : '' }}
                                >
                                    &larr; {{ __('Previous') }}
                                </button>

                                @if($selectedOptId)
                                    <button 
                                        wire:click="clearResponse" 
                                        class="px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition"
                                    >
                                        {{ __('Clear Response') }}
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2">
                                <button 
                                    wire:click="markForReviewAndNext" 
                                    class="px-4 py-2 bg-purple-50 border border-purple-200 hover:bg-purple-100 text-purple-700 font-bold text-xs rounded-xl shadow-xs transition"
                                >
                                    {{ __('Mark for Review & Next') }}
                                </button>

                                @if($currentIndex < $totalCount - 1)
                                    <button 
                                        wire:click="saveAndNext" 
                                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition"
                                    >
                                        {{ __('Save & Next') }} &rarr;
                                    </button>
                                @else
                                    <button 
                                        wire:click="openSubmitModal" 
                                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition"
                                    >
                                        {{ __('Review & Submit') }} &rarr;
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- RIGHT: Question Palette (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-5 space-y-5">
                    <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider border-b pb-2">
                        {{ __('Question Palette') }}
                    </h3>

                    <!-- Status Legend -->
                    <div class="grid grid-cols-2 gap-2 text-[11px] font-semibold text-gray-600">
                        <div class="flex items-center space-x-2">
                            <span class="w-4 h-4 rounded-md bg-emerald-500 text-white flex items-center justify-center text-[9px] font-bold">✓</span>
                            <span>{{ __('Answered') }} ({{ $answeredCount }})</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-4 h-4 rounded-md bg-rose-500 text-white flex items-center justify-center text-[9px] font-bold">✕</span>
                            <span>{{ __('Not Answered') }} ({{ $notAnsweredCount }})</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-4 h-4 rounded-md bg-purple-600 text-white flex items-center justify-center text-[9px] font-bold">?</span>
                            <span>{{ __('Marked Review') }} ({{ $markedForReviewCount }})</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-4 h-4 rounded-md bg-purple-600 ring-2 ring-emerald-400 text-white flex items-center justify-center text-[9px] font-bold">★</span>
                            <span>{{ __('Ans & Marked') }} ({{ $answeredAndMarkedCount }})</span>
                        </div>
                        <div class="flex items-center space-x-2 col-span-2">
                            <span class="w-4 h-4 rounded-md bg-gray-200 text-gray-600 flex items-center justify-center text-[9px] font-bold">&bull;</span>
                            <span>{{ __('Not Visited') }} ({{ $notVisitedCount }})</span>
                        </div>
                    </div>

                    <!-- Question Number Buttons Grid -->
                    <div class="border-t pt-4">
                        <div class="grid grid-cols-5 gap-2 max-h-80 overflow-y-auto pr-1">
                            @foreach($questionIds as $index => $qId)
                                @php
                                    $st = $paletteStatus[$qId] ?? 'not_visited';
                                    $isCurrent = ($currentIndex === $index);

                                    $btnClass = match($st) {
                                        'answered' => 'bg-emerald-500 text-white hover:bg-emerald-600',
                                        'not_answered' => 'bg-rose-500 text-white hover:bg-rose-600',
                                        'marked' => 'bg-purple-600 text-white hover:bg-purple-700',
                                        'answered_marked' => 'bg-purple-600 text-white ring-2 ring-emerald-400',
                                        default => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                                    };

                                    if ($isCurrent) {
                                        $btnClass .= ' ring-2 ring-indigo-600 ring-offset-2 font-black scale-105';
                                    }
                                @endphp
                                <button 
                                    wire:click="jumpToQuestion({{ $index }})" 
                                    class="h-9 rounded-lg text-xs font-bold transition flex items-center justify-center shadow-xs {{ $btnClass }}"
                                >
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Exam shortcut -->
                    <div class="pt-3 border-t">
                        <button 
                            wire:click="openSubmitModal" 
                            class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition text-center"
                        >
                            {{ __('Finish & Submit Exam') }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- SUBMIT CONFIRMATION MODAL -->
    @if($showSubmitModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 sm:p-7 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="text-center">
                    <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('Ready to Submit Exam?') }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Please review your question attempt summary before submitting.') }}</p>
                </div>

                <!-- Summary Grid -->
                <div class="grid grid-cols-2 gap-2 bg-gray-50 p-4 rounded-xl border border-gray-200 text-xs">
                    <div class="flex justify-between py-1 border-b">
                        <span class="text-gray-500 font-medium">{{ __('Total Questions:') }}</span>
                        <span class="font-bold text-gray-900">{{ $totalCount }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b">
                        <span class="text-emerald-700 font-medium">{{ __('Answered:') }}</span>
                        <span class="font-bold text-emerald-700">{{ $answeredCount + $answeredAndMarkedCount }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-rose-700 font-medium">{{ __('Unanswered:') }}</span>
                        <span class="font-bold text-rose-700">{{ $notAnsweredCount + $notVisitedCount }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-purple-700 font-medium">{{ __('Marked Review:') }}</span>
                        <span class="font-bold text-purple-700">{{ $markedForReviewCount }}</span>
                    </div>
                </div>

                <!-- Single Attempt Warning -->
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span><strong>{{ __('Warning:') }}</strong> {{ __('Once submitted, your final score will be calculated and you CANNOT retake this exam.') }}</span>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeSubmitModal" wire:loading.attr="disabled" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition disabled:opacity-50">
                        {{ __('Return to Exam') }}
                    </button>
                    <button type="button" wire:click="submit(false)" wire:loading.attr="disabled" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition disabled:opacity-50 flex items-center gap-1.5">
                        <span wire:loading.remove wire:target="submit">{{ __('Confirm & Submit') }}</span>
                        <span wire:loading wire:target="submit" class="flex items-center gap-1.5">
                            <svg class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ __('Submitting...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
