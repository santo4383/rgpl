<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Exams & Mock Tests') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('Manage mock tests, assign questions with custom marks, and review student attempts.') }}</p>
            </div>
            <a href="{{ route('admin.exams.create') }}" wire:navigate class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition">
                + {{ __('Create New Exam') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session()->has('message'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('message') }}
                </div>
            @endif

            <!-- Filter Controls -->
            <div class="bg-white p-4 rounded-xl shadow-xs border border-gray-200 flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="w-full md:w-1/3">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search exams by title...') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="w-full md:w-1/4">
                    <select wire:model.live="courseFilter" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('All Courses') }}</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Exams Table -->
            <div class="bg-white shadow-xs rounded-xl border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-3.5">{{ __('Exam Title / Course') }}</th>
                            <th class="px-6 py-3.5">{{ __('Marks & Questions') }}</th>
                            <th class="px-6 py-3.5">{{ __('Duration & Pass %') }}</th>
                            <th class="px-6 py-3.5">{{ __('Negative Marking') }}</th>
                            <th class="px-6 py-3.5">{{ __('Status') }}</th>
                            <th class="px-6 py-3.5">{{ __('Attempts') }}</th>
                            <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($exams as $exam)
                            @php
                                $assignedMarks = $exam->assignedMarksTotal();
                                $isFull = abs($assignedMarks - $exam->total_marks) < 0.01;
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $exam->title }}</div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">
                                            {{ $exam->course->name }}
                                        </span>
                                        @if($exam->category)
                                            <span class="text-gray-400">&bull;</span>
                                            <span>{{ $exam->category->name }}</span>
                                        @endif
                                        @if($exam->is_premium)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                PREMIUM
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs">
                                        <div class="font-bold text-gray-800">
                                            <span class="{{ $isFull ? 'text-emerald-600' : 'text-amber-600' }}">{{ $assignedMarks }}</span> / {{ $exam->total_marks }} Marks
                                        </div>
                                        <div class="text-gray-500 text-[11px] mt-0.5">
                                            {{ $exam->questions_count }} {{ __('questions assigned') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-900 font-medium">{{ $exam->duration_minutes }} {{ __('mins') }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $exam->pass_percentage }}% {{ __('to pass') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($exam->has_negative_marking)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                            -{{ $exam->default_negative_marks }} {{ __('marks') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('None') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button 
                                        wire:click="togglePublish({{ $exam->id }})" 
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition {{ $exam->is_published ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $exam->is_published ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $exam->is_published ? __('Published') : __('Draft') }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.exams.attempts', $exam->id) }}" wire:navigate class="text-xs font-bold text-indigo-600 hover:underline">
                                        {{ $exam->attempts_count }} {{ __('attempts') }} &rarr;
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-2">
                                    <a href="{{ route('admin.exams.questions', $exam->id) }}" wire:navigate class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition">
                                        {{ __('Assign Questions') }}
                                    </a>
                                    <a href="{{ route('admin.exams.edit', $exam->id) }}" wire:navigate class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                        {{ __('Edit') }}
                                    </a>
                                    <button wire:click="delete({{ $exam->id }})" wire:confirm="{{ __('Are you sure you want to delete this exam?') }}" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition">
                                        {{ __('Delete') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    <p class="font-bold text-base text-gray-600">{{ __('No exams found.') }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ __('Click "Create New Exam" above to set up your first mock examination.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $exams->links() }}
            </div>

        </div>
    </div>
</div>
