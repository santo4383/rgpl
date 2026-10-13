<?php

namespace App\Livewire\Admin\Students;

use App\Models\AuditLog;
use App\Models\Course;
use App\Models\ExamAttempt;
use App\Models\Payment;
use App\Models\PracticeAttempt;
use App\Models\Subscription;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class StudentManager extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all'; // all, active, inactive

    public $subscriptionFilter = 'all'; // all, subscribed, expired, free

    public $courseFilter = '';

    public ?int $selectedStudentId = null;

    public string $activeTab = 'overview'; // overview, subscriptions, practice, exams, logins

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'subscriptionFilter' => ['except' => 'all'],
        'courseFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSubscriptionFilter()
    {
        $this->resetPage();
    }

    public function updatingCourseFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus(int $studentId)
    {
        $student = User::findOrFail($studentId);

        // Prevent deactivating super admins or admins through student portal
        if ($student->hasRole('SUPER_ADMIN') || $student->hasRole('ADMIN')) {
            session()->flash('error', 'Cannot modify administrative account status from student manager.');

            return;
        }

        $student->is_active = ! $student->is_active;
        $student->save();

        AuditLog::record(
            action: $student->is_active ? 'ACTIVATE_STUDENT' : 'DEACTIVATE_STUDENT',
            module: 'STUDENTS',
            recordId: (string) $student->id,
            newValues: [
                'student_id' => $student->id,
                'name' => $student->name,
                'mobile_number' => $student->mobile_number,
                'is_active' => $student->is_active,
            ],
            userId: auth()->id(),
        );

        $actionText = $student->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Student {$student->name} has been {$actionText} successfully.");
    }

    public function viewStudent(int $studentId)
    {
        $this->selectedStudentId = $studentId;
        $this->activeTab = 'overview';
    }

    public function closeStudentModal()
    {
        $this->selectedStudentId = null;
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        // Base query for non-admin students
        $baseQuery = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['SUPER_ADMIN', 'ADMIN']);
        });

        // Summary counts
        $totalCount = (clone $baseQuery)->count();
        $activeCount = (clone $baseQuery)->where('is_active', true)->count();
        $inactiveCount = (clone $baseQuery)->where('is_active', false)->count();
        $subscribedCount = (clone $baseQuery)->whereHas('subscriptions', function ($q) {
            $q->where('status', 'ACTIVE')->where('expires_at', '>', now());
        })->count();

        // Filtered query
        $query = (clone $baseQuery)
            ->with(['courses', 'subscriptions.plan'])
            ->withCount(['practiceAttempts', 'examAttempts']);

        if ($this->search) {
            $term = '%'.$this->search.'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('mobile_number', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        if ($this->subscriptionFilter === 'subscribed') {
            $query->whereHas('subscriptions', function ($q) {
                $q->where('status', 'ACTIVE')->where('expires_at', '>', now());
            });
        } elseif ($this->subscriptionFilter === 'expired') {
            $query->whereHas('subscriptions', function ($q) {
                $q->where('expires_at', '<=', now());
            })->whereDoesntHave('subscriptions', function ($q) {
                $q->where('status', 'ACTIVE')->where('expires_at', '>', now());
            });
        } elseif ($this->subscriptionFilter === 'free') {
            $query->whereDoesntHave('subscriptions');
        }

        if ($this->courseFilter) {
            $query->whereHas('courses', function ($q) {
                $q->where('courses.id', $this->courseFilter);
            });
        }

        $students = $query->latest('created_at')->paginate(15);

        // Selected Student Details for Inspection
        $selectedStudent = null;
        $studentLogins = collect();
        $studentPracticeAttempts = collect();
        $studentExamAttempts = collect();
        $studentPayments = collect();
        $studentSubscriptions = collect();

        if ($this->selectedStudentId) {
            $selectedStudent = User::with(['courses', 'subscriptions.plan', 'payments.plan'])
                ->find($this->selectedStudentId);

            if ($selectedStudent) {
                $studentLogins = AuditLog::where('user_id', $selectedStudent->id)
                    ->where('action', 'LOGIN')
                    ->latest('created_at')
                    ->take(20)
                    ->get();

                $studentPracticeAttempts = PracticeAttempt::with(['question', 'course'])
                    ->where('user_id', $selectedStudent->id)
                    ->latest('last_attempted_at')
                    ->take(25)
                    ->get();

                $studentExamAttempts = ExamAttempt::with('exam')
                    ->where('user_id', $selectedStudent->id)
                    ->latest('created_at')
                    ->take(20)
                    ->get();

                $studentPayments = Payment::with('plan')
                    ->where('user_id', $selectedStudent->id)
                    ->latest('created_at')
                    ->get();

                $studentSubscriptions = Subscription::with('plan')
                    ->where('user_id', $selectedStudent->id)
                    ->latest('created_at')
                    ->get();
            }
        }

        return view('livewire.admin.students.student-manager', [
            'students' => $students,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'subscribedCount' => $subscribedCount,
            'courses' => Course::where('is_active', true)->orderBy('name')->get(),
            'selectedStudent' => $selectedStudent,
            'studentLogins' => $studentLogins,
            'studentPracticeAttempts' => $studentPracticeAttempts,
            'studentExamAttempts' => $studentExamAttempts,
            'studentPayments' => $studentPayments,
            'studentSubscriptions' => $studentSubscriptions,
        ])->layout('layouts.app');
    }
}
