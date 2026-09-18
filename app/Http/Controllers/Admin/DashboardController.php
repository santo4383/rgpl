<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Payment;
use App\Models\PracticeAttempt;
use App\Models\Question;
use App\Models\Subscription;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Students
        $studentsQuery = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'SUPER_ADMIN');
        });
        $totalStudents = $studentsQuery->count();
        $newStudentsThisWeek = (clone $studentsQuery)->where('created_at', '>=', now()->subDays(7))->count();

        // Content
        $totalCourses = Course::count();
        $totalQuestions = Question::count();
        $publishedQuestions = Question::where('status', 'PUBLISHED')->count();
        $draftQuestions = Question::where('status', 'DRAFT')->count();

        // Financials
        $approvedRevenue = Payment::where('status', 'APPROVED')->sum('amount');
        $approvedPaymentsCount = Payment::where('status', 'APPROVED')->count();
        $pendingPaymentsCount = Payment::where('status', 'PENDING')->count();
        $pendingPaymentsAmount = Payment::where('status', 'PENDING')->sum('amount');

        // Subscriptions
        $activeSubscriptions = Subscription::where('status', 'ACTIVE')
            ->where('expires_at', '>', now())
            ->count();
        $expiredSubscriptions = Subscription::where('expires_at', '<=', now())->count();

        // Examination & Practice
        $totalPracticeAttempts = PracticeAttempt::count();
        $totalExams = Exam::count();
        $totalExamAttempts = ExamAttempt::count();
        $passedExamAttempts = ExamAttempt::where('is_passed', true)->count();
        $examPassRate = $totalExamAttempts > 0 ? round(($passedExamAttempts / $totalExamAttempts) * 100, 1) : 0;

        // Pending payments awaiting review
        $pendingPayments = Payment::with(['user', 'plan'])
            ->where('status', 'PENDING')
            ->latest('created_at')
            ->take(5)
            ->get();

        // Recent Audit Stream
        $recentAuditLogs = AuditLog::with('user')
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'totalStudents' => $totalStudents,
            'newStudentsThisWeek' => $newStudentsThisWeek,
            'totalCourses' => $totalCourses,
            'totalQuestions' => $totalQuestions,
            'publishedQuestions' => $publishedQuestions,
            'draftQuestions' => $draftQuestions,
            'approvedRevenue' => $approvedRevenue,
            'approvedPaymentsCount' => $approvedPaymentsCount,
            'pendingPaymentsCount' => $pendingPaymentsCount,
            'pendingPaymentsAmount' => $pendingPaymentsAmount,
            'activeSubscriptions' => $activeSubscriptions,
            'expiredSubscriptions' => $expiredSubscriptions,
            'totalPracticeAttempts' => $totalPracticeAttempts,
            'totalExams' => $totalExams,
            'totalExamAttempts' => $totalExamAttempts,
            'examPassRate' => $examPassRate,
            'pendingPayments' => $pendingPayments,
            'recentAuditLogs' => $recentAuditLogs,
        ]);
    }
}
