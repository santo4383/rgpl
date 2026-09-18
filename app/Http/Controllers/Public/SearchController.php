<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Course;
use App\Models\NewsArticle;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (empty($q)) {
            return view('public.search', [
                'q' => '',
                'courses' => collect(),
                'categories' => collect(),
                'subcategories' => collect(),
                'announcements' => collect(),
                'news' => collect(),
                'totalResults' => 0,
            ]);
        }

        $courses = Course::active()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->get();

        $categories = Category::active()
            ->with('course')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->get();

        $subcategories = Subcategory::active()
            ->with('category.course')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->get();

        $announcements = Announcement::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            })
            ->get();

        $news = NewsArticle::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhere('content_html', 'like', "%{$q}%");
            })
            ->get();

        $totalResults = $courses->count() + $categories->count() + $subcategories->count() + $announcements->count() + $news->count();

        return view('public.search', compact('q', 'courses', 'categories', 'subcategories', 'announcements', 'news', 'totalResults'));
    }
}
