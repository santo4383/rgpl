<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Student Attempts: ') }} {{ $exam->title }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $exam->course->name }} &bull; {{ $exam->duration_minutes }} {{ __('mins') }} &bull; {{ $exam->total_marks }} {{ __('Total Marks') }}
                </p>
            </div>
            <a href="{{ route('admin.exams.index') }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-lg shadow-xs transition">
                &larr; {{ __('Back to Exams') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Controls -->
            <div class="bg-white p-4 rounded-xl shadow-xs border border-gray-200 flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="w-full md:w-1/3">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search student name or mobile...') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="w-full md:w-1/4">
                    <select wire:model.live="statusFilter" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="SUBMITTED">{{ __('Submitted') }}</option>
                        <option value="TIMED_OUT">{{ __('Timed Out') }}</option>
                        <option value="IN_PROGRESS">{{ __('In Progress') }}</option>
                    </select>
                </div>
            </div>

            <!-- Attempts Table -->
            <div class="bg-white shadow-xs rounded-xl border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-3.5">{{ __('Student') }}</th>
                            <th class="px-6 py-3.5">{{ __('Score Obtained') }}</th>
                            <th class="px-6 py-3.5">{{ __('Percentage & Result') }}</th>
                            <th class="px-6 py-3.5">{{ __('Questions (C / I / U)') }}</th>
                            <th class="px-6 py-3.5">{{ __('Time Spent') }}</th>
                            <th class="px-6 py-3.5">{{ __('Date') }}</th>
                            <th class="px-6 py-3.5 text-right">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($attempts as $attempt)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $attempt->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $attempt->user->mobile_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-black text-gray-900">{{ $attempt->score_obtained }}</span>
                                    <span class="text-xs text-gray-500">/ {{ $attempt->total_marks }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-bold">{{ $attempt->percentage }}%</span>
                                    @if($attempt->status === 'SUBMITTED' || $attempt->status === 'TIMED_OUT')
                                        @if($attempt->is_passed)
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
                                            {{ $attempt->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <span class="text-emerald-600 font-bold">{{ $attempt->correct_answers }}</span> /
                                    <span class="text-rose-600 font-bold">{{ $attempt->incorrect_answers }}</span> /
                                    <span class="text-gray-500">{{ $attempt->unanswered_questions }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    {{ gmdate('H:i:s', $attempt->time_spent_seconds) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $attempt->started_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('student.exams.result', $attempt->id) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline">
                                        {{ __('View Scorecard') }} &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    <p class="font-bold text-sm text-gray-600">{{ __('No student attempts recorded yet for this exam.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $attempts->links() }}
            </div>

        </div>
    </div>
</div>
