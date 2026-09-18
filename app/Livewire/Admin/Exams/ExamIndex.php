<?php

namespace App\Livewire\Admin\Exams;

use App\Models\Course;
use App\Models\Exam;
use Livewire\Component;
use Livewire\WithPagination;

class ExamIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $courseFilter = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCourseFilter()
    {
        $this->resetPage();
    }

    public function togglePublish(Exam $exam)
    {
        $exam->update([
            'is_published' => ! $exam->is_published,
        ]);

        session()->flash('message', 'Exam publication status updated.');
    }

    public function delete(Exam $exam)
    {
        $exam->delete();
        session()->flash('message', 'Exam deleted successfully.');
    }

    public function render()
    {
        $courses = Course::orderBy('name')->get();

        $query = Exam::with(['course', 'category', 'examQuestions'])
            ->withCount(['questions', 'attempts']);

        if (! empty($this->search)) {
            $query->where('title', 'ilike', '%'.$this->search.'%');
        }

        if (! empty($this->courseFilter)) {
            $query->where('course_id', $this->courseFilter);
        }

        $exams = $query->latest()->paginate(10);

        return view('livewire.admin.exams.exam-index', [
            'exams' => $exams,
            'courses' => $courses,
        ])->layout('layouts.app');
    }
}
