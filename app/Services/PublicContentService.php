<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Course;
use App\Models\Faq;
use App\Models\HomepageSlider;
use App\Models\HomepageStatistic;
use App\Models\NewsArticle;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Models\SubscriptionPlan;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

class PublicContentService
{
    public const CACHE_HOMEPAGE = 'pm_homepage_data';

    public const CACHE_FAQS = 'pm_all_faqs';

    public const CACHE_COURSES = 'pm_public_courses';

    public function getHomepageData(): array
    {
        return [
            'sliders' => HomepageSlider::active()->get(),
            'topAnnouncement' => Announcement::published()->first(),
            'courses' => Course::active()->withCount('questions')->get(),
            'statistics' => HomepageStatistic::active()->get(),
            'featuredCategories' => Category::featured()->with('course')->get(),
            'featuredSubcategories' => Subcategory::featured()->with('category.course')->get(),
            'latestNews' => NewsArticle::published()->limit(4)->get(),
            'latestAnnouncements' => Announcement::published()->limit(4)->get(),
            'testimonials' => Testimonial::published()->get(),
            'plans' => SubscriptionPlan::where('is_active', true)->orderBy('price', 'asc')->get(),
            'faqs' => Faq::homepage()->get(),
            'freeLimit' => (int) Setting::get('default_free_limit', 500),
        ];
    }

    public function getAllFaqs(): array
    {
        return Faq::active()->get()->groupBy('category')->toArray();
    }

    public function getCoursesWithStructure()
    {
        return Course::active()
            ->with(['categories' => function ($q) {
                $q->active()->with(['subcategories' => function ($sq) {
                    $sq->active();
                }]);
            }])
            ->withCount('questions')
            ->get();
    }

    public static function clearAllCache(): void
    {
        Cache::forget(self::CACHE_HOMEPAGE);
        Cache::forget(self::CACHE_FAQS);
        Cache::forget(self::CACHE_COURSES);
    }
}
