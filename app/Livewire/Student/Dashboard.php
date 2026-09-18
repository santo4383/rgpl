<?php

namespace App\Livewire\Student;

use App\Models\Course;
use App\Models\PracticeAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $showCourseManager = false;

    public $selectedCourseIds = [];

    public function mount()
    {
        $this->selectedCourseIds = Auth::user()->courses()->pluck('courses.id')->toArray();
    }

    public function toggleCourseManager()
    {
        $this->showCourseManager = ! $this->showCourseManager;
    }

    public function saveCourses()
    {
        $user = Auth::user();
        $user->courses()->sync($this->selectedCourseIds);
        $this->showCourseManager = false;
        session()->flash('message', 'Target courses updated successfully!');
    }

    public function enrollCourse($courseId)
    {
        $user = Auth::user();
        if (! $user->courses()->where('courses.id', $courseId)->exists()) {
            $user->courses()->attach($courseId);
            $this->selectedCourseIds[] = $courseId;
            session()->flash('message', 'Enrolled in course successfully!');
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Enrolled courses with question stats
        $enrolledCourses = $user->courses()->withCount([
            'questions' => function ($query) {
                $query->where('status', 'PUBLISHED');
            },
        ])->get()->map(function ($course) use ($user) {
            $attemptedCount = $user->uniqueQuestionsAttemptedCount($course->id);
            $correctCount = $user->correctAttemptsCount($course->id);
            $accuracy = $attemptedCount > 0 ? round(($correctCount / $attemptedCount) * 100, 1) : 0;
            $limit = $course->free_question_limit ?? 500;
            $quotaRemaining = max(0, $limit - $attemptedCount);
            $quotaPercentage = $limit > 0 ? min(100, round(($attemptedCount / $limit) * 100)) : 0;

            return [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'total_questions' => $course->questions_count,
                'attempted_count' => $attemptedCount,
                'correct_count' => $correctCount,
                'accuracy' => $accuracy,
                'free_limit' => $limit,
                'quota_remaining' => $quotaRemaining,
                'quota_percentage' => $quotaPercentage,
                'is_subscribed' => $user->hasActiveSubscription($course->id),
                'is_quota_exhausted' => $user->hasExceededFreeQuota($course->id),
            ];
        });

        // Global stats
        $totalAttempted = $user->uniqueQuestionsAttemptedCount();
        $totalCorrect = $user->correctAttemptsCount();
        $overallAccuracy = $totalAttempted > 0 ? round(($totalCorrect / $totalAttempted) * 100, 1) : 0;
        $totalBookmarks = $user->bookmarks()->count();

        // Recent attempts
        $recentAttempts = PracticeAttempt::where('user_id', $user->id)
            ->with(['question', 'course', 'selectedOption'])
            ->latest('last_attempted_at')
            ->take(5)
            ->get();

        // All available active courses for selection
        $allCourses = Course::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.student.dashboard', [
            'enrolledCourses' => $enrolledCourses,
            'totalAttempted' => $totalAttempted,
            'totalCorrect' => $totalCorrect,
            'overallAccuracy' => $overallAccuracy,
            'totalBookmarks' => $totalBookmarks,
            'recentAttempts' => $recentAttempts,
            'allCourses' => $allCourses,
        ])->layout('layouts.app');
    }
}
