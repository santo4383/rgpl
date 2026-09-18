<?php

namespace App\Livewire\Admin\Exams;

use App\Models\Category;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Question;
use App\Models\Subcategory;
use Livewire\Component;
use Livewire\WithPagination;

class ExamQuestions extends Component
{
    use WithPagination;

    public Exam $exam;

    // Filters for question bank
    public string $search = '';

    public string $categoryFilter = '';

    public string $subcategoryFilter = '';

    // Per-question inputs for adding
    public array $addMarks = [];

    public array $addNegMarks = [];

    // Per-question inputs for editing existing
    public array $editMarks = [];

    public array $editNegMarks = [];

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
        $this->loadAssignedQuestionsData();
    }

    public function loadAssignedQuestionsData()
    {
        foreach ($this->exam->examQuestions as $eq) {
            $this->editMarks[$eq->id] = (float) $eq->marks;
            $this->editNegMarks[$eq->id] = (float) $eq->negative_marks;
        }
    }

    public function updatedCategoryFilter()
    {
        $this->subcategoryFilter = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function addQuestion($questionId)
    {
        $this->resetErrorBag();

        $marks = isset($this->addMarks[$questionId]) && is_numeric($this->addMarks[$questionId])
            ? (float) $this->addMarks[$questionId]
            : 1.0;

        $negMarks = isset($this->addNegMarks[$questionId]) && is_numeric($this->addNegMarks[$questionId])
            ? (float) $this->addNegMarks[$questionId]
            : ($this->exam->has_negative_marking ? (float) $this->exam->default_negative_marks : 0.0);

        if ($marks <= 0) {
            $this->addError('allocation', 'Question marks must be greater than 0.');

            return;
        }

        // Validate Total Marks Constraint
        $currentAssigned = $this->exam->assignedMarksTotal();
        $projectedTotal = $currentAssigned + $marks;
        $maxAllowed = (float) $this->exam->total_marks;

        if ($projectedTotal > ($maxAllowed + 0.001)) {
            $remaining = max(0, $maxAllowed - $currentAssigned);
            $this->addError('allocation', "Cannot add question: Assigned marks ({$projectedTotal}) would exceed the Exam Total Marks limit ({$maxAllowed}). Only {$remaining} marks remaining.");

            return;
        }

        // Check if already assigned
        if ($this->exam->examQuestions()->where('question_id', $questionId)->exists()) {
            $this->addError('allocation', 'This question is already assigned to this exam.');

            return;
        }

        $maxSort = (int) $this->exam->examQuestions()->max('sort_order');

        ExamQuestion::create([
            'exam_id' => $this->exam->id,
            'question_id' => $questionId,
            'marks' => $marks,
            'negative_marks' => $this->exam->has_negative_marking ? $negMarks : 0.0,
            'sort_order' => $maxSort + 1,
        ]);

        $this->exam->load('examQuestions');
        $this->loadAssignedQuestionsData();

        session()->flash('message', "Question added successfully (+{$marks} marks).");
    }

    public function updateMarks($examQuestionId)
    {
        $this->resetErrorBag();

        $newMarks = isset($this->editMarks[$examQuestionId]) && is_numeric($this->editMarks[$examQuestionId])
            ? (float) $this->editMarks[$examQuestionId]
            : 1.0;

        $newNegMarks = isset($this->editNegMarks[$examQuestionId]) && is_numeric($this->editNegMarks[$examQuestionId])
            ? (float) $this->editNegMarks[$examQuestionId]
            : 0.0;

        if ($newMarks <= 0) {
            $this->addError("marks_{$examQuestionId}", 'Marks must be greater than 0.');

            return;
        }

        $otherMarks = (float) $this->exam->examQuestions()->where('id', '!=', $examQuestionId)->sum('marks');
        $projectedTotal = $otherMarks + $newMarks;
        $maxAllowed = (float) $this->exam->total_marks;

        if ($projectedTotal > ($maxAllowed + 0.001)) {
            $maxForThis = max(0, $maxAllowed - $otherMarks);
            $this->addError("marks_{$examQuestionId}", "Cannot update marks: New total ({$projectedTotal}) would exceed the Exam Total Marks ({$maxAllowed}). Maximum you can set is {$maxForThis}.");

            return;
        }

        $eq = ExamQuestion::findOrFail($examQuestionId);
        $eq->update([
            'marks' => $newMarks,
            'negative_marks' => $this->exam->has_negative_marking ? $newNegMarks : 0.0,
        ]);

        $this->exam->load('examQuestions');
        $this->loadAssignedQuestionsData();

        session()->flash('message', 'Question marks updated successfully.');
    }

    public function removeQuestion($examQuestionId)
    {
        $eq = ExamQuestion::where('exam_id', $this->exam->id)->where('id', $examQuestionId)->first();
        if ($eq) {
            $eq->delete();
            $this->exam->load('examQuestions');
            $this->loadAssignedQuestionsData();
            session()->flash('message', 'Question removed from exam.');
        }
    }

    public function render()
    {
        $categories = Category::where('course_id', $this->exam->course_id)->where('is_active', true)->orderBy('name')->get();
        $subcategories = $this->categoryFilter ? Subcategory::where('category_id', $this->categoryFilter)->where('is_active', true)->orderBy('name')->get() : [];

        // Already assigned question IDs
        $assignedQuestionIds = $this->exam->examQuestions()->pluck('question_id')->toArray();

        // Available published questions from this course
        $availableQuery = Question::where('course_id', $this->exam->course_id)
            ->where('status', 'PUBLISHED')
            ->whereNotIn('id', $assignedQuestionIds)
            ->with(['category', 'subcategory', 'options']);

        if (! empty($this->search)) {
            $availableQuery->where('question_text', 'ilike', '%'.$this->search.'%');
        }

        if (! empty($this->categoryFilter)) {
            $availableQuery->where('category_id', $this->categoryFilter);
        }

        if (! empty($this->subcategoryFilter)) {
            $availableQuery->where('subcategory_id', $this->subcategoryFilter);
        }

        $availableQuestions = $availableQuery->latest()->paginate(10);

        // Assigned questions with full details
        $assignedQuestions = $this->exam->examQuestions()
            ->with(['question.category', 'question.subcategory', 'question.options'])
            ->get();

        $assignedMarks = $this->exam->assignedMarksTotal();
        $remainingMarks = $this->exam->remainingMarks();

        return view('livewire.admin.exams.exam-questions', [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'availableQuestions' => $availableQuestions,
            'assignedQuestions' => $assignedQuestions,
            'assignedMarks' => $assignedMarks,
            'remainingMarks' => $remainingMarks,
        ])->layout('layouts.app');
    }
}
