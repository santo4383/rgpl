<x-public-layout>
    <!-- 3. HERO SLIDER -->
    <section class="relative bg-gradient-to-b from-slate-900 via-blue-950 to-indigo-950 text-white overflow-hidden" 
             x-data="{
                 current: 0,
                 total: {{ is_countable($sliders) ? count($sliders) : 0 }},
                 autoplayTimer: null,
                 startAutoplay() {
                     if (this.total <= 1) return;
                     this.autoplayTimer = setInterval(() => {
                         this.next();
                     }, 6000);
                 },
                 stopAutoplay() {
                     if (this.autoplayTimer) clearInterval(this.autoplayTimer);
                 },
                 next() {
                     if (this.total <= 1) return;
                     this.current = (this.current + 1) % this.total;
                 },
                 prev() {
                     if (this.total <= 1) return;
                     this.current = (this.current - 1 + this.total) % this.total;
                 }
             }" 
             x-init="startAutoplay()"
             @mouseenter="stopAutoplay()" 
             @mouseleave="startAutoplay()">
        
        <!-- Background Ambient Accents -->
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-500 rounded-full filter blur-[100px]"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-500 rounded-full filter blur-[120px]"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 min-h-[560px] grid grid-cols-1 items-center">
            @forelse($sliders as $index => $slider)
                <div x-show="current === {{ $index }}" 
                     @if($index !== 0) x-cloak style="display: none;" @endif
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-4"
                     class="col-start-1 row-start-1 w-full">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        <div class="lg:col-span-8 space-y-6">
                            <!-- Badge -->
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span>{{ $slider->subtitle ?? 'Online MCQ Practice Platform' }}</span>
                            </div>

                            <!-- Main Title -->
                            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                                {{ $slider->title }}
                            </h1>

                            <!-- Description -->
                            @if($slider->description)
                                <p class="text-base sm:text-xl text-slate-300 max-w-2xl leading-relaxed">
                                    {{ $slider->description }}
                                </p>
                            @endif

                            <!-- Action Buttons -->
                            <div class="pt-4 flex flex-wrap items-center gap-4">
                                @if($slider->button_text && $slider->button_url)
                                    @guest
                                        <a href="{{ $slider->button_url == '/practice' ? route('register') : url($slider->button_url) }}" class="inline-flex items-center justify-center px-7 py-3.5 rounded-xl font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-lg shadow-amber-500/20 transition hover:scale-[1.02]">
                                            {{ $slider->button_text }} &rarr;
                                        </a>
                                    @else
                                        <a href="{{ url($slider->button_url) }}" class="inline-flex items-center justify-center px-7 py-3.5 rounded-xl font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-lg shadow-amber-500/20 transition hover:scale-[1.02]">
                                            {{ $slider->button_text }} &rarr;
                                        </a>
                                    @endguest
                                @endif

                                @if($slider->secondary_button_text && $slider->secondary_button_url)
                                    <a href="{{ url($slider->secondary_button_url) }}" class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-semibold text-white bg-slate-800/80 hover:bg-slate-800 border border-slate-700 transition">
                                        {{ $slider->secondary_button_text }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Right Graphic Banner -->
                        <div class="hidden lg:flex lg:col-span-4 justify-center">
                            @if($slider->desktop_image)
                                <div class="relative w-72 h-72 sm:w-80 sm:h-80 rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
                                    <img src="{{ asset('storage/' . $slider->desktop_image) }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                @php
                                    $textForDetect = strtolower($slider->title . ' ' . $slider->subtitle);
                                    $isMpsc = str_contains($textForDetect, 'mpsc');
                                    $isPolice = str_contains($textForDetect, 'police');
                                    $isCommerce = str_contains($textForDetect, 'commerce');
                                    $isFree = str_contains($textForDetect, '500 free') || str_contains($textForDetect, 'free');
                                @endphp
                                <div class="relative w-72 h-72 sm:w-80 sm:h-80 rounded-2xl bg-gradient-to-tr {{ $isMpsc ? 'from-amber-600/30 via-slate-900/80 to-blue-600/30 border-amber-500/30' : ($isCommerce ? 'from-blue-600/30 via-slate-900/80 to-indigo-600/30 border-indigo-500/30' : ($isFree ? 'from-emerald-600/30 via-slate-900/80 to-teal-600/30 border-emerald-500/30' : 'from-blue-600/30 via-indigo-900/40 to-amber-500/20 border-white/10')) }} backdrop-blur-md p-6 flex flex-col justify-between shadow-2xl">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold uppercase tracking-wider {{ $isMpsc ? 'text-amber-400' : ($isCommerce ? 'text-indigo-300' : ($isFree ? 'text-emerald-400' : 'text-amber-400')) }}">
                                            @if($isMpsc) MPSC Stream
                                            @elseif($isPolice) Police Bharti
                                            @elseif($isCommerce) Commerce Stream
                                            @elseif($isFree) Starter Pack
                                            @else Exam Stream
                                            @endif
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="text-2xl font-bold text-white">
                                            @if($isMpsc) Rajyaseva &amp; Combined
                                            @elseif($isPolice) Written &amp; Physical
                                            @elseif($isCommerce) 11th, 12th, CA &amp; CS
                                            @elseif($isFree) 500 Free Questions
                                            @else 100% Syllabus Aligned
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-300">
                                            @if($isMpsc) Polity, History, Geography, CSAT &amp; General Science with detailed explanations.
                                            @elseif($isPolice) Marathi Grammar, General Knowledge, Basic Maths &amp; Reasoning sets.
                                            @elseif($isCommerce) Accountancy, Economics, Mercantile Law, Foundation &amp; Intermediate.
                                            @elseif($isFree) Experience the adaptive practice engine and instant performance analytics at zero cost.
                                            @else Marathi &amp; English bilingual support with step-wise concept reasoning.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between pt-4 border-t border-white/10 text-xs text-slate-300">
                                        <span>
                                            @if($isMpsc) 15,000+ Questions
                                            @elseif($isPolice) 8,000+ Questions
                                            @elseif($isCommerce) 12,000+ Questions
                                            @elseif($isFree) 500 Free MCQs
                                            @else Bilingual Question Bank
                                            @endif
                                        </span>
                                        <span class="text-amber-300 font-semibold">
                                            @if($isFree) Zero Cost Start
                                            @else Instant Solutions
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center w-full py-12">
                    <h1 class="text-4xl font-black text-white">Prepare Smarter. Practice More. Score Better.</h1>
                    <p class="mt-4 text-slate-300">Your trusted online MCQ preparation partner for competitive examinations.</p>
                </div>
            @endforelse
        </div>

        <!-- Slider Controls -->
        @if(count($sliders) > 1)
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 flex items-center justify-between">
                <!-- Dots Indicator -->
                <div class="flex items-center gap-2">
                    @foreach($sliders as $idx => $s)
                        <button @click="current = {{ $idx }}" 
                                :class="current === {{ $idx }} ? 'w-8 bg-amber-400' : 'w-2.5 bg-slate-600 hover:bg-slate-400'"
                                class="h-2.5 rounded-full transition-all duration-300"
                                aria-label="Go to slide {{ $idx + 1 }}"></button>
                    @endforeach
                </div>

                <!-- Prev / Next Arrows -->
                <div class="flex items-center gap-2">
                    <button @click="prev()" aria-label="Previous Slide" class="p-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-white border border-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="next()" aria-label="Next Slide" class="p-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-white border border-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        @endif
    </section>

    <!-- 4. PREPARATION AREAS / COURSES ("Choose Your Preparation Path") -->
    <section class="py-16 sm:py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">Targeted Streams</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Choose Your Preparation Path</h2>
                <p class="mt-3 text-lg text-slate-600">Select your target competitive examination stream. Our modular database allows continuous expansion into new career fields.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($courses as $course)
                    <div class="relative bg-white rounded-2xl border border-slate-200 hover:border-blue-400 p-8 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                        
                        <div>
                            <!-- Header Icon & Badge -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 rounded-xl bg-blue-50 group-hover:bg-blue-600 group-hover:text-white text-blue-700 flex items-center justify-center transition-colors">
                                    @if(str_contains(strtolower($course->name), 'mpsc'))
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    @elseif(str_contains(strtolower($course->name), 'police'))
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                    @else
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    @endif
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $course->questions_count ?? 0 }}+ Questions
                                </span>
                            </div>

                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-700 transition">
                                {{ $course->name }}
                            </h3>

                            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                                {{ $course->description }}
                            </p>

                            <!-- Subcategories preview for Commerce -->
                            @if(str_contains(strtolower($course->name), 'commerce'))
                                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800">11th Commerce</span>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800">12th Commerce</span>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-800">CA</span>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-800">CS</span>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-800">ICWA</span>
                                </div>
                            @endif
                        </div>

                        <!-- Buttons -->
                        <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 gap-3">
                            <a href="{{ url('/courses/' . ($course->slug ?? $course->id)) }}" class="text-center px-4 py-2.5 rounded-lg text-sm font-semibold border border-slate-300 text-slate-700 hover:bg-slate-50 transition">
                                Explore
                            </a>
                            @guest
                                <a href="{{ route('register') }}" class="text-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 transition">
                                    Practice
                                </a>
                            @else
                                <a href="{{ route('student.practice', $course->id) }}" class="text-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 transition">
                                    Practice
                                </a>
                            @endguest
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. RGPL ACADEMY AT A GLANCE (Statistics) -->
    <section class="py-16 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">RGPL Academy at a Glance</h2>
                <p class="mt-2 text-slate-400 text-sm">Empowering exam aspirants statewide with robust, verified test infrastructure.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-6 sm:gap-8 text-center">
                @foreach($statistics as $stat)
                    <div class="p-6 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex flex-col items-center justify-center">
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-black text-amber-400 tracking-tight">
                            {{ $stat->display_value }}
                        </div>
                        <div class="mt-2 text-sm sm:text-base font-bold text-white">
                            {{ $stat->title }}
                        </div>
                        @if($stat->description)
                            <div class="mt-1 text-xs text-slate-400">
                                {{ $stat->description }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. PRACTICE & EXAM MODES ("Practice Your Way") -->
    <section class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">Flexible Learning</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Practice Your Way</h2>
                <p class="mt-3 text-lg text-slate-600">Choose the learning methodology that suits your stage of preparation.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                
                <!-- Card 1: Practice Mode -->
                <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-800 uppercase tracking-wider mb-4">
                            For Practice
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Self-Paced Conceptual Practice</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">
                            Learn at your own pace with question-by-question practice and instant answers.
                        </p>

                        <ul class="mt-6 space-y-3 text-sm text-slate-700">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Subject-wise practice modules</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Difficulty-based practice (Easy, Medium, Hard)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Instant feedback &amp; real-time validation</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Thorough bilingual Marathi &amp; English explanations</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Progress tracking &amp; personal question bookmarks</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8 pt-6">
                        <a href="{{ auth()->check() ? route('student.practice') : route('register') }}" class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-bold text-white bg-blue-700 hover:bg-blue-800 shadow-md transition">
                            Start Practicing &rarr;
                        </a>
                    </div>
                </div>

                <!-- Card 2: Exam Mode -->
                <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider mb-4">
                            For Exam
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Timed Mock Tests &amp; Simulation</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">
                            Test yourself with timed examinations and mock tests.
                        </p>

                        <ul class="mt-6 space-y-3 text-sm text-slate-700">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Timed tests with persistent countdown timer</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Question palette navigation (Answered, Unvisited, Review)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Mark for review &amp; single-attempt enforcement</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Automatic scoring with negative marking penalty</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Comprehensive scorecard &amp; full solution review</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8 pt-6">
                        <a href="{{ auth()->check() ? route('student.exams') : url('/exams') }}" class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-bold text-slate-900 bg-amber-400 hover:bg-amber-300 shadow-md transition">
                            Explore Exams &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 7. WHY CHOOSE US ("Everything You Need for Smarter Exam Preparation") -->
    <section class="py-16 sm:py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">The RGPL Academy Advantage</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Everything You Need for Smarter Exam Preparation</h2>
                <p class="mt-3 text-lg text-slate-600">Built ground-up with cutting edge reliability, syllabus rigor, and user experience tailored for Indian competitive exams.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- 1. Organized Question Bank -->
                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Organized Question Bank</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Hierarchical Field &rarr; Category &rarr; Subcategory organization ensures you study specific micro-topics without clutter.</p>
                </div>

                <!-- 2. Exam-Oriented Practice -->
                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-600 text-white flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Exam-Oriented Practice</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Questions mirror past commission examination trends with appropriate difficulty tiers and negative marking parameters.</p>
                </div>

                <!-- 3. Instant Feedback -->
                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Instant Feedback</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Never wait to verify answers. Receive instantaneous right/wrong feedback alongside comprehensive conceptual explanations.</p>
                </div>

                <!-- 4. Performance Tracking -->
                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Performance Tracking</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Comprehensive visual analytics track total questions solved, accuracy percentage, and target course strengths.</p>
                </div>

                <!-- 5. Secure Learning Platform -->
                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-600 text-white flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Secure Learning Platform</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Enterprise role-based security, SMS OTP verification, and transparent manual payment verification protocols.</p>
                </div>

                <!-- 6. Future Mobile Learning -->
                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 transition">
                    <div class="w-12 h-12 rounded-xl bg-rose-600 text-white flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Future Mobile Learning</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">100% responsive web design with upcoming dedicated native mobile companion applications for offline practice.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- 8. HOW IT WORKS (Five-Step Visual Timeline) -->
    <section class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">Easy Onboarding</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">How It Works</h2>
                <p class="mt-3 text-lg text-slate-600">Five simple steps to transform your competitive exam preparation.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 relative">
                
                <!-- Step 01 -->
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs text-center flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-800 font-black text-lg flex items-center justify-center mb-4">
                        01
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Create Account</h3>
                    <p class="mt-2 text-xs text-slate-600">Quick sign-up via your mobile number with secure SMS OTP verification.</p>
                </div>

                <!-- Step 02 -->
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs text-center flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-800 font-black text-lg flex items-center justify-center mb-4">
                        02
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Choose Preparation Area</h3>
                    <p class="mt-2 text-xs text-slate-600">Select MPSC, Police Bharti, or Commerce according to your examination focus.</p>
                </div>

                <!-- Step 03 -->
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs text-center flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-800 font-black text-lg flex items-center justify-center mb-4">
                        03
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Practice 500 Free Questions</h3>
                    <p class="mt-2 text-xs text-slate-600">Solve up to {{ $freeLimit }} high-yield questions completely free with explanations.</p>
                </div>

                <!-- Step 04 -->
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs text-center flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-800 font-black text-lg flex items-center justify-center mb-4">
                        04
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Subscribe for Premium Access</h3>
                    <p class="mt-2 text-xs text-slate-600">Unlock unlimited practice and full mock exam passes with instant UPI verification.</p>
                </div>

                <!-- Step 05 -->
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs text-center flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-800 font-black text-lg flex items-center justify-center mb-4">
                        05
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Improve Your Performance</h3>
                    <p class="mt-2 text-xs text-slate-600">Eliminate weaknesses, track accuracy trends, and secure your competitive rank.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- 9. FREE 500 QUESTIONS CTA ("Start Practicing for Free") -->
    <section class="py-16 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="max-w-2xl space-y-3 text-center md:text-left">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-slate-950 uppercase tracking-wider">
                    Zero Risk Guarantee
                </span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-white">Start Practicing for Free</h2>
                <p class="text-slate-300 text-base sm:text-lg">
                    Begin your preparation with up to <span class="text-amber-400 font-bold">{{ $freeLimit }} free practice questions</span> in your selected preparation area.
                </p>
            </div>

            <div class="shrink-0">
                <a href="{{ auth()->check() ? route('student.practice') : route('register') }}" class="inline-flex items-center px-8 py-4 rounded-xl font-black text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-xl shadow-amber-500/20 transition hover:scale-105">
                    Register &amp; Start Practicing &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- 10. FEATURED COURSES ("Featured Preparation Programs") -->
    <section class="py-16 sm:py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">Curated Curricula</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Featured Preparation Programs</h2>
                <p class="mt-3 text-lg text-slate-600">Explore high-demand subject modules marked as featured by our academic administrators.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($featuredCategories as $cat)
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 bg-blue-100/70 px-2.5 py-0.5 rounded">
                                {{ $cat->course->name ?? 'Course Program' }}
                            </span>
                            <h3 class="mt-3 font-bold text-slate-900 text-lg">
                                {{ $cat->name }}
                            </h3>
                            <p class="mt-2 text-xs text-slate-600 line-clamp-3">
                                {{ $cat->description }}
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-500">{{ $cat->subcategories_count ?? 4 }}+ Modules</span>
                            <a href="{{ url('/courses/' . ($cat->course->slug ?? 'mpsc')) }}" class="text-blue-700 hover:text-blue-800">Explore &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8 text-slate-500">
                        Explore our preparation catalog for featured programs.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- 11. LATEST ANNOUNCEMENTS & NEWS -->
    <section class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">Timely Bulletins</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">Latest Announcements &amp; News</h2>
                    <p class="mt-1 text-slate-600 text-sm">Stay updated with recruitment alerts, timetable changes, and fresh question additions.</p>
                </div>
                <a href="{{ url('/announcements-and-news') }}" class="font-bold text-sm text-blue-700 hover:text-blue-800 inline-flex items-center gap-1">
                    View All Updates &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- News items -->
                @foreach($latestNews as $news)
                    <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-lg transition flex flex-col justify-between">
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800">
                                    NEWS &bull; {{ $news->category }}
                                </span>
                                <span class="text-xs text-slate-500">
                                    {{ $news->publish_date?->format('M d, Y') ?? 'Recent' }}
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-900 text-lg hover:text-blue-700 transition">
                                <a href="{{ url('/news/' . $news->slug) }}">{{ $news->title }}</a>
                            </h3>

                            <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $news->short_description }}
                            </p>
                        </div>

                        <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-500">{{ $news->author ?? 'Editorial Desk' }}</span>
                            <a href="{{ url('/news/' . $news->slug) }}" class="text-blue-700 hover:underline">Read Article &rarr;</a>
                        </div>
                    </article>
                @endforeach

                <!-- Announcement items -->
                @foreach($latestAnnouncements as $ann)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-lg transition flex flex-col justify-between">
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-900">
                                    NOTICE &bull; {{ $ann->category }}
                                </span>
                                <span class="text-xs text-slate-500">
                                    {{ $ann->created_at?->format('M d, Y') }}
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-900 text-lg">
                                {{ $ann->title }}
                            </h3>

                            <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $ann->short_description }}
                            </p>
                        </div>

                        <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
                            <span class="text-emerald-700 font-bold">Priority: {{ $ann->priority }}</span>
                            @if($ann->link)
                                <a href="{{ url($ann->link) }}" class="text-amber-700 hover:underline font-bold">{{ $ann->button_text ?? 'View Details' }} &rarr;</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 12. STUDENT TESTIMONIALS ("What Our Students Say") -->
    <section class="py-16 sm:py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">Candidate Reviews</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">What Our Students Say</h2>
                <p class="mt-3 text-lg text-slate-600">Authentic feedback from aspirants who leveled up their competitive test scores.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($testimonials as $t)
                    <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between shadow-xs">
                        <div>
                            <!-- Star Rating -->
                            <div class="flex items-center gap-1 text-amber-400 mb-4">
                                @for($i = 0; $i < ($t->rating ?? 5); $i++)
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>

                            <p class="text-sm text-slate-700 italic leading-relaxed">
                                "{{ $t->content }}"
                            </p>
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-200 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-700 text-white font-black flex items-center justify-center text-sm shrink-0">
                                {{ substr($t->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $t->name }}</h4>
                                <p class="text-xs text-slate-500">{{ $t->designation }} &bull; <span class="text-blue-700 font-semibold">{{ $t->field }}</span></p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 13. SUBSCRIPTION PLANS (Pricing) -->
    <section class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200" id="pricing">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">Transparent Pricing</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Simple, Predictable Subscription Plans</h2>
                <p class="mt-3 text-lg text-slate-600">Start with our free question quota and upgrade anytime for unlimited question bank and mock exam access.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($plans as $plan)
                    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-amber-400 hover:shadow-xl transition">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">{{ $plan->name }}</h3>
                            <p class="mt-2 text-xs text-slate-500">{{ $plan->description ?? 'Full unlimited access during plan tenure' }}</p>

                            <div class="mt-6 flex items-baseline gap-1">
                                <span class="text-4xl font-black text-slate-900">₹{{ number_format($plan->price, 0) }}</span>
                                <span class="text-xs font-semibold text-slate-500">/ {{ $plan->duration_days }} days</span>
                            </div>

                            <ul class="mt-6 space-y-3 text-xs text-slate-700">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    <span>Unlimited question bank solving</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    <span>Full access to mock examinations</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    <span>Detailed answer rationales</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    <span>Instant UPI transfer activation</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-8 pt-4">
                            @guest
                                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl font-bold text-sm text-slate-900 bg-amber-400 hover:bg-amber-300 transition shadow-sm">
                                    Subscribe &rarr;
                                </a>
                            @else
                                <a href="{{ route('student.plans') }}" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl font-bold text-sm text-slate-900 bg-amber-400 hover:bg-amber-300 transition shadow-sm">
                                    Subscribe &rarr;
                                </a>
                            @endguest
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-500">
                        Active plans will appear here.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- 14. FAQ (Frequently Asked Questions) -->
    <section class="py-16 sm:py-24 bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">Answers &amp; Clarity</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Frequently Asked Questions</h2>
                <p class="mt-2 text-slate-600 text-sm">Have questions regarding our questions, subscriptions, or mock exam platform?</p>
            </div>

            <div class="space-y-4" x-data="{ activeAccordion: null }">
                @foreach($faqs as $index => $faq)
                    <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 transition">
                        <button @click="activeAccordion = (activeAccordion === {{ $index }} ? null : {{ $index }})" 
                                class="w-full px-6 py-4 text-left font-bold text-slate-900 flex items-center justify-between gap-4 focus:outline-none">
                            <span>{{ $faq->question }}</span>
                            <svg class="w-5 h-5 text-slate-500 transition-transform duration-200 shrink-0" 
                                 :class="activeAccordion === {{ $index }} ? 'rotate-180 text-blue-700' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeAccordion === {{ $index }}" 
                             x-collapse
                             class="px-6 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                            {!! $faq->answer_html !!}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <a href="{{ url('/faq') }}" class="font-bold text-sm text-blue-700 hover:text-blue-800 inline-flex items-center gap-1">
                    Explore Complete FAQ Library &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- 15. FINAL CTA -->
    <section class="py-20 bg-gradient-to-tr from-blue-950 via-slate-900 to-indigo-950 text-white text-center relative overflow-hidden">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-6">
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                Ready to Ace Your Competitive Examination?
            </h2>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto">
                Join thousands of MPSC, Police Bharti, and Commerce aspirants practicing on RGPL Academy today. Zero risk with 500 free practice questions.
            </p>
            <div class="pt-4 flex flex-wrap justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-xl shadow-amber-500/20 transition hover:scale-105">
                        Register &amp; Start Practicing Free &rarr;
                    </a>
                    <a href="{{ route('login') }}" class="px-6 py-4 rounded-xl font-semibold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                        Existing Student Login
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 rounded-xl font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-xl shadow-amber-500/20 transition hover:scale-105">
                        Go to My Dashboard &rarr;
                    </a>
                @endguest
            </div>
        </div>
    </section>
</x-public-layout>
