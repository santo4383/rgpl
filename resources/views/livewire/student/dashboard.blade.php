<div>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome & Actions Banner -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('Welcome, :name!', ['name' => auth()->user()->name]) }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Track your practice progress and prepare for your target exams.') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" wire:click="toggleCourseManager" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        {{ __('Manage Target Courses') }}
                    </button>
                    <a href="{{ route('student.practice') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-indigo-700 shadow-xs transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ __('Practice Now') }}
                    </a>
                </div>
            </div>

            <!-- Flash Status -->
            @if (session()->has('message'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <!-- Target Course Manager Modal / Card -->
            @if($showCourseManager)
                <div class="bg-white rounded-xl shadow-md border border-indigo-100 p-6 transition">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('Choose Your Target Courses') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('Select the courses/examinations you are preparing for.') }}</p>
                        </div>
                        <button wire:click="toggleCourseManager" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveCourses">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mb-6">
                            @foreach($allCourses as $c)
                                <label class="flex items-start p-3.5 rounded-lg border cursor-pointer transition {{ in_array($c->id, $selectedCourseIds) ? 'bg-indigo-50 border-indigo-400 ring-1 ring-indigo-400' : 'bg-white border-gray-200 hover:bg-gray-50' }}">
                                    <input type="checkbox" wire:model="selectedCourseIds" value="{{ $c->id }}" class="rounded text-indigo-600 focus:ring-indigo-500 mt-1">
                                    <div class="ml-3">
                                        <div class="font-bold text-sm text-gray-900">{{ $c->name }}</div>
                                        @if($c->description)
                                            <div class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $c->description }}</div>
                                        @endif
                                        <div class="text-[11px] text-gray-400 mt-1">{{ __('Free Quota:') }} {{ $c->free_question_limit ?? 500 }} {{ __('questions') }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="toggleCourseManager" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">{{ __('Cancel') }}</button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-sm">
                                {{ __('Save Target Courses') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Overall Performance Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Enrolled Courses -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5 flex items-center">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Enrolled Courses') }}</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ count($enrolledCourses) }}</div>
                    </div>
                </div>

                <!-- Total Practiced -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5 flex items-center">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Unique Questions Practiced') }}</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAttempted }}</div>
                    </div>
                </div>

                <!-- Accuracy Rate -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5 flex items-center">
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Accuracy Rate') }}</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $overallAccuracy }}%</div>
                    </div>
                </div>

                <!-- Bookmarked Questions -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5 flex items-center">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Bookmarks') }}</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $totalBookmarks }}</div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Courses Section -->
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">{{ __('My Courses & Practice Progress') }}</h3>
                </div>

                @if(count($enrolledCourses) === 0)
                    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-10 text-center">
                        <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800">{{ __('No Target Courses Selected Yet') }}</h4>
                        <p class="text-sm text-gray-500 max-w-md mx-auto mt-1 mb-5">{{ __('Select the courses or examinations you want to prepare for to start practicing questions.') }}</p>
                        <button wire:click="toggleCourseManager" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm">
                            {{ __('Choose Your Courses Now') }}
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($enrolledCourses as $course)
                            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-lg font-bold text-gray-900">{{ $course['name'] }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $course['accuracy'] >= 60 ? 'bg-green-100 text-green-800' : ($course['accuracy'] > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600') }}">
                                            {{ $course['accuracy'] }}% {{ __('Accuracy') }}
                                        </span>
                                    </div>
                                    @if($course['description'])
                                        <p class="text-xs text-gray-500 line-clamp-2 mb-4">{{ $course['description'] }}</p>
                                    @endif

                                    @if($course['is_subscribed'])
                                        <!-- Premium Subscription Badge & Status -->
                                        <div class="mb-4 bg-gradient-to-r from-emerald-50 to-teal-50 p-3 rounded-lg border border-emerald-200">
                                            <div class="flex justify-between items-center text-xs mb-1">
                                                <span class="font-bold text-emerald-800 flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-emerald-600 inline" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                    {{ __('PREMIUM PLAN') }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    {{ __('UNLIMITED ACCESS') }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between text-[11px] text-emerald-700 mt-2 font-medium">
                                                <span>{{ $course['attempted_count'] }} {{ __('questions practiced') }}</span>
                                                <span>{{ $course['total_questions'] }} {{ __('total available') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Free Quota Progress -->
                                        <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                            <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1.5">
                                                <span>{{ __('Free Quota Usage') }}</span>
                                                <span>{{ $course['attempted_count'] }} / {{ $course['free_limit'] }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="h-2 rounded-full {{ $course['is_quota_exhausted'] ? 'bg-red-500' : 'bg-indigo-600' }}" style="width: {{ $course['quota_percentage'] }}%"></div>
                                            </div>
                                            <div class="flex justify-between text-[11px] text-gray-500 mt-1">
                                                <span>
                                                    @if($course['is_quota_exhausted'])
                                                        <span class="text-red-600 font-bold">{{ __('Free Quota Exhausted') }}</span>
                                                    @else
                                                        {{ $course['quota_remaining'] }} {{ __('free questions remaining') }}
                                                    @endif
                                                </span>
                                                <span>{{ $course['total_questions'] }} {{ __('total available') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <div class="text-xs text-gray-500">
                                        <span class="font-bold text-green-600">{{ $course['correct_count'] }}</span> {{ __('correct') }}
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('student.exams', ['course' => $course['id']]) }}" wire:navigate class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg shadow-xs transition">
                                            {{ __('Exams') }}
                                        </a>
                                        <a href="{{ route('student.practice', $course['id']) }}" wire:navigate class="inline-flex items-center px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-xs transition">
                                            {{ __('Practice') }} &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Recent Practice Activity -->
            @if(count($recentAttempts) > 0)
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-base font-bold text-gray-900">{{ __('Recent Practice History') }}</h3>
                        <a href="{{ route('student.practice') }}" wire:navigate class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">{{ __('Continue Practice &rarr;') }}</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($recentAttempts as $att)
                            <div class="p-4 hover:bg-gray-50 flex items-center justify-between transition">
                                <div class="flex items-start space-x-3">
                                    <div class="mt-1">
                                        @if($att->is_correct)
                                            <span class="w-6 h-6 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs" title="Correct">✓</span>
                                        @else
                                            <span class="w-6 h-6 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-bold text-xs" title="Incorrect">✕</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 line-clamp-1">
                                            {{ $att->question->question_text ?? 'Question #' . $att->question_id }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <span class="font-semibold text-gray-700">{{ $att->course->name ?? 'Course' }}</span> &bull; 
                                            {{ $att->last_attempted_at ? $att->last_attempted_at->diffForHumans() : 'Recently' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $att->is_correct ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $att->is_correct ? 'Correct' : 'Incorrect' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
