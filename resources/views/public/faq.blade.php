<x-public-layout title="Frequently Asked Questions (FAQ) - RGPL Academy" description="Find answers to common questions regarding RGPL Academy practice quotas, mock exams, and subscription activation.">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 via-blue-950 to-indigo-950 text-white py-16 text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Help &amp; Knowledge Base
            </span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight">
                Frequently Asked Questions
            </h1>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                Everything you need to know about practicing, taking mock exams, and managing subscriptions on RGPL Academy.
            </p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        
        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-semibold">
            <a href="{{ url('/faq') }}" class="px-4 py-2 rounded-xl transition {{ empty($category) || $category === 'all' ? 'bg-blue-700 text-white font-bold' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                All Categories
            </a>
            @foreach($allCategories as $cat)
                <a href="{{ url('/faq?category=' . urlencode($cat)) }}" class="px-4 py-2 rounded-xl shrink-0 transition {{ $category === $cat ? 'bg-blue-700 text-white font-bold' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        <!-- FAQs Accordions -->
        <div class="space-y-8" x-data="{ activeFaq: null }">
            @forelse($faqs as $catName => $items)
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900 border-b border-slate-200 pb-2 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span>{{ $catName }}</span>
                    </h2>

                    <div class="space-y-3">
                        @foreach($items as $faq)
                            <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs">
                                <button @click="activeFaq = (activeFaq === {{ $faq->id }} ? null : {{ $faq->id }})" 
                                        class="w-full px-6 py-4 text-left font-bold text-slate-900 flex items-center justify-between gap-4 focus:outline-none">
                                    <span>{{ $faq->question }}</span>
                                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-200 shrink-0" 
                                         :class="activeFaq === {{ $faq->id }} ? 'rotate-180 text-blue-700' : ''" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="activeFaq === {{ $faq->id }}" 
                                     x-collapse
                                     class="px-6 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                    {!! $faq->answer_html !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-12 text-center text-slate-500 border border-slate-200">
                    No FAQs found matching the selected category.
                </div>
            @endforelse
        </div>

        <!-- Still have questions? -->
        <div class="bg-blue-50 border border-blue-200 rounded-3xl p-8 text-center space-y-3">
            <h3 class="text-xl font-bold text-blue-950">Have a specific question not listed here?</h3>
            <p class="text-sm text-blue-900/80 max-w-md mx-auto">Our academic support desk is ready to help you with course details, mock schedules, and account guidance.</p>
            <div class="pt-2">
                <a href="{{ url('/contact-us') }}" class="inline-flex items-center px-6 py-3 rounded-xl font-bold text-sm text-white bg-blue-700 hover:bg-blue-800 transition shadow-sm">
                    Contact Student Helpdesk &rarr;
                </a>
            </div>
        </div>

    </div>
</x-public-layout>
