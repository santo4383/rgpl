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
            $q->whereIn('name', ['SUPER_ADMIN', 'ADMIN']);
        });
        $totalStudents = (clone $studentsQuery)->count();
        $activeStudentsCount = (clone $studentsQuery)->where('is_active', true)->count();
        $deactivatedStudentsCount = (clone $studentsQuery)->where('is_active', false)->count();
        $newStudentsThisWeek = (clone $studentsQuery)->where('created_at', '>=', now()->subDays(7))->count();

        // Recent Registered Students with Subscriptions, Courses, and Quota Details
        $recentStudents = (clone $studentsQuery)
            ->with(['courses', 'subscriptions.plan'])
            ->withCount(['practiceAttempts', 'examAttempts'])
            ->latest('created_at')
            ->take(6)
            ->get();

        // Student Logins Today
        $todayStudentLoginsCount = AuditLog::where('action', 'LOGIN')
            ->where('created_at', '>=', now()->startOfDay())
            ->whereHas('user', function ($q) {
                $q->whereDoesntHave('roles', fn ($r) => $r->whereIn('name', ['SUPER_ADMIN', 'ADMIN']));
            })
            ->count();

        // Recent Student Activities (logins, registrations, payments)
        $recentStudentActivities = AuditLog::with('user')
            ->whereHas('user', function ($q) {
                $q->whereDoesntHave('roles', fn ($r) => $r->whereIn('name', ['SUPER_ADMIN', 'ADMIN']));
            })
            ->latest('created_at')
            ->take(8)
            ->get();

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
            'activeStudentsCount' => $activeStudentsCount,
            'deactivatedStudentsCount' => $deactivatedStudentsCount,
            'newStudentsThisWeek' => $newStudentsThisWeek,
            'recentStudents' => $recentStudents,
            'todayStudentLoginsCount' => $todayStudentLoginsCount,
            'recentStudentActivities' => $recentStudentActivities,
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
