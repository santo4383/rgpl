<x-public-layout :title="$article->meta_title ?? $article->title" :description="$article->meta_description ?? $article->short_description">
    <!-- Breadcrumb & Header -->
    <div class="bg-gradient-to-b from-slate-900 to-blue-950 text-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
            <div class="flex items-center gap-2 text-xs font-semibold text-amber-400">
                <a href="{{ url('/announcements-and-news') }}" class="hover:underline">&larr; All Announcements &amp; News</a>
                <span>&bull;</span>
                <span class="text-slate-300 uppercase">{{ $article->category }}</span>
            </div>
            
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                {{ $article->title }}
            </h1>

            <div class="pt-2 flex flex-wrap items-center gap-4 text-xs text-slate-300">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>By {{ $article->author ?? 'ParikshaMitra Editorial Desk' }}</span>
                </div>
                <span>&bull;</span>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Published {{ $article->publish_date?->format('F d, Y') ?? 'Recently' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-8">
            
            @if($article->featured_image)
                <div class="rounded-2xl overflow-hidden shadow-xs">
                    <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-auto object-cover max-h-96">
                </div>
            @endif

            <!-- Short summary quote callout -->
            @if($article->short_description)
                <div class="border-l-4 border-amber-500 bg-amber-50/60 p-5 rounded-r-xl text-slate-700 font-medium text-base italic leading-relaxed">
                    {{ $article->short_description }}
                </div>
            @endif

            <!-- Article Body -->
            <div class="prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-blue-700 prose-img:rounded-xl leading-relaxed text-slate-700 text-base">
                {!! $article->content_html !!}
            </div>

            <!-- Share Buttons -->
            <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm font-bold text-slate-700">Share this update:</div>
                <div class="flex items-center gap-2">
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                        <span>WhatsApp</span>
                    </a>
                    <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" target="_blank" rel="noopener noreferrer" class="px-3.5 py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                        <span>Telegram</span>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="px-3.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                        <span>X (Twitter)</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Related News -->
        @if($relatedNews->isNotEmpty())
            <div class="mt-16 space-y-6">
                <h3 class="text-2xl font-bold text-slate-900">Related Updates &amp; Articles</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedNews as $related)
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs hover:border-blue-400 transition flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2 py-0.5 rounded">
                                    {{ $related->category }}
                                </span>
                                <h4 class="mt-2 font-bold text-slate-900 text-sm hover:text-blue-700 transition">
                                    <a href="{{ url('/news/' . $related->slug) }}">{{ $related->title }}</a>
                                </h4>
                                <p class="mt-1 text-xs text-slate-600 line-clamp-2">
                                    {{ $related->short_description }}
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>{{ $related->publish_date?->format('M d, Y') }}</span>
                                <a href="{{ url('/news/' . $related->slug) }}" class="text-blue-700 font-semibold hover:underline">Read &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-public-layout>
