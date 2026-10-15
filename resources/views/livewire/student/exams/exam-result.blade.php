<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ __('Exam Scorecard & Solution Analysis') }}
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                        {{ $exam->course->name }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $exam->title }} &bull; {{ __('Completed on') }} {{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y H:i') : $attempt->updated_at->format('M d, Y H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('student.exams') }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-lg shadow-xs transition">
                    &larr; {{ __('Back to All Exams') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash message -->
            @if (session()->has('message'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('message') }}
                </div>
            @endif

            <!-- HERO SCORECARD -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-6 sm:p-8 overflow-hidden">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black tracking-wider uppercase {{ $attempt->is_passed ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                                {{ $attempt->is_passed ? __('PASSED') : __('FAILED') }}
                            </span>
                            <span class="text-xs text-gray-500 font-medium">
                                {{ __('Passing Cut-off:') }} {{ $exam->pass_percentage }}%
                            </span>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-gray-900 mt-2">
                            {{ $attempt->score_obtained }} <span class="text-gray-400 text-lg font-medium">/ {{ $attempt->total_marks }} {{ __('Marks') }}</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ __('Time Spent:') }} <strong>{{ gmdate('H:i:s', $attempt->time_spent_seconds) }}</strong> / {{ $exam->duration_minutes }} {{ __('minutes allocated') }}
                        </p>
                    </div>

                    <!-- Stat Cards Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="bg-gray-50 border border-gray-100 p-3 rounded-xl">
                            <span class="text-[10px] font-bold text-gray-500 uppercase block">{{ __('Percentage') }}</span>
                            <span class="text-xl font-black text-gray-900">{{ $attempt->percentage }}%</span>
                        </div>

                        <div class="bg-gray-50 border border-gray-100 p-3 rounded-xl">
                            <span class="text-[10px] font-bold text-gray-500 uppercase block">{{ __('Accuracy') }}</span>
                            <span class="text-xl font-black {{ $accuracy >= 60 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $accuracy }}%</span>
                        </div>

                        <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl">
                            <span class="text-[10px] font-bold text-emerald-700 uppercase block">{{ __('Correct') }}</span>
                            <span class="text-xl font-black text-emerald-700">{{ $attempt->correct_answers }}</span>
                        </div>

                        <div class="bg-rose-50 border border-rose-100 p-3 rounded-xl">
                            <span class="text-[10px] font-bold text-rose-700 uppercase block">{{ __('Incorrect') }}</span>
                            <span class="text-xl font-black text-rose-700">{{ $attempt->incorrect_answers }}</span>
                        </div>
                    </div>
                </div>

                <!-- Secondary Summary Bar -->
                <div class="pt-4 flex flex-wrap items-center justify-between text-xs text-gray-500 gap-4">
                    <div class="flex items-center space-x-4">
                        <span>{{ __('Total Questions:') }} <strong>{{ $attempt->total_questions }}</strong></span>
                        <span>&bull;</span>
                        <span>{{ __('Attempted:') }} <strong>{{ $attempt->attempted_questions }}</strong></span>
                        <span>&bull;</span>
                        <span>{{ __('Unanswered:') }} <strong>{{ $attempt->unanswered_questions }}</strong></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                            {{ __('Single Attempt Exam — Completed') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- SOLUTIONS & QUESTION-BY-QUESTION ANALYSIS -->
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">{{ __('Detailed Solutions & Explanations') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Review your answers, see correct options, and read detailed solutions.') }}</p>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <button 
                            wire:click="setFilter('all')" 
                            class="px-3 py-1.5 rounded-lg font-bold transition {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            {{ __('All Questions') }} ({{ $attempt->total_questions }})
                        </button>
                        <button 
                            wire:click="setFilter('correct')" 
                            class="px-3 py-1.5 rounded-lg font-bold transition {{ $filter === 'correct' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}"
                        >
                            {{ __('Correct') }} ({{ $attempt->correct_answers }})
                        </button>
                        <button 
                            wire:click="setFilter('incorrect')" 
                            class="px-3 py-1.5 rounded-lg font-bold transition {{ $filter === 'incorrect' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}"
                        >
                            {{ __('Incorrect') }} ({{ $attempt->incorrect_answers }})
                        </button>
                        <button 
                            wire:click="setFilter('unanswered')" 
                            class="px-3 py-1.5 rounded-lg font-bold transition {{ $filter === 'unanswered' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            {{ __('Unanswered') }} ({{ $attempt->unanswered_questions }})
                        </button>
                    </div>
                </div>

                <!-- Questions List -->
                <div class="space-y-4">
                    @forelse($answers as $index => $ans)
                        @php
                            $q = $ans->question;
                            $userOpt = $ans->selectedOption;
                            $isCorrect = $ans->is_correct === true;
                            $isIncorrect = $ans->is_answered && $ans->is_correct === false;
                            $isSkipped = !$ans->is_answered;
                        @endphp
                        <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                            <!-- Question Header -->
                            <div class="px-6 py-3.5 bg-gray-50/80 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="flex items-center space-x-2">
                                    <span class="font-black text-gray-900 text-sm">#{{ $index + 1 }}</span>
                                    @if($q->category)
                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-[11px] rounded font-medium">
                                            {{ $q->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if($isCorrect)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800">
                                            ✓ {{ __('Correct') }} (+{{ $ans->marks_awarded }} {{ __('marks') }})
                                        </span>
                                    @elseif($isIncorrect)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-rose-100 text-rose-800">
                                            ✕ {{ __('Incorrect') }} ({{ $ans->marks_awarded }} {{ __('marks') }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-600">
                                            {{ __('Unanswered / Skipped') }} (0 {{ __('marks') }})
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Question Content -->
                            <div class="p-6 space-y-4">
                                <div class="text-base text-gray-900 leading-relaxed font-medium">
                                    @if($q->question_html)
                                        <div class="math-content prose max-w-none text-gray-900">{!! $q->question_html !!}</div>
                                    @else
                                        <p class="whitespace-pre-line">{{ $q->question_text }}</p>
                                    @endif
                                </div>

                                <!-- Question Diagram / Image -->
                                @if($q->images->count() > 0)
                                    <div class="flex flex-wrap gap-4 pt-1">
                                        @foreach($q->images as $img)
                                            <div class="rounded-xl border border-gray-200 p-2 bg-gray-50">
                                                <img src="{{ Storage::url($img->image_path) }}" alt="Diagram" class="max-h-48 rounded object-contain">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Options with Highlights -->
                                <div class="space-y-2 pt-2">
                                    @foreach($q->options->sortBy('sort_order') as $opt)
                                        @php
                                            $isUserChoice = ($ans->selected_option_id == $opt->id);
                                            $isOptCorrect = (bool) $opt->is_correct;

                                            if ($isOptCorrect) {
                                                $optStyle = 'border-emerald-500 bg-emerald-50/70 text-emerald-950 font-semibold';
                                                $badgeStyle = 'bg-emerald-600 text-white';
                                            } elseif ($isUserChoice && !$isOptCorrect) {
                                                $optStyle = 'border-rose-400 bg-rose-50/70 text-rose-950 font-semibold';
                                                $badgeStyle = 'bg-rose-600 text-white';
                                            } else {
                                                $optStyle = 'border-gray-200 bg-white text-gray-700';
                                                $badgeStyle = 'bg-gray-100 text-gray-700';
                                            }
                                        @endphp
                                        <div class="flex items-center p-3.5 rounded-xl border-2 transition {{ $optStyle }}">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs mr-3 shrink-0 {{ $badgeStyle }}">
                                                {{ $opt->option_key }}
                                            </div>
                                            <div class="flex-1 text-sm">
                                                @if($opt->option_html)
                                                    <div class="math-content prose prose-sm max-w-none">{!! $opt->option_html !!}</div>
                                                @else
                                                    {{ $opt->option_text }}
                                                @endif
                                            </div>
                                            <div class="ml-3 shrink-0 flex items-center gap-1.5 text-xs font-bold">
                                                @if($isOptCorrect)
                                                    <span class="text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">{{ __('Correct Answer') }}</span>
                                                @endif
                                                @if($isUserChoice)
                                                    <span class="text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">{{ __('Your Answer') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Explanation Box -->
                                @if($q->explanation_html || $q->explanation_text)
                                    <div class="mt-4 p-4 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 space-y-1">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">{{ __('Explanation & Solution:') }}</span>
                                        <div class="math-content prose prose-sm max-w-none text-gray-700">
                                            {!! $q->explanation_html ?: nl2br(e($q->explanation_text)) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
                            {{ __('No questions match the selected filter.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Bottom Back Button -->
            <div class="pt-6 border-t border-gray-200 flex justify-between items-center">
                <a href="{{ route('student.exams') }}" wire:navigate class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    &larr; {{ __('Back to Examination Center') }}
                </a>
                <a href="{{ route('student.practice') }}" wire:navigate class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-xl shadow-xs transition">
                    {{ __('Continue Practice Mode') }} &rarr;
                </a>
            </div>

        </div>
    </div>
</div>
