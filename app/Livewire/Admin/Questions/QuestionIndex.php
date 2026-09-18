<?php

namespace App\Livewire\Admin\Questions;

use App\Models\Question;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function delete(Question $question)
    {
        $question->delete();
        session()->flash('message', 'Question deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.questions.question-index', [
            'questions' => Question::where('question_text', 'like', "%{$this->search}%")
                ->orWhere('question_html', 'like', "%{$this->search}%")
                ->with(['course', 'category', 'subcategory'])
                ->paginate(15),
        ])->layout('layouts.app');
    }
}
