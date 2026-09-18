<x-public-layout title="Search Results - RGPL Academy">
    <!-- Header Search Bar -->
    <div class="bg-gradient-to-b from-slate-900 to-blue-950 text-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <h1 class="text-3xl font-black tracking-tight">Search RGPL Academy</h1>
            <form action="{{ url('/search') }}" method="GET" class="relative">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search courses, categories, announcements, news..." autofocus class="w-full pl-12 pr-28 py-3.5 rounded-2xl bg-white text-slate-900 text-base shadow-lg focus:ring-4 focus:ring-amber-400 focus:outline-none">
                <svg class="w-6 h-6 text-slate-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <button type="submit" class="absolute right-2 top-2 px-5 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm transition">
                    Search
                </button>
            </form>
            @if(!empty($q))
                <div class="text-xs text-slate-300">
                    Found <strong>{{ $totalResults }}</strong> matches for "<span class="text-amber-400">{{ $q }}</span>"
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        @if(empty($q))
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500">
                Type an examination name (e.g. "MPSC", "Police", "Commerce", "Accountancy", "Polity") to search our syllabus programs.
            </div>
        @elseif($totalResults === 0)
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500">
                No matching preparation streams or announcements found. Try a broader search term.
            </div>
        @else
            <!-- Courses Results -->
            @if($courses->isNotEmpty())
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Preparation Courses ({{ $courses->count() }})</span>
                    </h2>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($courses as $c)
                            <a href="{{ url('/courses/' . ($c->slug ?? $c->id)) }}" class="block p-5 rounded-2xl bg-white border border-slate-200 hover:border-blue-500 shadow-xs hover:shadow-md transition">
                                <div class="font-bold text-slate-900 text-base">{{ $c->name }}</div>
                                <div class="text-xs text-slate-600 mt-1">{{ $c->description }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Categories & Topics Results -->
            @if($categories->isNotEmpty() || $subcategories->isNotEmpty())
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span>Curriculum Categories &amp; Topics ({{ $categories->count() + $subcategories->count() }})</span>
                    </h2>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($categories as $cat)
                            <a href="{{ url('/courses/' . ($cat->course->slug ?? $cat->course_id)) }}" class="block p-4 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 shadow-xs transition">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-sm">{{ $cat->name }}</span>
                                    <span class="text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ $cat->course->name ?? 'Course' }}</span>
                                </div>
                                @if($cat->description)
                                    <div class="text-xs text-slate-500 mt-1">{{ $cat->description }}</div>
                                @endif
                            </a>
                        @endforeach

                        @foreach($subcategories as $sub)
                            <a href="{{ url('/courses/' . ($sub->category->course->slug ?? $sub->category->course_id ?? '')) }}" class="block p-4 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 shadow-xs transition">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-sm">{{ $sub->name }}</span>
                                    <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">{{ $sub->category->name ?? 'Category' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- News & Articles -->
            @if($news->isNotEmpty())
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        <span>News Articles ({{ $news->count() }})</span>
                    </h2>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($news as $art)
                            <a href="{{ url('/news/' . $art->slug) }}" class="block p-5 rounded-2xl bg-white border border-slate-200 hover:border-amber-500 shadow-xs transition">
                                <div class="flex items-center justify-between gap-2 text-xs text-slate-500 mb-1">
                                    <span class="font-bold text-amber-700 uppercase">{{ $art->category }}</span>
                                    <span>{{ $art->publish_date?->format('M d, Y') }}</span>
                                </div>
                                <div class="font-bold text-slate-900 text-base">{{ $art->title }}</div>
                                <div class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $art->short_description }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Announcements -->
            @if($announcements->isNotEmpty())
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                        <span>Announcements ({{ $announcements->count() }})</span>
                    </h2>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($announcements as $ann)
                            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                                <div class="text-xs font-bold text-purple-700 uppercase mb-1">{{ $ann->category }}</div>
                                <div class="font-bold text-slate-900 text-base">{{ $ann->title }}</div>
                                <div class="text-xs text-slate-600 mt-1">{{ $ann->short_description }}</div>
                                @if($ann->link)
                                    <div class="mt-3">
                                        <a href="{{ url($ann->link) }}" class="text-xs font-bold text-blue-700 hover:underline">
                                            {{ $ann->button_text ?? 'View Details' }} &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-public-layout>
