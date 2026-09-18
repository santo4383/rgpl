<?php

namespace App\Livewire\Admin\Content;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Course;
use App\Models\Setting;
use App\Services\PublicContentService;
use Livewire\Component;

class HomepageSectionManager extends Component
{
    // Section Visibility Toggles
    public bool $section_announcement_bar = true;

    public bool $section_hero_slider = true;

    public bool $section_courses = true;

    public bool $section_glance = true;

    public bool $section_practice_exam = true;

    public bool $section_why_us = true;

    public bool $section_how_it_works = true;

    public bool $section_free_cta = true;

    public bool $section_featured_courses = true;

    public bool $section_news = true;

    public bool $section_testimonials = true;

    public bool $section_pricing = true;

    public bool $section_faq = true;

    public bool $section_final_cta = true;

    // Featured Content Toggles
    public $allCategories;

    public $allCourses;

    public function mount()
    {
        $this->section_announcement_bar = (bool) Setting::get('sec_announcement_bar', true);
        $this->section_hero_slider = (bool) Setting::get('sec_hero_slider', true);
        $this->section_courses = (bool) Setting::get('sec_courses', true);
        $this->section_glance = (bool) Setting::get('sec_glance', true);
        $this->section_practice_exam = (bool) Setting::get('sec_practice_exam', true);
        $this->section_why_us = (bool) Setting::get('sec_why_us', true);
        $this->section_how_it_works = (bool) Setting::get('sec_how_it_works', true);
        $this->section_free_cta = (bool) Setting::get('sec_free_cta', true);
        $this->section_featured_courses = (bool) Setting::get('sec_featured_courses', true);
        $this->section_news = (bool) Setting::get('sec_news', true);
        $this->section_testimonials = (bool) Setting::get('sec_testimonials', true);
        $this->section_pricing = (bool) Setting::get('sec_pricing', true);
        $this->section_faq = (bool) Setting::get('sec_faq', true);
        $this->section_final_cta = (bool) Setting::get('sec_final_cta', true);

        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->allCourses = Course::active()->get();
        $this->allCategories = Category::with('course')->active()->get();
    }

    public function toggleCategoryFeatured(int $id)
    {
        $category = Category::findOrFail($id);
        $old = $category->is_featured;
        $category->is_featured = ! $category->is_featured;
        $category->save();

        AuditLog::record('CATEGORY_FEATURED_TOGGLED', 'HOMEPAGE', (string) $category->id, ['is_featured' => $old], ['is_featured' => $category->is_featured]);
        PublicContentService::clearAllCache();
        $this->loadCategories();
        session()->flash('message', "Featured status for category '{$category->name}' updated.");
    }

    public function toggleCourseFeatured(int $id)
    {
        $course = Course::findOrFail($id);
        $old = $course->is_featured;
        $course->is_featured = ! $course->is_featured;
        $course->save();

        AuditLog::record('COURSE_FEATURED_TOGGLED', 'HOMEPAGE', (string) $course->id, ['is_featured' => $old], ['is_featured' => $course->is_featured]);
        PublicContentService::clearAllCache();
        $this->loadCategories();
        session()->flash('message', "Featured status for field '{$course->name}' updated.");
    }

    public function saveSections()
    {
        Setting::set('sec_announcement_bar', $this->section_announcement_bar, 'homepage_sections', 'boolean');
        Setting::set('sec_hero_slider', $this->section_hero_slider, 'homepage_sections', 'boolean');
        Setting::set('sec_courses', $this->section_courses, 'homepage_sections', 'boolean');
        Setting::set('sec_glance', $this->section_glance, 'homepage_sections', 'boolean');
        Setting::set('sec_practice_exam', $this->section_practice_exam, 'homepage_sections', 'boolean');
        Setting::set('sec_why_us', $this->section_why_us, 'homepage_sections', 'boolean');
        Setting::set('sec_how_it_works', $this->section_how_it_works, 'homepage_sections', 'boolean');
        Setting::set('sec_free_cta', $this->section_free_cta, 'homepage_sections', 'boolean');
        Setting::set('sec_featured_courses', $this->section_featured_courses, 'homepage_sections', 'boolean');
        Setting::set('sec_news', $this->section_news, 'homepage_sections', 'boolean');
        Setting::set('sec_testimonials', $this->section_testimonials, 'homepage_sections', 'boolean');
        Setting::set('sec_pricing', $this->section_pricing, 'homepage_sections', 'boolean');
        Setting::set('sec_faq', $this->section_faq, 'homepage_sections', 'boolean');
        Setting::set('sec_final_cta', $this->section_final_cta, 'homepage_sections', 'boolean');

        AuditLog::record('HOMEPAGE_SECTIONS_SAVED', 'HOMEPAGE', 'GLOBAL', null, [
            'sec_hero_slider' => $this->section_hero_slider,
            'sec_featured_courses' => $this->section_featured_courses,
        ]);

        PublicContentService::clearAllCache();
        session()->flash('message', 'Homepage section visibility settings saved.');
    }

    public function render()
    {
        return view('livewire.admin.content.homepage-section-manager')->layout('layouts.app');
    }
}
