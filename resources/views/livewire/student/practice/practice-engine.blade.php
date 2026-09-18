<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Practice Mode') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Select a topic, test your knowledge with immediate feedback, and master your syllabus.') }}</p>
            </div>
            
            <!-- Quota & Performance Pills -->
            <div class="flex items-center space-x-3 text-xs">
                @if($isSubscribed)
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-1.5 rounded-lg font-semibold flex items-center shadow-xs">
                        <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span>{{ __('Premium Active:') }} <strong>{{ __('Unlimited Practice') }}</strong></span>
                    </div>
                @else
                    <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 px-3 py-1.5 rounded-lg font-medium flex items-center shadow-xs">
                        <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>{{ __('Free Quota:') }} <strong>{{ $quotaRemaining }}</strong> / {{ $freeLimit }} {{ __('remaining') }}</span>
                    </div>
                @endif
                <a href="{{ route('dashboard') }}" wire:navigate class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg font-semibold text-gray-700 shadow-xs transition">
                    &larr; {{ __('Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Controls Bar -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-4 sm:p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                    <!-- Course Filter -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Course') }}</label>
                        <select wire:model.live="course_id" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Category / Subject') }}</label>
                        <select wire:model.live="category_id" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500" {{ empty($categories) ? 'disabled' : '' }}>
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subcategory Filter -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Subcategory / Topic') }}</label>
                        <select wire:model.live="subcategory_id" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500" {{ empty($subcategories) ? 'disabled' : '' }}>
                            <option value="">{{ __('All Subcategories') }}</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Difficulty Filter -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Difficulty') }}</label>
                        <select wire:model.live="difficulty" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">{{ __('All Levels') }}</option>
                            <option value="Easy">{{ __('Easy') }}</option>
                            <option value="Medium">{{ __('Medium') }}</option>
                            <option value="Hard">{{ __('Hard') }}</option>
                        </select>
                    </div>

                    <!-- Question Status Filter -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Filter Mode') }}</label>
                        <select wire:model.live="status_filter" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">{{ __('All Questions') }}</option>
                            <option value="unattempted">{{ __('Unattempted Only') }}</option>
                            <option value="incorrect">{{ __('Incorrect / Mistakes') }}</option>
                            <option value="bookmarked">{{ __('Bookmarked') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Quota Exceeded Alert -->
            @if($quotaExceededAlert || ($isQuotaExhausted && !$hasSubmitted && $currentQuestion))
                <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-r-xl shadow-xs flex items-start justify-between">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-amber-500 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <div>
                            <h4 class="text-sm font-bold text-amber-900">{{ __('Free Question Limit Reached') }}</h4>
                            <p class="text-xs text-amber-800 mt-1">
                                {{ __('You have practiced all') }} {{ $freeLimit }} {{ __('free questions included for this course. To access all remaining premium questions and practice without limits, upgrade to a subscription.') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('student.plans') }}" wire:navigate class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow-xs ml-4 shrink-0">
                        {{ __('View Upgrade Plans') }}
                    </a>
                </div>
            @endif

            <!-- Main Question Area -->
            @if($currentQuestion)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Question Top Header -->
                    <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex flex-wrap justify-between items-center gap-3">
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 font-bold text-xs rounded-md">
                                {{ __('Question') }} {{ $currentIndex + 1 }} / {{ $totalQuestions }}
                            </span>
                            @if($currentQuestion->category)
                                <span class="px-2.5 py-1 bg-gray-200 text-gray-700 text-xs font-medium rounded-md">
                                    {{ $currentQuestion->category->name }}
                                </span>
                            @endif
                            @if($currentQuestion->subcategory)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[11px] rounded">
                                    {{ $currentQuestion->subcategory->name }}
                                </span>
                            @endif
                            <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $currentQuestion->difficulty === 'Easy' ? 'bg-green-100 text-green-700' : ($currentQuestion->difficulty === 'Hard' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $currentQuestion->difficulty }}
                            </span>
                        </div>

                        <!-- Bookmark Button -->
                        <button wire:click="toggleBookmark" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $isBookmarked ? 'bg-amber-50 border-amber-300 text-amber-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 mr-1 {{ $isBookmarked ? 'fill-current text-amber-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                            {{ $isBookmarked ? __('Bookmarked') : __('Bookmark') }}
                        </button>
                    </div>

                    <!-- Question Content -->
                    <div class="p-6 md:p-8">
                        <div class="prose max-w-none text-gray-900 text-base md:text-lg leading-relaxed font-normal mb-8">
                            {!! $currentQuestion->question_html ?: nl2br(e($currentQuestion->question_text)) !!}
                        </div>

                        <!-- Options List -->
                        <div class="space-y-3 mb-6">
                            @foreach($currentQuestion->options as $option)
                                @php
                                    $isSelected = ($selectedOptionId == $option->id);
                                    $isCorrectOption = (bool) $option->is_correct;
                                    
                                    $optionClass = "border-gray-200 hover:bg-indigo-50/50 hover:border-indigo-300";
                                    $badgeClass = "bg-gray-100 text-gray-700 border-gray-300";
                                    $icon = null;

                                    if ($hasSubmitted) {
                                        if ($isCorrectOption) {
                                            $optionClass = "bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20";
                                            $badgeClass = "bg-emerald-600 text-white border-emerald-600";
                                            $icon = "check";
                                        } elseif ($isSelected && !$isCorrectOption) {
                                            $optionClass = "bg-rose-50 border-rose-500 ring-2 ring-rose-500/20";
                                            $badgeClass = "bg-rose-600 text-white border-rose-600";
                                            $icon = "cross";
                                        } else {
                                            $optionClass = "border-gray-200 opacity-60";
                                        }
                                    } elseif ($isSelected) {
                                        $optionClass = "bg-indigo-50 border-indigo-500 ring-2 ring-indigo-500/20";
                                        $badgeClass = "bg-indigo-600 text-white border-indigo-600";
                                    }
                                @endphp

                                <div 
                                    wire:click="selectOption({{ $option->id }})" 
                                    class="flex items-start p-4 rounded-xl border text-left cursor-pointer transition shadow-xs {{ $optionClass }}"
                                >
                                    <div class="w-8 h-8 rounded-lg border font-bold text-sm flex items-center justify-center mr-3.5 shrink-0 transition {{ $badgeClass }}">
                                        @if($icon === 'check')
                                            ✓
                                        @elseif($icon === 'cross')
                                            ✕
                                        @else
                                            {{ $option->option_key }}
                                        @endif
                                    </div>
                                    <div class="flex-grow text-gray-800 text-sm md:text-base pt-1">
                                        {!! $option->option_html ?: e($option->option_text) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Feedback & Explanation Box -->
                        @if($hasSubmitted)
                            <div class="mt-6 rounded-xl border overflow-hidden {{ $isCurrentCorrect ? 'bg-emerald-50/70 border-emerald-200' : 'bg-rose-50/70 border-rose-200' }}">
                                <div class="px-5 py-3.5 flex items-center {{ $isCurrentCorrect ? 'bg-emerald-100/80 text-emerald-900' : 'bg-rose-100/80 text-rose-900' }}">
                                    @if($isCurrentCorrect)
                                        <svg class="w-5 h-5 mr-2 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span class="font-bold text-sm">{{ __('Correct Answer! Great work.') }}</span>
                                    @else
                                        <svg class="w-5 h-5 mr-2 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                        <span class="font-bold text-sm">
                                            {{ __('Incorrect.') }} 
                                            @php
                                                $correctOpt = $currentQuestion->options->firstWhere('is_correct', true);
                                            @endphp
                                            @if($correctOpt)
                                                {{ __('The correct choice is Option') }} <strong>{{ $correctOpt->option_key }}</strong>.
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                @if($currentQuestion->explanation_html || $currentQuestion->explanation_text)
                                    <div class="p-5 text-sm text-gray-800 border-t {{ $isCurrentCorrect ? 'border-emerald-200' : 'border-rose-200' }}">
                                        <div class="font-bold text-xs text-gray-600 uppercase tracking-wider mb-2">{{ __('Explanation & Notes:') }}</div>
                                        <div class="prose prose-sm max-w-none text-gray-700">
                                            {!! $currentQuestion->explanation_html ?: nl2br(e($currentQuestion->explanation_text)) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Navigation Bar -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                        <button 
                            wire:click="previousQuestion" 
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition disabled:opacity-40 disabled:cursor-not-allowed"
                            {{ $currentIndex === 0 ? 'disabled' : '' }}
                        >
                            &larr; {{ __('Previous') }}
                        </button>

                        <div class="text-xs text-gray-500 font-medium">
                            {{ __('Question') }} {{ $currentIndex + 1 }} {{ __('of') }} {{ $totalQuestions }}
                        </div>

                        <button 
                            wire:click="nextQuestion" 
                            class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-xs transition disabled:opacity-40 disabled:cursor-not-allowed"
                            {{ $currentIndex >= $totalQuestions - 1 ? 'disabled' : '' }}
                        >
                            {{ __('Next') }} &rarr;
                        </button>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-xs">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">{{ __('No Questions Found') }}</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto mt-1 mb-6">
                        {{ __('There are no questions matching your selected filters. Try selecting a different category or changing your filter mode.') }}
                    </p>
                    <button wire:click="$set('status_filter', 'all')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-xs">
                        {{ __('Reset Filters') }}
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>
