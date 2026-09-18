<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\CourseController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Public\StaticPageController;
use App\Livewire\Admin\AuditLogs\AuditLogIndex;
use App\Livewire\Admin\Categories\CategoryForm;
use App\Livewire\Admin\Categories\CategoryIndex;
use App\Livewire\Admin\Content\AnnouncementManager;
use App\Livewire\Admin\Content\ContactMessageManager;
use App\Livewire\Admin\Content\FaqManager;
use App\Livewire\Admin\Content\HomepageSectionManager;
use App\Livewire\Admin\Content\NewsManager;
use App\Livewire\Admin\Content\PageManager;
use App\Livewire\Admin\Content\SliderManager;
use App\Livewire\Admin\Content\StatisticManager;
use App\Livewire\Admin\Content\TestimonialManager;
use App\Livewire\Admin\Courses\CourseForm;
use App\Livewire\Admin\Courses\CourseIndex;
use App\Livewire\Admin\Exams\ExamAttemptsList;
use App\Livewire\Admin\Exams\ExamForm;
use App\Livewire\Admin\Exams\ExamIndex;
use App\Livewire\Admin\Exams\ExamQuestions;
use App\Livewire\Admin\Payments\PaymentIndex;
use App\Livewire\Admin\Plans\PlanForm;
use App\Livewire\Admin\Plans\PlanIndex;
use App\Livewire\Admin\Questions\QuestionForm;
use App\Livewire\Admin\Questions\QuestionIndex;
use App\Livewire\Admin\Settings\SettingsManager;
use App\Livewire\Admin\Subcategories\SubcategoryForm;
use App\Livewire\Admin\Subcategories\SubcategoryIndex;
use App\Livewire\Admin\Users\AdminUserManager;
use App\Livewire\Student\Dashboard;
use App\Livewire\Student\Exams\ExamList;
use App\Livewire\Student\Exams\ExamResult;
use App\Livewire\Student\Exams\ExamTake;
use App\Livewire\Student\Practice\PracticeEngine;
use App\Livewire\Student\Subscriptions\MySubscriptions;
use App\Livewire\Student\Subscriptions\PlanList;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Brand & CMS Website Routes (ParikshaMitra)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('public.home');

