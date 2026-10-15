<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Questions Bank') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('Search, filter, and manage questions across academic streams, categories, difficulty, and status.') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-xs rounded-xl shadow-xs transition">
                    &larr; {{ __('Admin Dashboard') }}
                </a>
                <a href="{{ route('admin.questions.create') }}" wire:navigate class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Create Question') }}
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

            <!-- ENHANCED SEARCH & FILTER BAR -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-4 sm:p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
                    
                    <!-- 1. Keyword Search -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Keyword Search') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="{{ __('Search text, HTML, code...') }}"
                                   class="w-full pl-8 text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- 2. Course / Stream Dropdown -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Course / Stream') }}</label>
                        <select wire:model.live="course_id"
                                class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('All Courses / Streams') }}</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Cascading Category Dropdown -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Category') }}</label>
                        <select wire:model.live="category_id"
                                @disabled(empty($course_id))
                                class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                            <option value="">{{ empty($course_id) ? __('Select Course First') : __('All Categories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Cascading Subcategory Dropdown -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Sub-Category') }}</label>
                        <select wire:model.live="subcategory_id"
                                @disabled(empty($category_id))
                                class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                            <option value="">{{ empty($category_id) ? __('Select Category First') : __('All Sub-Categories') }}</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 5. Difficulty Filter -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Difficulty') }}</label>
                        <select wire:model.live="difficulty"
                                class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('All Difficulties') }}</option>
                            <option value="Easy">{{ __('Easy') }}</option>
                            <option value="Medium">{{ __('Medium') }}</option>
                            <option value="Hard">{{ __('Hard') }}</option>
                        </select>
                    </div>

                    <!-- 6. Status Filter -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">{{ __('Status') }}</label>
                        <select wire:model.live="status"
                                class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="DRAFT">{{ __('Draft') }}</option>
                            <option value="REVIEW">{{ __('Needs Review') }}</option>
                            <option value="PUBLISHED">{{ __('Published') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filter Status & Reset Action -->
                <div class="mt-4 pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2 text-gray-600">
                        <span>{{ __('Showing') }} <strong class="text-gray-900">{{ $totalFilteredCount }}</strong> {{ __('question(s)') }}</span>
                        @if($hasActiveFilters)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ __('Filtered View') }}
                            </span>
                        @endif
                    </div>

                    <div>
                        <button type="button"
                                wire:click="resetFilters"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-bold text-xs transition {{ $hasActiveFilters ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            {{ __('Reset All Filters') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- QUESTIONS DATA TABLE -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-500 uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 whitespace-nowrap">{{ __('Code / ID') }}</th>
                                <th class="px-6 py-3.5">{{ __('Course / Category') }}</th>
                                <th class="px-6 py-3.5">{{ __('Question Snippet') }}</th>
                                <th class="px-6 py-3.5 text-center whitespace-nowrap">{{ __('Difficulty') }}</th>
                                <th class="px-6 py-3.5 text-center whitespace-nowrap">{{ __('Status') }}</th>
                                <th class="px-6 py-3.5 text-right whitespace-nowrap">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($questions as $question)
                                <tr class="hover:bg-gray-50/70 transition" wire:key="q-row-{{ $question->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-mono font-bold text-xs bg-slate-100 text-indigo-700 px-2.5 py-1 rounded-md border border-slate-200 inline-block">
                                            {{ $question->question_code ?? ('#'.$question->id) }}
                                        </div>
                                        @if($question->question_number)
                                            <div class="text-[10px] text-gray-400 mt-1">
                                                Q# {{ $question->question_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="font-bold text-gray-900">{{ $question->course->name ?? 'N/A' }}</div>
                                        <div class="text-gray-500">{{ $question->category->name ?? '—' }}</div>
                                        @if($question->subcategory)
                                            <div class="text-[11px] text-gray-400 mt-0.5">&bull; {{ $question->subcategory->name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="math-content font-medium text-gray-800 line-clamp-2 max-w-md [&>p]:inline [&>p]:m-0">
                                            {!! $question->question_html ?: nl2br(e($question->question_text)) !!}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-1">
                                            {{ $question->options->count() }} {{ __('options') }}
                                            @if($question->is_free)
                                                &bull; <span class="text-emerald-600 font-semibold">{{ __('Free') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $diff = strtolower($question->difficulty ?? 'medium');
                                        @endphp
                                        @if($diff === 'easy')
                                            <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ __('Easy') }}
                                            </span>
                                        @elseif($diff === 'hard')
                                            <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                                {{ __('Hard') }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                                {{ __('Medium') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($question->status === 'PUBLISHED')
                                            <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                                                {{ __('Published') }}
                                            </span>
                                        @elseif($question->status === 'REVIEW')
                                            <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-full bg-blue-100 text-blue-800">
                                                {{ __('Needs Review') }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-full bg-amber-100 text-amber-800">
                                                {{ __('Draft') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                        <a href="{{ route('admin.questions.edit', $question) }}"
                                           wire:navigate
                                           class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            {{ __('Edit') }}
                                        </a>
                                        <button type="button"
                                                wire:click="delete({{ $question->id }})"
                                                wire:confirm="{{ __('Are you sure you want to permanently delete this question?') }}"
                                                class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            {{ __('Delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div class="font-bold text-gray-700 text-sm">{{ __('No questions match your search or filter criteria.') }}</div>
                                            <p class="text-xs text-gray-400 max-w-sm">{{ __('Try adjusting keyword terms, selecting a different course or status, or reset all active filters.') }}</p>
                                            @if($hasActiveFilters)
                                                <button type="button" wire:click="resetFilters" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                                    {{ __('Clear All Filters') }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($questions->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $questions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
