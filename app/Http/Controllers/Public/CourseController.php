<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::active()
            ->with(['categories' => function ($q) {
                $q->active()->withCount('subcategories');
            }])
            ->withCount('questions')
            ->get();

        return view('public.courses.index', compact('courses'));
    }

    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? $slug : 0)
            ->with(['categories' => function ($q) {
                $q->active()->with(['subcategories' => function ($sq) {
                    $sq->active();
                }]);
            }])
            ->withCount('questions')
            ->firstOrFail();

        return view('public.courses.show', compact('course'));
    }
}
