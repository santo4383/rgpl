<?php

namespace App\Livewire\Student\Exams;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ExamTake extends Component
{
    public ExamAttempt $attempt;

    public Exam $exam;

    public int $currentIndex = 0;

    public array $questionIds = [];

    public array $userAnswers = []; // question_id => selected_option_id

    public array $reviewFlags = []; // question_id => bool

    public array $visited = [];     // question_id => bool

    public int $remainingSeconds = 0;

    public bool $showSubmitModal = false;

    public function mount(ExamAttempt $attempt)
    {
        // Authorization
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this exam attempt.');
        }

        // If attempt is already completed, redirect to result (Single Attempt Policy)
        if ($attempt->status === 'SUBMITTED' || $attempt->status === 'TIMED_OUT') {
            return $this->redirect(route('student.exams.result', $attempt->id), navigate: true);
        }

        $this->attempt = $attempt;
        $this->exam = $attempt->exam;

        // Check if expired on load
        if ($this->attempt->isExpired()) {
            $this->attempt->gradeAndSubmit(timedOut: true);

            return $this->redirect(route('student.exams.result', $attempt->id), navigate: true);
        }

        $this->remainingSeconds = $this->attempt->remainingSeconds();

        // Load questions in order
        $this->questionIds = $this->exam->questions()->pluck('questions.id')->toArray();

        // Load existing answers
        $dbAnswers = $this->attempt->answers()->get();
        foreach ($dbAnswers as $ans) {
            $this->userAnswers[$ans->question_id] = $ans->selected_option_id;
            $this->reviewFlags[$ans->question_id] = (bool) $ans->is_marked_for_review;
        }

        // Mark first question as visited
        if (! empty($this->questionIds)) {
            $this->visited[$this->questionIds[0]] = true;
        }
    }

    public function selectOption($optionId)
    {
        $currentQuestionId = $this->questionIds[$this->currentIndex] ?? null;
        if (! $currentQuestionId) {
            return;
        }

        $this->userAnswers[$currentQuestionId] = $optionId;

        ExamAnswer::where('exam_attempt_id', $this->attempt->id)
            ->where('question_id', $currentQuestionId)
            ->update([
                'selected_option_id' => $optionId,
                'is_answered' => true,
            ]);
    }

    public function clearResponse()
    {
        $currentQuestionId = $this->questionIds[$this->currentIndex] ?? null;
        if (! $currentQuestionId) {
            return;
        }

        $this->userAnswers[$currentQuestionId] = null;

        ExamAnswer::where('exam_attempt_id', $this->attempt->id)
            ->where('question_id', $currentQuestionId)
            ->update([
                'selected_option_id' => null,
                'is_answered' => false,
            ]);
    }

    public function toggleReview()
    {
        $currentQuestionId = $this->questionIds[$this->currentIndex] ?? null;
        if (! $currentQuestionId) {
            return;
        }

        $isCurrentlyReviewed = $this->reviewFlags[$currentQuestionId] ?? false;
        $this->reviewFlags[$currentQuestionId] = ! $isCurrentlyReviewed;

        ExamAnswer::where('exam_attempt_id', $this->attempt->id)
            ->where('question_id', $currentQuestionId)
            ->update([
                'is_marked_for_review' => $this->reviewFlags[$currentQuestionId],
            ]);
    }

    public function saveAndNext()
    {
        if ($this->currentIndex < count($this->questionIds) - 1) {
            $this->jumpToQuestion($this->currentIndex + 1);
        }
    }

    public function markForReviewAndNext()
    {
        $currentQuestionId = $this->questionIds[$this->currentIndex] ?? null;
        if ($currentQuestionId) {
            $this->reviewFlags[$currentQuestionId] = true;
            ExamAnswer::where('exam_attempt_id', $this->attempt->id)
                ->where('question_id', $currentQuestionId)
                ->update(['is_marked_for_review' => true]);
        }

        if ($this->currentIndex < count($this->questionIds) - 1) {
            $this->jumpToQuestion($this->currentIndex + 1);
        }
    }

    public function previousQuestion()
    {
        if ($this->currentIndex > 0) {
            $this->jumpToQuestion($this->currentIndex - 1);
        }
    }

    public function jumpToQuestion(int $index)
    {
        if (isset($this->questionIds[$index])) {
            $this->currentIndex = $index;
            $this->visited[$this->questionIds[$index]] = true;
        }
    }

    public function checkTimerExpired()
    {
        if ($this->attempt->isExpired()) {
            $this->submit(timedOut: true);
        }
    }

    public function openSubmitModal()
    {
        $this->showSubmitModal = true;
    }

    public function closeSubmitModal()
    {
        $this->showSubmitModal = false;
    }

    public function submit(bool $timedOut = false)
    {
        $this->attempt->gradeAndSubmit($timedOut);
        session()->flash('message', $timedOut ? 'Exam time expired. Your exam was auto-submitted.' : 'Exam submitted successfully! Review your scorecard below.');

        return $this->redirect(route('student.exams.result', $this->attempt->id), navigate: true);
    }

    public function render()
    {
        $currentQuestionId = $this->questionIds[$this->currentIndex] ?? null;
        $currentQuestion = $currentQuestionId ? Question::with(['options', 'images'])->find($currentQuestionId) : null;

        // Exam Question specific marks
        $examQuestion = $currentQuestionId ? $this->exam->examQuestions()->where('question_id', $currentQuestionId)->first() : null;
        $questionMarks = $examQuestion ? (float) $examQuestion->marks : 1.0;
        $questionNegMarks = $examQuestion ? (float) $examQuestion->negative_marks : 0.0;

        // Compute summary counts for palette & submit modal
        $totalCount = count($this->questionIds);
        $answeredCount = 0;
        $markedForReviewCount = 0;
        $answeredAndMarkedCount = 0;
        $notAnsweredCount = 0;
        $notVisitedCount = 0;

        $paletteStatus = [];
        foreach ($this->questionIds as $idx => $qId) {
            $hasAns = ! empty($this->userAnswers[$qId]);
            $isRev = ! empty($this->reviewFlags[$qId]);
            $isVis = ! empty($this->visited[$qId]);

            if ($hasAns && $isRev) {
                $status = 'answered_marked';
                $answeredAndMarkedCount++;
            } elseif ($hasAns) {
                $status = 'answered';
                $answeredCount++;
            } elseif ($isRev) {
                $status = 'marked';
                $markedForReviewCount++;
            } elseif ($isVis) {
                $status = 'not_answered';
                $notAnsweredCount++;
            } else {
                $status = 'not_visited';
                $notVisitedCount++;
            }

            $paletteStatus[$qId] = $status;
        }

        return view('livewire.student.exams.exam-take', [
            'currentQuestion' => $currentQuestion,
            'questionMarks' => $questionMarks,
            'questionNegMarks' => $questionNegMarks,
            'totalCount' => $totalCount,
            'answeredCount' => $answeredCount,
            'markedForReviewCount' => $markedForReviewCount,
            'answeredAndMarkedCount' => $answeredAndMarkedCount,
            'notAnsweredCount' => $notAnsweredCount,
            'notVisitedCount' => $notVisitedCount,
            'paletteStatus' => $paletteStatus,
        ])->layout('layouts.app');
    }
}
