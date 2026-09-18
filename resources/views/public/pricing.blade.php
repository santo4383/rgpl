<x-public-layout title="Subscription Plans &amp; Pricing - RGPL Academy" description="Affordable, transparent subscription plans for MPSC, Police Bharti, and Commerce examinations.">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 via-blue-950 to-indigo-950 text-white py-16 text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Transparent &amp; Accessible
            </span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight">
                Invest in Your Examination Success
            </h1>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                Choose the plan that fits your preparation timetable. All plans include unlimited practice and full mock exam access.
            </p>
        </div>
    </div>

    <!-- Pricing Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
            
            <!-- Free Starter Card -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700 uppercase tracking-wider mb-4">
                        Starter Pass
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Free Practice</h2>
                    <p class="mt-2 text-xs text-slate-500">Perfect for exploring the platform and testing your baseline knowledge.</p>

                    <div class="mt-6 flex items-baseline gap-1">
                        <span class="text-4xl font-black text-slate-900">₹0</span>
                        <span class="text-xs font-semibold text-slate-500">/ forever free</span>
                    </div>

                    <ul class="mt-6 space-y-3 text-xs text-slate-700">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            <span>Up to 500 practice questions</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            <span>Full bilingual explanations</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            <span>Personal accuracy metrics</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-4">
                    <a href="{{ auth()->check() ? route('student.practice') : route('register') }}" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl font-bold text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                        {{ auth()->check() ? 'Start Practicing' : 'Register Free' }}
                    </a>
                </div>
            </div>

            <!-- Dynamic Active Plans from DB -->
            @foreach($plans as $plan)
                <div class="bg-white rounded-3xl p-8 border-2 {{ $loop->iteration == 2 ? 'border-amber-400 shadow-xl relative' : 'border-slate-200 shadow-sm' }} flex flex-col justify-between">
                    @if($loop->iteration == 2)
                        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-[11px] font-black uppercase bg-amber-500 text-slate-950 shadow-sm">
                            Most Popular Pass
                        </div>
                    @endif

                    <div>
                        <div class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-800 uppercase tracking-wider mb-4">
                            {{ $plan->duration_days }} Days Access
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900">{{ $plan->name }}</h2>
                        <p class="mt-2 text-xs text-slate-500">{{ $plan->description ?? 'Unlimited access to questions and timed mock tests.' }}</p>

                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-black text-slate-900">₹{{ number_format($plan->price, 0) }}</span>
                            <span class="text-xs font-semibold text-slate-500">/ {{ $plan->duration_days }} days</span>
                        </div>

                        <ul class="mt-6 space-y-3 text-xs text-slate-700">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span><strong>Unlimited</strong> practice attempts</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>All timed mock exams &amp; scorecards</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Bilingual explanations in Devanagari</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>Instant UPI QR code verification</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8 pt-4">
                        @guest
                            <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl font-bold text-sm text-slate-950 bg-amber-400 hover:bg-amber-300 transition shadow-sm">
                                Subscribe Now &rarr;
                            </a>
                        @else
                            <a href="{{ route('student.plans') }}" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl font-bold text-sm text-slate-950 bg-amber-400 hover:bg-amber-300 transition shadow-sm">
                                Subscribe Now &rarr;
                            </a>
                        @endguest
                    </div>
                </div>
            @endforeach

        </div>

        <!-- How Payments Work (UPI / QR) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-6">
            <div class="max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full">
                    Payment Procedure
                </span>
                <h2 class="mt-3 text-2xl font-bold text-slate-900">How Manual UPI &amp; Bank Transfer Works</h2>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    To keep subscription fees as affordable as possible without expensive gateway commissions, RGPL Academy supports direct UPI transfers and NEFT/IMPS bank deposits verified by human administrators.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100 text-sm">
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs mb-3">1</div>
                    <div class="font-bold text-slate-900 mb-1">Scan UPI QR or Copy Bank Details</div>
                    <p class="text-xs text-slate-600">Select your desired plan from your student dashboard and scan our official UPI QR code or note our SBI account details.</p>
                </div>
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs mb-3">2</div>
                    <div class="font-bold text-slate-900 mb-1">Submit 12-Digit UTR Number</div>
                    <p class="text-xs text-slate-600">Upload your payment screenshot and enter the 12-digit transaction reference (UTR) provided by your UPI app (Google Pay, PhonePe, Paytm).</p>
                </div>
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs mb-3">3</div>
                    <div class="font-bold text-slate-900 mb-1">Prompt Verification &amp; Activation</div>
                    <p class="text-xs text-slate-600">Our administrative desk verifies the bank credit and activates your plan immediately, unlocking all courses and tests.</p>
                </div>
            </div>
        </div>

    </div>
</x-public-layout>
