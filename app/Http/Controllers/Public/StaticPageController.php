<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Page;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function about()
    {
        $page = Page::published()->where('slug', 'about-us')->first();

        return view('public.about-us', compact('page'));
    }

    public function pricing()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price', 'asc')->get();

        return view('public.pricing', compact('plans'));
    }

    public function faq(Request $request)
    {
        $category = $request->query('category');
        $query = Faq::active();

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $faqs = $query->get()->groupBy('category');
        $allCategories = Faq::active()->distinct()->pluck('category');

        return view('public.faq', compact('faqs', 'allCategories', 'category'));
    }

    public function practice()
    {
        if (auth()->check()) {
            return redirect()->route('student.practice');
        }

        return view('public.practice');
    }

    public function exams()
    {
        if (auth()->check()) {
            return redirect()->route('student.exams');
        }

        return view('public.exams');
    }

    public function privacy()
    {
        $page = Page::published()->where('slug', 'privacy-policy')->firstOrFail();

        return view('public.pages.legal', ['page' => $page, 'navTitle' => 'Privacy Policy']);
    }

    public function terms()
    {
        $page = Page::published()->where('slug', 'terms-and-conditions')->firstOrFail();

        return view('public.pages.legal', ['page' => $page, 'navTitle' => 'Terms and Conditions']);
    }

    public function refund()
    {
        $page = Page::published()->where('slug', 'refund-policy')->firstOrFail();

        return view('public.pages.legal', ['page' => $page, 'navTitle' => 'Refund Policy']);
    }
}
