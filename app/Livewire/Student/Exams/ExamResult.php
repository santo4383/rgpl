<?php

namespace App\Livewire\Student\Exams;

use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ExamResult extends Component
{
    public ExamAttempt $attempt;

    public Exam $exam;

    public string $filter = 'all'; // 'all', 'correct', 'incorrect', 'unanswered'

    public function mount(ExamAttempt $attempt)
    {
        $user = Auth::user();

        // Allow owner or super admin
        if ($attempt->user_id !== $user->id && ! $user->hasRole('SUPER_ADMIN')) {
            abort(403, 'Unauthorized access to this result scorecard.');
        }

        // If attempt is still in progress, grade it if expired or redirect to take
        if ($attempt->status === 'IN_PROGRESS') {
            if ($attempt->isExpired()) {
                $attempt->gradeAndSubmit(timedOut: true);
            } else {
                return $this->redirect(route('student.exams.take', $attempt->id), navigate: true);
            }
        }

        $this->attempt = $attempt;
        $this->exam = $attempt->exam;
    }

    public function setFilter(string $filter)
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $answersQuery = $this->attempt->answers()
            ->with(['question.options', 'question.images', 'question.category', 'selectedOption']);

        if ($this->filter === 'correct') {
            $answersQuery->where('is_correct', true);
        } elseif ($this->filter === 'incorrect') {
            $answersQuery->where('is_answered', true)->where('is_correct', false);
        } elseif ($this->filter === 'unanswered') {
            $answersQuery->where('is_answered', false);
        }

        $answers = $answersQuery->get();

        // Calculate accuracy
        $attempted = $this->attempt->attempted_questions;
        $correct = $this->attempt->correct_answers;
        $accuracy = $attempted > 0 ? round(($correct / $attempted) * 100, 1) : 0;

        return view('livewire.student.exams.exam-result', [
            'answers' => $answers,
            'accuracy' => $accuracy,
        ])->layout('layouts.app');
    }
}
