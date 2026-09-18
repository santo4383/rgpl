<?php

use App\Http\Controllers\Api\V1\Auth\ApiAuthController;
use App\Http\Controllers\Api\V1\Bookmarks\ApiBookmarkController;
use App\Http\Controllers\Api\V1\Content\ApiContentController;
use App\Http\Controllers\Api\V1\Exams\ApiExamController;
use App\Http\Controllers\Api\V1\Fields\ApiFieldController;
use App\Http\Controllers\Api\V1\Notifications\ApiNotificationController;
use App\Http\Controllers\Api\V1\Payments\ApiPaymentController;
use App\Http\Controllers\Api\V1\Practice\ApiPracticeController;
use App\Http\Controllers\Api\V1\Progress\ApiProgressController;
use App\Http\Controllers\Api\V1\Reports\ApiQuestionReportController;
use App\Http\Controllers\Api\V1\Subscriptions\ApiSubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ParikshaMitra Mobile REST API (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // 1. Public Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('login/send-otp', [ApiAuthController::class, 'sendLoginOtp']);
        Route::post('login/verify', [ApiAuthController::class, 'verifyLoginOtp']);
        Route::post('register/send-otp', [ApiAuthController::class, 'sendRegisterOtp']);
        Route::post('register/verify', [ApiAuthController::class, 'verifyRegisterOtp']);
    });

    // 2. Public Content & Metadata
    Route::get('public/home', [ApiContentController::class, 'publicHome']);
    Route::get('public/search', [ApiContentController::class, 'search']);
    Route::post('public/contact', [ApiContentController::class, 'submitContact']);
    Route::get('public/about', [ApiContentController::class, 'about']);
    Route::get('fields', [ApiFieldController::class, 'index']);
    Route::get('fields/{id}', [ApiFieldController::class, 'show']);
    Route::get('subscriptions/plans', [ApiSubscriptionController::class, 'plans']);
    Route::get('announcements', [ApiContentController::class, 'announcements']);
    Route::get('news', [ApiContentController::class, 'news']);
    Route::get('news/{slug}', [ApiContentController::class, 'newsDetail']);
    Route::get('faqs', [ApiContentController::class, 'faqs']);

    // Public Exam Catalog (allow guests to browse available mock tests)
    Route::get('exams', [ApiExamController::class, 'index']);
    Route::get('exams/{id}', [ApiExamController::class, 'show']);

    // 3. Authenticated Student Routes
    Route::middleware('auth:sanctum')->group(function () {

        // Student Profile & Auth Session
        Route::prefix('auth')->group(function () {
            Route::post('logout', [ApiAuthController::class, 'logout']);
            Route::get('me', [ApiAuthController::class, 'me']);
            Route::put('profile', [ApiAuthController::class, 'updateProfile']);
            Route::post('profile/send-otp', [ApiAuthController::class, 'sendMobileChangeOtp']);
            Route::post('profile/verify-mobile', [ApiAuthController::class, 'verifyMobileChange']);
        });

        // Home Aggregated Feed & Daily Modules
        Route::get('home', [ApiContentController::class, 'home']);
        Route::get('daily-quiz', [ApiContentController::class, 'dailyQuiz']);
        Route::get('question-of-the-day', [ApiContentController::class, 'questionOfTheDay']);
        Route::post('question-of-the-day/submit', [ApiContentController::class, 'submitQuestionOfTheDay']);

        // Practice Engine & Quota
        Route::prefix('practice')->group(function () {
            Route::get('questions', [ApiPracticeController::class, 'questions']);
            Route::post('submit', [ApiPracticeController::class, 'submitAnswer']);
            Route::get('summary', [ApiPracticeController::class, 'summary']);
        });

        // Timed Mock Exams Engine (attempts require active session)
        Route::prefix('exams')->group(function () {
            Route::post('{id}/start', [ApiExamController::class, 'start']);
            Route::post('attempts/{attemptId}/answer', [ApiExamController::class, 'saveAnswer']);
            Route::post('attempts/{attemptId}/submit', [ApiExamController::class, 'submit']);
            Route::get('attempts/{attemptId}/result', [ApiExamController::class, 'result']);
        });

        // Subscriptions & Entitlements
        Route::prefix('subscriptions')->group(function () {
            Route::get('my', [ApiSubscriptionController::class, 'mySubscriptions']);
        });

        // Manual Payments & Receipts
        Route::prefix('payments')->group(function () {
            Route::get('instructions', [ApiPaymentController::class, 'instructions']);
            Route::post('submit', [ApiPaymentController::class, 'submit']);
            Route::get('my', [ApiPaymentController::class, 'myPayments']);
        });

        // Bookmarks
        Route::prefix('bookmarks')->group(function () {
            Route::get('/', [ApiBookmarkController::class, 'index']);
            Route::post('{questionId}/toggle', [ApiBookmarkController::class, 'toggle']);
        });

        // Question Quality Reporting
        Route::post('questions/{questionId}/report', [ApiQuestionReportController::class, 'store']);

        // Progress & Analytics
        Route::get('progress', [ApiProgressController::class, 'index']);

        // Notifications & FCM Token Registration
        Route::prefix('notifications')->group(function () {
            Route::get('/', [ApiNotificationController::class, 'index']);
            Route::post('fcm-token', [ApiNotificationController::class, 'registerToken']);
        });
    });
});
