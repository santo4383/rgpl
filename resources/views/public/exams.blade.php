<x-public-layout title="Mock Exams &amp; Timed Tests - RGPL Academy" description="Simulate real competitive exam environments with live timers, question palettes, and negative marking scoring.">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 via-blue-950 to-indigo-950 text-white py-16 lg:py-20 text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Exam Hall Simulation
            </span>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight">
                Timed Mock Examination Mode
            </h1>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                Experience the pressure of authentic competitive testing with live countdown timers, question status palettes, negative marking, and instant percentile scorecards.
            </p>
            <div class="pt-4">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 transition shadow-lg">
                        Register &amp; Take Mock Exams &rarr;
                    </a>
                @else
                    <a href="{{ route('student.exams') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl font-bold text-white bg-blue-700 hover:bg-blue-800 transition shadow-lg">
                        View Available Exams &rarr;
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Exam Features -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-xl bg-red-100 text-red-700 flex items-center justify-center font-bold text-xl">1</div>
                <h3 class="text-xl font-bold text-slate-900">Live Countdown Timer</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Persistent timer counts down to the exact second. Automated auto-submission ensures strict test integrity when time expires.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xl">2</div>
                <h3 class="text-xl font-bold text-slate-900">Interactive Question Palette</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Jump between questions instantly. Color codes distinctly mark Answered, Unanswered, and Marked-for-Review questions.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xl">3</div>
                <h3 class="text-xl font-bold text-slate-900">Detailed Scorecards</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Instant evaluation computes total marks, accuracy percentage, negative penalty subtractions, and complete question reviews.</p>
            </div>
        </div>
    </div>
</x-public-layout>
