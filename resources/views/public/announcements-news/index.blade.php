<x-public-layout title="Announcements &amp; News">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 to-blue-950 text-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Official Updates
            </span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight">
                Announcements &amp; News
            </h1>
            <p class="max-w-2xl mx-auto text-slate-300 text-sm sm:text-base">
                Commission notices, syllabus changes, recruitment alerts, and fresh question additions.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Search & Category Filters Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs mb-10 space-y-4">
            <form action="{{ url('/announcements-and-news') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                <div class="md:col-span-8 relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search announcements, news, or exams..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <button type="submit" class="w-full px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-blue-700 hover:bg-blue-800 transition">
                        Search Updates
                    </button>
                    @if(!empty($search) || $filter !== 'all')
                        <a href="{{ url('/announcements-and-news') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Filters -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 pt-2 text-xs font-semibold">
                <span class="text-slate-400 uppercase tracking-wider shrink-0 mr-1">Filter:</span>
                <a href="{{ url('/announcements-and-news?filter=all' . ($search ? "&search={$search}" : '')) }}" 
                   class="px-3 py-1.5 rounded-lg shrink-0 transition {{ $filter === 'all' ? 'bg-blue-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">All</a>
                <a href="{{ url('/announcements-and-news?filter=announcements' . ($search ? "&search={$search}" : '')) }}" 
                   class="px-3 py-1.5 rounded-lg shrink-0 transition {{ $filter === 'announcements' ? 'bg-blue-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Announcements</a>
                <a href="{{ url('/announcements-and-news?filter=news' . ($search ? "&search={$search}" : '')) }}" 
                   class="px-3 py-1.5 rounded-lg shrink-0 transition {{ $filter === 'news' ? 'bg-blue-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">News</a>
                <a href="{{ url('/announcements-and-news?filter=Exam+Update' . ($search ? "&search={$search}" : '')) }}" 
                   class="px-3 py-1.5 rounded-lg shrink-0 transition {{ $filter === 'Exam Update' ? 'bg-blue-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Exam Updates</a>
                <a href="{{ url('/announcements-and-news?filter=New+Course' . ($search ? "&search={$search}" : '')) }}" 
                   class="px-3 py-1.5 rounded-lg shrink-0 transition {{ $filter === 'New Course' ? 'bg-blue-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">New Courses</a>
                <a href="{{ url('/announcements-and-news?filter=Practice+Update' . ($search ? "&search={$search}" : '')) }}" 
                   class="px-3 py-1.5 rounded-lg shrink-0 transition {{ $filter === 'Practice Update' ? 'bg-blue-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Practice Updates</a>
                <a href="{{ url('/announcements-and-news?filter=Important+Notice' . ($search ? "&search={$search}" : '')) }}" 
                   class="px-3 py-1.5 rounded-lg shrink-0 transition {{ $filter === 'Important Notice' ? 'bg-blue-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Important Notices</a>
            </div>
        </div>

        <!-- Featured Spotlight -->
        @if($featuredItem)
            <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-3xl text-white p-8 sm:p-10 mb-12 shadow-md">
                <div class="max-w-3xl space-y-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-400 text-slate-950">
                        Featured Spotlight &bull; {{ $featuredItem->category ?? 'Notice' }}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-white">
                        {{ $featuredItem->title }}
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        {{ $featuredItem->short_description }}
                    </p>
                    <div class="pt-3">
                        @if($featuredItem instanceof \App\Models\NewsArticle)
                            <a href="{{ url('/news/' . $featuredItem->slug) }}" class="inline-flex items-center gap-1 font-bold text-amber-300 hover:text-amber-200 text-sm">
                                Read Full Article &rarr;
                            </a>
                        @elseif($featuredItem->link)
                            <a href="{{ url($featuredItem->link) }}" class="inline-flex items-center gap-1 font-bold text-amber-300 hover:text-amber-200 text-sm">
                                {{ $featuredItem->button_text ?? 'View Details' }} &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Grid of Articles & Announcements -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- News Articles -->
            @foreach($articles as $art)
                <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-lg transition flex flex-col justify-between">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800">
                                NEWS &bull; {{ $art->category }}
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ $art->publish_date?->format('M d, Y') }}
                            </span>
                        </div>

                        <h3 class="font-bold text-slate-900 text-lg hover:text-blue-700 transition">
                            <a href="{{ url('/news/' . $art->slug) }}">{{ $art->title }}</a>
                        </h3>

                        <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">
                            {{ $art->short_description }}
                        </p>
                    </div>

                    <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
                        <span class="text-slate-500">{{ $art->author ?? 'Editorial Desk' }}</span>
                        <a href="{{ url('/news/' . $art->slug) }}" class="text-blue-700 hover:underline">Read Article &rarr;</a>
                    </div>
                </article>
            @endforeach

            <!-- Announcements -->
            @foreach($announcements as $ann)
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

        @if($articles->isEmpty() && $announcements->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500">
                No announcements or news articles found matching your criteria.
            </div>
        @endif

        <!-- Pagination -->
        @if(method_exists($articles, 'links'))
            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        @elseif(method_exists($announcements, 'links'))
            <div class="mt-12">
                {{ $announcements->links() }}
            </div>
        @endif

    </div>
</x-public-layout>
