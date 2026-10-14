<?php

namespace App\Livewire\Admin\Questions;

use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\Subcategory;
use App\Services\Security\QuestionVaultService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $course_id = '';

    public string $category_id = '';

    public string $subcategory_id = '';

    public string $difficulty = '';

    public string $status = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'course_id' => ['except' => ''],
        'category_id' => ['except' => ''],
        'subcategory_id' => ['except' => ''],
        'difficulty' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCourseId(): void
    {
        $this->category_id = '';
        $this->subcategory_id = '';
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->subcategory_id = '';
        $this->resetPage();
    }

    public function updatedSubcategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedDifficulty(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->course_id = '';
        $this->category_id = '';
        $this->subcategory_id = '';
        $this->difficulty = '';
        $this->status = '';
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->course_id !== ''
            || $this->category_id !== ''
            || $this->subcategory_id !== ''
            || $this->difficulty !== ''
            || $this->status !== '';
    }

    public function delete(Question $question): void
    {
        $question->delete();
        session()->flash('message', 'Question deleted successfully.');
    }

    public function render(): View
    {
        $courses = Course::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categories = $this->course_id
            ? Category::where('course_id', $this->course_id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
            : collect();

        $subcategories = $this->category_id
            ? Subcategory::where('category_id', $this->category_id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
            : collect();

        $query = Question::query()->with(['course', 'category', 'subcategory']);

        if ($this->search !== '') {
            $search = trim($this->search);

            if (app(QuestionVaultService::class)->isEnabled()) {
                $candidateQuery = Question::query();
                if ($this->course_id !== '') {
                    $candidateQuery->where('course_id', $this->course_id);
                }
                if ($this->category_id !== '') {
                    $candidateQuery->where('category_id', $this->category_id);
                }
                if ($this->subcategory_id !== '') {
                    $candidateQuery->where('subcategory_id', $this->subcategory_id);
                }
                if ($this->difficulty !== '') {
                    $candidateQuery->where('difficulty', $this->difficulty);
                }
                if ($this->status !== '') {
                    $candidateQuery->where('status', $this->status);
                }

                $matchingIds = $candidateQuery->get(['id', 'question_code', 'question_text', 'question_html'])
                    ->filter(function ($q) use ($search) {
                        return str_contains(mb_strtolower($q->question_code ?? ''), mb_strtolower($search))
                            || str_contains(mb_strtolower($q->question_text ?? ''), mb_strtolower($search))
                            || str_contains(mb_strtolower($q->question_html ?? ''), mb_strtolower($search));
                    })
                    ->pluck('id');

                $query->whereIn('id', $matchingIds);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('question_text', 'like', "%{$search}%")
                        ->orWhere('question_html', 'like', "%{$search}%")
                        ->orWhere('question_code', 'like', "%{$search}%");
                });
            }
        }

        if ($this->course_id !== '') {
            $query->where('course_id', $this->course_id);
        }

        if ($this->category_id !== '') {
            $query->where('category_id', $this->category_id);
        }

        if ($this->subcategory_id !== '') {
            $query->where('subcategory_id', $this->subcategory_id);
        }

        if ($this->difficulty !== '') {
            $query->where('difficulty', $this->difficulty);
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        $questions = $query->latest()->paginate(15);

        return view('livewire.admin.questions.question-index', [
            'questions' => $questions,
            'courses' => $courses,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'totalFilteredCount' => $questions->total(),
            'hasActiveFilters' => $this->hasActiveFilters(),
        ])->layout('layouts.app');
    }
}
