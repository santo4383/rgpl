<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ __('Assign Questions to Exam') }}
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                        {{ $exam->course->name }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $exam->title }} &bull; {{ $exam->duration_minutes }} {{ __('minutes') }} &bull; {{ $exam->pass_percentage }}% {{ __('passing') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.exams.edit', $exam->id) }}" wire:navigate class="px-3.5 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-lg shadow-xs transition">
                    {{ __('Edit Exam Settings') }}
                </a>
                <a href="{{ route('admin.exams.index') }}" wire:navigate class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-xs transition">
                    &larr; {{ __('Done / Back to Exams') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash & Error Alerts -->
            @if (session()->has('message'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('message') }}
                </div>
            @endif

            @error('allocation')
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-bold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    {{ $message }}
                </div>
            @enderror

            <!-- Total Marks Allocation Status Bar -->
            @php
                $pctAllocated = $exam->total_marks > 0 ? min(100, round(($assignedMarks / $exam->total_marks) * 100, 1)) : 0;
                $isFullyAllocated = abs($assignedMarks - $exam->total_marks) < 0.01;
            @endphp
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Marks Allocation Monitor') }}</span>
                        <div class="text-2xl font-black text-gray-900 mt-0.5">
                            <span class="{{ $isFullyAllocated ? 'text-emerald-600' : 'text-indigo-600' }}">{{ $assignedMarks }}</span>
                            <span class="text-gray-400 font-medium">/ {{ $exam->total_marks }}</span>
                            <span class="text-xs font-semibold ml-2 text-gray-500">({{ $pctAllocated }}% allocated)</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-xs">
                        <div class="bg-gray-50 border border-gray-200 px-3 py-2 rounded-lg text-center">
                            <span class="text-gray-500 block font-medium">{{ __('Questions Assigned') }}</span>
                            <span class="text-base font-bold text-gray-900">{{ count($assignedQuestions) }}</span>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 px-3 py-2 rounded-lg text-center">
                            <span class="text-gray-500 block font-medium">{{ __('Remaining Marks') }}</span>
                            <span class="text-base font-bold {{ $remainingMarks > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $remainingMarks }}</span>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 px-3 py-2 rounded-lg text-center">
                            <span class="text-gray-500 block font-medium">{{ __('Negative Marking') }}</span>
                            <span class="text-xs font-bold {{ $exam->has_negative_marking ? 'text-rose-600' : 'text-gray-500' }}">
                                {{ $exam->has_negative_marking ? '-' . $exam->default_negative_marks : __('None') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full transition-all duration-300 {{ $isFullyAllocated ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ $pctAllocated }}%"></div>
                </div>

                @if($isFullyAllocated)
                    <div class="mt-3 text-xs text-emerald-700 font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        {{ __('Exam is fully allocated! Total assigned question marks match the exam maximum marks exactly.') }}
                    </div>
                @elseif($remainingMarks > 0)
                    <div class="mt-3 text-xs text-amber-700 font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ __('You have') }} <strong>{{ $remainingMarks }} {{ __('marks unallocated') }}</strong>. {{ __('You can add more questions from the bank below.') }}
                    </div>
                @endif
            </div>

            <!-- SECTION 1: Currently Assigned Questions -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                            {{ __('Assigned Questions') }} ({{ count($assignedQuestions) }})
                        </h3>
                        <p class="text-xs text-gray-500">{{ __('Questions currently included in this examination. You can customize marks per question.') }}</p>
                    </div>
                </div>

                @if(count($assignedQuestions) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($assignedQuestions as $index => $eq)
                            <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-gray-50/70 transition">
                                <div class="flex items-start gap-3 max-w-2xl">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-800 font-bold text-xs flex items-center justify-center shrink-0">
                                        Q{{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">
                                            {{ $eq->question->question_text }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-1 text-[11px] text-gray-500">
                                            @if($eq->question->category)
                                                <span class="font-medium text-gray-700">{{ $eq->question->category->name }}</span>
                                                <span>&bull;</span>
                                            @endif
                                            <span>{{ count($eq->question->options) }} {{ __('options') }}</span>
                                            <span>&bull;</span>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">{{ $eq->question->difficulty }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inline Marks Config & Remove -->
                                <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">{{ __('Marks') }}</label>
                                        <input 
                                            type="number" 
                                            step="0.5" 
                                            min="0.5" 
                                            wire:model.defer="editMarks.{{ $eq->id }}" 
                                            class="w-20 text-xs text-center border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 font-bold"
                                        >
                                    </div>

                                    @if($exam->has_negative_marking)
                                        <div>
                                            <label class="block text-[10px] font-bold text-rose-600 uppercase">{{ __('Neg. Marks') }}</label>
                                            <input 
                                                type="number" 
                                                step="0.05" 
                                                min="0" 
                                                wire:model.defer="editNegMarks.{{ $eq->id }}" 
                                                class="w-20 text-xs text-center border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-rose-700 font-bold"
                                            >
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-1 mt-3">
                                        <button 
                                            wire:click="updateMarks({{ $eq->id }})" 
                                            title="{{ __('Save Marks') }}" 
                                            class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-lg transition"
                                        >
                                            {{ __('Save') }}
                                        </button>
                                        <button 
                                            wire:click="removeQuestion({{ $eq->id }})" 
                                            wire:confirm="{{ __('Remove this question from the exam?') }}" 
                                            title="{{ __('Remove') }}" 
                                            class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error("marks_{$eq->id}")
                                <div class="px-6 py-2 bg-rose-50 text-rose-700 text-xs font-bold">{{ $message }}</div>
                            @enderror
                        @endforeach
                    </div>
                @else
                    <div class="p-10 text-center text-gray-500">
                        <p class="font-bold text-sm text-gray-700">{{ __('No questions assigned yet.') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ __('Browse the Question Bank below and click "Add to Exam" to populate this mock test.') }}</p>
                    </div>
                @endif
            </div>

            <!-- SECTION 2: Available Question Bank -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                            {{ __('Question Bank') }} ({{ $exam->course->name }})
                        </h3>
                        <p class="text-xs text-gray-500">{{ __('Search published questions from this course to assign with custom marks.') }}</p>
                    </div>

                    <!-- Search & Filters -->
                    <div class="flex flex-wrap items-center gap-3">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="{{ __('Search question text...') }}" 
                            class="text-xs border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 w-48"
                        >

                        <select wire:model.live="categoryFilter" class="text-xs border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        @if(!empty($subcategories))
                            <select wire:model.live="subcategoryFilter" class="text-xs border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Subcategories') }}</option>
                                @foreach($subcategories as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                @if($availableQuestions->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($availableQuestions as $q)
                            @php
                                $suggestedMarks = $remainingMarks >= 1.0 ? 1.0 : max(0.5, $remainingMarks);
                            @endphp
                            <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-gray-50/70 transition">
                                <div class="max-w-2xl">
                                    <div class="text-sm font-semibold text-gray-900 leading-snug">
                                        {{ $q->question_text }}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-[11px] text-gray-500">
                                        @if($q->category)
                                            <span class="font-medium text-gray-700 bg-gray-100 px-2 py-0.5 rounded">{{ $q->category->name }}</span>
                                        @endif
                                        @if($q->subcategory)
                                            <span>{{ $q->subcategory->name }}</span>
                                            <span>&bull;</span>
                                        @endif
                                        <span>{{ count($q->options) }} {{ __('options') }}</span>
                                        <span>&bull;</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $q->difficulty === 'Hard' ? 'bg-rose-100 text-rose-700' : ($q->difficulty === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                            {{ $q->difficulty }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Add form per question -->
                                <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">{{ __('Marks') }}</label>
                                        <input 
                                            type="number" 
                                            step="0.5" 
                                            min="0.5" 
                                            placeholder="{{ $suggestedMarks }}" 
                                            wire:model.defer="addMarks.{{ $q->id }}" 
                                            class="w-20 text-xs text-center border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 font-bold"
                                        >
                                    </div>

                                    @if($exam->has_negative_marking)
                                        <div>
                                            <label class="block text-[10px] font-bold text-rose-600 uppercase">{{ __('Neg. Marks') }}</label>
                                            <input 
                                                type="number" 
                                                step="0.05" 
                                                min="0" 
                                                placeholder="{{ $exam->default_negative_marks }}" 
                                                wire:model.defer="addNegMarks.{{ $q->id }}" 
                                                class="w-20 text-xs text-center border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-rose-700 font-bold"
                                            >
                                        </div>
                                    @endif

                                    <button 
                                        wire:click="addQuestion({{ $q->id }})" 
                                        class="mt-3 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center gap-1"
                                    >
                                        + {{ __('Add to Exam') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 bg-gray-50 border-t border-gray-200">
                        {{ $availableQuestions->links() }}
                    </div>
                @else
                    <div class="p-10 text-center text-gray-500">
                        <p class="font-bold text-sm text-gray-700">{{ __('No available published questions found.') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ __('All published questions from this course are either already assigned or do not match your filter criteria.') }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
