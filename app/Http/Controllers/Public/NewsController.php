<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $search = $request->query('search', '');

        $newsQuery = NewsArticle::published();
        $annQuery = Announcement::published();

        if (! empty($search)) {
            $newsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
            $annQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($filter === 'announcements') {
            $articles = collect();
            $announcements = $annQuery->paginate(12)->withQueryString();
        } elseif ($filter === 'news') {
            $announcements = collect();
            $articles = $newsQuery->paginate(12)->withQueryString();
        } elseif (in_array($filter, ['Exam Update', 'New Course', 'Practice Update', 'Important Notice'])) {
            $newsQuery->where('category', $filter);
            $annQuery->where('category', $filter);
            $articles = $newsQuery->paginate(6)->withQueryString();
            $announcements = $annQuery->paginate(6)->withQueryString();
        } else {
            // 'all'
            $articles = $newsQuery->paginate(6)->withQueryString();
            $announcements = $annQuery->paginate(6)->withQueryString();
        }

        $featuredItem = NewsArticle::featured()->first() ?? Announcement::featured()->first();

        return view('public.announcements-news.index', compact('articles', 'announcements', 'filter', 'search', 'featuredItem'));
    }

    public function show(string $slug)
    {
        $article = NewsArticle::published()->where('slug', $slug)->firstOrFail();

        $relatedNews = NewsArticle::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->limit(3)
            ->get();

        if ($relatedNews->isEmpty()) {
            $relatedNews = NewsArticle::published()
                ->where('id', '!=', $article->id)
                ->limit(3)
                ->get();
        }

        return view('public.announcements-news.show', compact('article', 'relatedNews'));
    }
}
