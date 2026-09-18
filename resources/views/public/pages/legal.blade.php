<x-public-layout :title="$page->meta_title ?? $page->title" :description="$page->meta_description">
    <!-- Header -->
    <div class="bg-gradient-to-b from-slate-900 to-blue-950 text-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Legal &amp; Policy Compliance</span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white">
                {{ $page->title }}
            </h1>
            <p class="text-xs text-slate-300">
                Last updated: {{ $page->updated_at?->format('F d, Y') ?? 'September 2026' }}
            </p>
        </div>
    </div>

    <!-- Document Content Container -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-14 shadow-sm">
            <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed space-y-4">
                {!! $page->content_html !!}
            </div>

            <div class="mt-12 pt-6 border-t border-slate-200 text-xs text-slate-500 flex flex-wrap justify-between items-center gap-4">
                <span>&copy; {{ date('Y') }} RGPL Academy. All Rights Reserved.</span>
                <a href="{{ url('/contact-us') }}" class="text-blue-700 font-bold hover:underline">Contact Compliance Desk &rarr;</a>
            </div>
        </div>
    </div>
</x-public-layout>
