<x-public-layout :title="$course->name" :description="$course->description">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 via-blue-950 to-indigo-950 text-white py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl space-y-4">
                <a href="{{ url('/courses') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-400 hover:text-amber-300">
                    &larr; Back to all preparation courses
                </a>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white">
                    {{ $course->name }}
                </h1>
                <p class="text-slate-300 text-base sm:text-lg leading-relaxed">
                    {{ $course->description }}
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-300">
                    <span class="px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white">
                        {{ $course->questions_count }} Curated Questions
                    </span>
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300">
                        {{ $course->free_question_limit }} Free Questions Included
                    </span>
                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300">
                        Bilingual Marathi &amp; English
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Curriculum / Categories Breakdown -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Left 8 Cols: Syllabus & Categories -->
            <div class="lg:col-span-8 space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                        Syllabus Structure &amp; Study Categories
                    </h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Every topic is arranged systematically by subject and micro-topic for progressive concept building.
                    </p>
                </div>

                <div class="space-y-6">
                    @forelse($course->categories as $category)
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-slate-900">
                                    {{ $category->name }}
                                </h3>
                                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                                    {{ count($category->subcategories) }} Topics
                                </span>
                            </div>

                            @if($category->description)
                                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                                    {{ $category->description }}
                                </p>
                            @endif

                            @if($category->subcategories->count() > 0)
                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Subject Topics</div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($category->subcategories as $sub)
                                            <div class="flex items-center gap-2 text-xs font-medium text-slate-700 bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                <span>{{ $sub->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">
                            Course curriculum categories are currently being updated.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right 4 Cols: CTA & Free Practice Card -->
            <div class="lg:col-span-4">
                <div class="sticky top-28 bg-white rounded-3xl border border-slate-200 p-8 shadow-lg space-y-6">
                    <div class="space-y-2">
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase bg-amber-100 text-amber-900">
                            Starter Quota
                        </span>
                        <h3 class="text-2xl font-black text-slate-900">
                            {{ $course->free_question_limit }} Free Questions
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Sign up and immediately practice up to {{ $course->free_question_limit }} questions in this preparation area. No credit card required.
                        </p>
                    </div>

                    <div class="space-y-3 pt-2">
                        @guest
                            <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-md transition">
                                Start Free Practice &rarr;
                            </a>
                            <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 border border-slate-300 text-sm transition">
                                Existing Student Login
                            </a>
                        @else
                            <a href="{{ route('student.practice', $course->id) }}" class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-bold text-white bg-blue-700 hover:bg-blue-800 shadow-md transition">
                                Launch Practice Engine &rarr;
                            </a>
                            <a href="{{ route('student.plans') }}" class="w-full inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 border border-slate-300 text-sm transition">
                                Upgrade to Unlimited Pass
                            </a>
                        @endguest
                    </div>

                    <div class="pt-6 border-t border-slate-100 space-y-3 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>Instant answer evaluation</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>Detailed Marathi &amp; English rationales</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>Personal accuracy tracking</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-public-layout>
