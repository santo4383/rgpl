<?php

namespace App\Livewire\Student\Exams;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;

class ExamList extends Component
{
    public string $activeTab = 'exams'; // 'exams' or 'history'

    #[Url(as: 'course')]
    public $course_id = '';

    // Modal state for exam instructions
    public ?Exam $selectedExam = null;

    public bool $showInstructionModal = false;

    public bool $termsAccepted = false;

    public function mount()
    {
        $courseParam = request()->query('course');
        if ($courseParam) {
            $this->course_id = (string) $courseParam;
        } else {
            $this->course_id = '';
        }
    }

    public function openInstructions(Exam $exam)
    {
        $this->selectedExam = $exam;
        $this->termsAccepted = false;
        $this->showInstructionModal = true;
    }

    public function closeInstructions()
    {
        $this->selectedExam = null;
        $this->showInstructionModal = false;
        $this->termsAccepted = false;
    }

    public function startExam()
    {
        $user = Auth::user();
        $exam = $this->selectedExam;

        if (! $exam) {
            return;
        }

        // Check if student already has a completed attempt (Single attempt policy)
        $existingAttempt = $exam->userAttempt($user->id);
        if ($existingAttempt) {
            if ($existingAttempt->status === 'SUBMITTED' || $existingAttempt->status === 'TIMED_OUT') {
                $this->closeInstructions();
                session()->flash('message', 'You have already completed this exam.');

                return $this->redirect(route('student.exams.result', $existingAttempt->id), navigate: true);
            }

            // Resume in-progress attempt
            $this->closeInstructions();

            return $this->redirect(route('student.exams.take', $existingAttempt->id), navigate: true);
        }

        // Check Premium Access
        if ($exam->is_premium && ! $user->hasActiveSubscription($exam->course_id)) {
            $this->closeInstructions();
            session()->flash('error', 'This exam is reserved for Premium subscribers. Please upgrade your plan.');

            return $this->redirect(route('student.plans'), navigate: true);
        }

        // Create new attempt and populate answers in transaction
        $attempt = DB::transaction(function () use ($user, $exam) {
            $questions = $exam->questions;

            $attempt = ExamAttempt::create([
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'started_at' => now(),
                'status' => 'IN_PROGRESS',
                'total_questions' => $questions->count(),
                'total_marks' => $exam->total_marks,
            ]);

            foreach ($questions as $q) {
                ExamAnswer::create([
                    'exam_attempt_id' => $attempt->id,
                    'question_id' => $q->id,
                    'is_answered' => false,
                    'is_marked_for_review' => false,
                ]);
            }

            return $attempt;
        });

        $this->closeInstructions();

        return $this->redirect(route('student.exams.take', $attempt->id), navigate: true);
    }

    public function render()
    {
        $user = Auth::user();
        $enrolledCourses = $user->courses()->get();

        // Query available published exams
        $examsQuery = Exam::where('is_published', true)
            ->with(['course', 'category'])
            ->withCount('questions');

        if (! empty($this->course_id)) {
            $examsQuery->where('course_id', $this->course_id);
        } else {
            $enrolledCourseIds = $enrolledCourses->pluck('id')->toArray();
            if (! empty($enrolledCourseIds)) {
                $examsQuery->whereIn('course_id', $enrolledCourseIds);
            }
        }

        $exams = $examsQuery->latest()->get()->map(function ($exam) use ($user) {
            $attempt = $exam->userAttempt($user->id);
            $isSubscribed = $user->hasActiveSubscription($exam->course_id);

            return [
                'model' => $exam,
                'attempt' => $attempt,
                'is_completed' => $attempt && in_array($attempt->status, ['SUBMITTED', 'TIMED_OUT']),
                'is_in_progress' => $attempt && $attempt->status === 'IN_PROGRESS',
                'can_attempt' => ! $exam->is_premium || $isSubscribed,
                'is_subscribed' => $isSubscribed,
            ];
        });

        // User attempt history
        $attempts = $user->examAttempts()
            ->with(['exam.course'])
            ->latest('started_at')
            ->get();

        return view('livewire.student.exams.exam-list', [
            'enrolledCourses' => $enrolledCourses,
            'exams' => $exams,
            'attempts' => $attempts,
        ])->layout('layouts.app');
    }
}
