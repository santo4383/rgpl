<x-public-layout title="Practice Mode Engine - RGPL Academy" description="Interactive, self-paced MCQ practice with instant answer validation and conceptual explanations.">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 via-blue-950 to-indigo-950 text-white py-16 lg:py-20 text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                Self-Paced Learning Engine
            </span>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight">
                Adaptive Practice Mode
            </h1>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                Learn concept by concept without the stress of ticking clocks. Get immediate feedback, bookmark tricky questions, and track your accuracy across topics.
            </p>
            <div class="pt-4">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 transition shadow-lg">
                        Register &amp; Practice Free (500 Questions) &rarr;
                    </a>
                @else
                    <a href="{{ route('student.practice') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl font-bold text-white bg-blue-700 hover:bg-blue-800 transition shadow-lg">
                        Launch Practice Mode &rarr;
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xl">1</div>
                <h3 class="text-xl font-bold text-slate-900">Granular Filtering</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Select your exact Course, Category, and Subcategory topic. Hone in on areas where your syllabus revision needs strengthening.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xl">2</div>
                <h3 class="text-xl font-bold text-slate-900">Instant Answer Feedback</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Click your answer and immediately see whether you were correct, with the correct option highlighted in green.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xl">3</div>
                <h3 class="text-xl font-bold text-slate-900">In-Depth Explanations</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Understand why an option is correct. Our academic team drafts exhaustive explanations with statutory references and history notes.</p>
            </div>
        </div>
    </div>
</x-public-layout>
