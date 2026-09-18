<?php

namespace App\Livewire\Admin\Exams;

use App\Models\Exam;
use Livewire\Component;
use Livewire\WithPagination;

class ExamAttemptsList extends Component
{
    use WithPagination;

    public Exam $exam;

    public string $search = '';

    public string $statusFilter = '';

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->exam->attempts()->with('user');

        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'ilike', '%'.$this->search.'%')
                    ->orWhere('mobile_number', 'like', '%'.$this->search.'%');
            });
        }

        $attempts = $query->latest('started_at')->paginate(15);

        return view('livewire.admin.exams.exam-attempts-list', [
            'attempts' => $attempts,
        ])->layout('layouts.app');
    }
}
