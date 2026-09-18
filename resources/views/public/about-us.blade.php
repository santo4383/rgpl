<x-public-layout title="About Us - RGPL Academy" description="Learn about RGPL Academy, our mission to democratize quality MCQ practice for competitive examinations across Maharashtra.">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 via-blue-950 to-indigo-950 text-white py-16 lg:py-24 text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Our Story &amp; Mission
            </span>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight">
                Empowering Aspirants, Elevating Scores
            </h1>
            <p class="text-slate-300 text-base sm:text-xl max-w-2xl mx-auto leading-relaxed">
                RGPL Academy is an online MCQ practice and examination platform engineered to make competitive exam preparation systematic, affordable, and accessible.
            </p>
        </div>
    </div>

    <!-- Main Content from CMS or Fallback -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        @if($page && $page->content_html)
            <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-14 shadow-sm">
                {!! $page->content_html !!}
            </div>
        @else
            <!-- Standard Full Fallback View -->
            <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-14 shadow-sm space-y-10">
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Who We Are</h2>
                    <p class="text-slate-700 leading-relaxed"><strong>RGPL Academy</strong> ("Your Exam Preparation Partner") is an educational technology initiative dedicated to competitive examination aspirants and commerce students across Maharashtra. We combine deep syllabus mapping with high-yield MCQ authoring to provide a focused, distraction-free online test environment.</p>
                </section>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-100">
                    <div class="p-6 rounded-2xl bg-blue-50/60 border border-blue-100 space-y-2">
                        <h3 class="text-lg font-bold text-blue-900">Our Mission</h3>
                        <p class="text-sm text-blue-950/80 leading-relaxed">To democratize affordable, high-yield examination practice for every student, eliminating geographical barriers and ensuring access to verified conceptual explanations.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-amber-50/60 border border-amber-100 space-y-2">
                        <h3 class="text-lg font-bold text-amber-900">Our Vision</h3>
                        <p class="text-sm text-amber-950/80 leading-relaxed">To become Maharashtra’s premier, trusted digital preparation hub, guiding lakhs of candidates into state administration, police services, and professional finance careers.</p>
                    </div>
                </div>

                <section class="pt-4 border-t border-slate-100">
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">What We Offer</h2>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-slate-700">
                        <li class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-start gap-3">
                            <span class="text-blue-600 font-bold text-lg">&bull;</span>
                            <div><strong>Bilingual Question Bank:</strong> Authentic Marathi and English translations for every state exam question.</div>
                        </li>
                        <li class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-start gap-3">
                            <span class="text-blue-600 font-bold text-lg">&bull;</span>
                            <div><strong>Dual Preparation Modes:</strong> Step-by-step Practice Mode and timed mock exam test simulations.</div>
                        </li>
                        <li class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-start gap-3">
                            <span class="text-blue-600 font-bold text-lg">&bull;</span>
                            <div><strong>500 Free Questions:</strong> Generous free quota so every student experiences platform quality before paying.</div>
                        </li>
                        <li class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-start gap-3">
                            <span class="text-blue-600 font-bold text-lg">&bull;</span>
                            <div><strong>Transparent UPI Payments:</strong> Direct QR transfer and prompt human-verified subscription activations.</div>
                        </li>
                    </ul>
                </section>
            </div>
        @endif

        <!-- CTA Banner -->
        <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-3xl p-8 sm:p-12 text-white text-center space-y-4 shadow-xl">
            <h2 class="text-2xl sm:text-4xl font-black">Begin Your Preparation Today</h2>
            <p class="text-slate-300 text-sm sm:text-base max-w-xl mx-auto">
                Join our community of focused aspirants and experience the power of consistent MCQ practice.
            </p>
            <div class="pt-2">
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 transition shadow-md">
                    Claim Your 500 Free Questions &rarr;
                </a>
            </div>
        </div>
    </div>
</x-public-layout>
