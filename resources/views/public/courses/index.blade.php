<x-public-layout title="Examination Courses &amp; Preparation Fields">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 to-blue-950 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Examination Streams
            </span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight">
                Preparation Areas &amp; Courses
            </h1>
            <p class="max-w-2xl mx-auto text-slate-300 text-base sm:text-lg">
                Explore our syllabus-aligned question banks for MPSC, Maharashtra Police Bharti, and Commerce professional degrees.
            </p>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($courses as $course)
                <div class="bg-white rounded-2xl border border-slate-200 hover:border-blue-400 p-8 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $course->questions_count }} MCQs
                            </span>
                        </div>

                        <h2 class="text-xl font-bold text-slate-900 group-hover:text-blue-700 transition">
                            <a href="{{ url('/courses/' . ($course->slug ?? $course->id)) }}">
                                {{ $course->name }}
                            </a>
                        </h2>

                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                            {{ $course->description }}
                        </p>

                        <!-- Categories list -->
                        <div class="mt-6 pt-4 border-t border-slate-100 space-y-2">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Included Categories</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($course->categories as $cat)
                                    <span class="px-2.5 py-1 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $cat->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-amber-700 font-bold">{{ $course->free_question_limit }} Free Questions</span>
                        <a href="{{ url('/courses/' . ($course->slug ?? $course->id)) }}" class="text-sm font-bold text-blue-700 hover:text-blue-800">
                            Explore Curriculum &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-public-layout>