Route::get('/courses', [CourseController::class, 'index'])->name('public.courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('public.courses.show');

Route::get('/practice', [StaticPageController::class, 'practice'])->name('public.practice');
Route::get('/exams', [StaticPageController::class, 'exams'])->name('public.exams');

Route::get('/announcements-and-news', [NewsController::class, 'index'])->name('public.news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('public.news.show');

Route::get('/pricing', [StaticPageController::class, 'pricing'])->name('public.pricing');
Route::get('/faq', [StaticPageController::class, 'faq'])->name('public.faq');
Route::get('/about-us', [StaticPageController::class, 'about'])->name('public.about');

Route::get('/contact-us', [ContactController::class, 'index'])->name('public.contact.index');
Route::post('/contact-us', [ContactController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('public.contact.submit');

Route::get('/search', [SearchController::class, 'index'])->name('public.search');

Route::get('/privacy-policy', [StaticPageController::class, 'privacy'])->name('public.privacy');
Route::get('/terms-and-conditions', [StaticPageController::class, 'terms'])->name('public.terms');
Route::get('/refund-policy', [StaticPageController::class, 'refund'])->name('public.refund');

/*
|--------------------------------------------------------------------------
| Authenticated Student Portal Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/student/practice/{course?}', PracticeEngine::class)
    ->middleware(['auth'])
    ->name('student.practice');

Route::get('/subscriptions/plans', PlanList::class)
    ->middleware(['auth'])
    ->name('student.plans');

Route::get('/subscriptions/history', MySubscriptions::class)
    ->middleware(['auth'])
    ->name('student.subscriptions');

Route::get('/student/exams', ExamList::class)
    ->middleware(['auth'])
    ->name('student.exams');

Route::get('/exams/{attempt}/take', ExamTake::class)
    ->middleware(['auth'])
    ->name('student.exams.take');

Route::get('/exams/{attempt}/result', ExamResult::class)
    ->middleware(['auth'])
    ->name('student.exams.result');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

/*
|--------------------------------------------------------------------------
| Admin Portal Authentication & Redirection Routes
|--------------------------------------------------------------------------
*/

// /admin automatic redirection
Route::get('/admin', function () {
    if (auth()->check() && (auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasRole('ADMIN'))) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('admin.login');
})->name('admin.index');

// Dedicated Admin Authentication Routes
Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');
Route::post('/admin/login/send-otp', [AdminLoginController::class, 'sendOtp'])->name('admin.login.send-otp');
Route::post('/admin/login/verify-otp', [AdminLoginController::class, 'verifyOtp'])->name('admin.login.verify-otp');
Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin & Content Management Routes (SUPER_ADMIN, ADMIN)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'role:SUPER_ADMIN,ADMIN'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Super Admin User Management
    Route::get('/users', AdminUserManager::class)
        ->middleware('role:SUPER_ADMIN')
        ->name('admin.users.index');

    // Academic hierarchy
    Route::get('/courses', CourseIndex::class)->name('admin.courses.index');
    Route::get('/courses/create', CourseForm::class)->name('admin.courses.create');
    Route::get('/courses/{course}/edit', CourseForm::class)->name('admin.courses.edit');

    Route::get('/categories', CategoryIndex::class)->name('admin.categories.index');
    Route::get('/categories/create', CategoryForm::class)->name('admin.categories.create');
    Route::get('/categories/{category}/edit', CategoryForm::class)->name('admin.categories.edit');

    Route::get('/subcategories', SubcategoryIndex::class)->name('admin.subcategories.index');
    Route::get('/subcategories/create', SubcategoryForm::class)->name('admin.subcategories.create');
    Route::get('/subcategories/{subcategory}/edit', SubcategoryForm::class)->name('admin.subcategories.edit');

    // Questions bank & mock exams
    Route::get('/questions', QuestionIndex::class)->name('admin.questions.index');
    Route::get('/questions/create', QuestionForm::class)->name('admin.questions.create');
    Route::get('/questions/{question}/edit', QuestionForm::class)->name('admin.questions.edit');

    Route::get('/plans', PlanIndex::class)->name('admin.plans.index');
    Route::get('/plans/create', PlanForm::class)->name('admin.plans.create');
    Route::get('/plans/{plan}/edit', PlanForm::class)->name('admin.plans.edit');

    Route::get('/payments', PaymentIndex::class)->name('admin.payments.index');

    Route::get('/exams', ExamIndex::class)->name('admin.exams.index');
    Route::get('/exams/create', ExamForm::class)->name('admin.exams.create');
    Route::get('/exams/{exam}/edit', ExamForm::class)->name('admin.exams.edit');
    Route::get('/exams/{exam}/questions', ExamQuestions::class)->name('admin.exams.questions');
    Route::get('/exams/{exam}/attempts', ExamAttemptsList::class)->name('admin.exams.attempts');

    // Content Management System (CMS)
    Route::get('/sliders', SliderManager::class)->name('admin.sliders.index');
    Route::get('/statistics', StatisticManager::class)->name('admin.statistics.index');
    Route::get('/homepage-sections', HomepageSectionManager::class)->name('admin.homepage-sections.index');
    Route::get('/announcements', AnnouncementManager::class)->name('admin.announcements.index');
    Route::get('/news', NewsManager::class)->name('admin.news.index');
    Route::get('/faqs', FaqManager::class)->name('admin.faqs.index');
    Route::get('/testimonials', TestimonialManager::class)->name('admin.testimonials.index');
    Route::get('/pages', PageManager::class)->name('admin.pages.index');
    Route::get('/contact-messages', ContactMessageManager::class)->name('admin.contact-messages.index');

    // System Settings & Logs
    Route::get('/audit-logs', AuditLogIndex::class)->name('admin.audit-logs.index');
    Route::get('/settings', SettingsManager::class)->name('admin.settings.index');
});

require __DIR__.'/auth.php';
