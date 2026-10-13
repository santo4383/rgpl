<?php

namespace App\Livewire\Student\Practice;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Course;
use App\Models\PracticeAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PracticeEngine extends Component
{
    public $course_id = '';

    public $category_id = '';

    public $subcategory_id = '';

    public $difficulty = 'all';

    public $status_filter = 'all'; // all, unattempted, incorrect, bookmarked

    public $questionIds = [];

    public $currentIndex = 0;

    public ?Question $currentQuestion = null;

    public $selectedOptionId = null;

    public $hasSubmitted = false;

    public $isCurrentCorrect = false;

    public $isBookmarked = false;

    public $quotaExceededAlert = false;

    public function mount($course = null)
    {
        $user = Auth::user();

        if ($course) {
            $courseModel = is_numeric($course) ? Course::find($course) : Course::where('id', $course)->first();
            if ($courseModel) {
                $this->course_id = $courseModel->id;
            }
        }

        // If no course selected, default to first enrolled course or first active course
        if (! $this->course_id) {
            $enrolled = $user->courses()->first();
            $this->course_id = $enrolled ? $enrolled->id : Course::where('is_active', true)->value('id');
        }

        $this->loadQuestionList();
    }

    public function updatedCourseId()
    {
        $this->category_id = '';
        $this->subcategory_id = '';
        $this->currentIndex = 0;
        $this->loadQuestionList();
    }

    public function updatedCategoryId()
    {
        $this->subcategory_id = '';
        $this->currentIndex = 0;
        $this->loadQuestionList();
    }

    public function updatedSubcategoryId()
    {
        $this->currentIndex = 0;
        $this->loadQuestionList();
    }

    public function updatedDifficulty()
    {
        $this->currentIndex = 0;
        $this->loadQuestionList();
    }

    public function updatedStatusFilter()
    {
        $this->currentIndex = 0;
        $this->loadQuestionList();
    }

    public function updatedCurrentIndex()
    {
        $this->loadCurrentQuestion();
    }

    public function loadQuestionList()
    {
        if (! $this->course_id) {
            $this->questionIds = [];
            $this->currentQuestion = null;

            return;
        }

        $user = Auth::user();
        $query = Question::where('course_id', $this->course_id)
            ->where('status', 'PUBLISHED');

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->subcategory_id) {
            $query->where('subcategory_id', $this->subcategory_id);
        }

        if ($this->difficulty && $this->difficulty !== 'all') {
            $query->where('difficulty', $this->difficulty);
        }

        if ($this->status_filter === 'unattempted') {
            $query->whereDoesntHave('practiceAttempts', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($this->status_filter === 'incorrect') {
            $query->whereHas('practiceAttempts', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('is_correct', false);
            });
        } elseif ($this->status_filter === 'bookmarked') {
            $query->whereHas('bookmarks', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $this->questionIds = $query->orderBy('id')->pluck('id')->toArray();
        $this->currentIndex = 0;
        $this->loadCurrentQuestion();
    }

    public function loadCurrentQuestion()
    {
        $this->selectedOptionId = null;
        $this->hasSubmitted = false;
        $this->quotaExceededAlert = false;

        if (empty($this->questionIds) || ! isset($this->questionIds[$this->currentIndex])) {
            $this->currentQuestion = null;

            return;
        }

        $questionId = $this->questionIds[$this->currentIndex];
        $this->currentQuestion = Question::with(['options' => function ($q) {
            $q->orderBy('sort_order')->orderBy('option_key');
        }, 'course', 'category', 'subcategory'])->find($questionId);

        if (! $this->currentQuestion) {
            return;
        }

        $user = Auth::user();

        // Check if question was already attempted
        $attempt = PracticeAttempt::where('user_id', $user->id)
            ->where('question_id', $this->currentQuestion->id)
            ->first();

        if ($attempt) {
            $this->selectedOptionId = $attempt->selected_option_id;
            $this->hasSubmitted = true;
            $this->isCurrentCorrect = $attempt->is_correct;
        }

        // Check bookmark status
        $this->isBookmarked = Bookmark::where('user_id', $user->id)
            ->where('question_id', $this->currentQuestion->id)
            ->exists();
    }

    public function selectOption($optionId)
    {
        if ($this->hasSubmitted) {
            return; // Already submitted for this attempt
        }

        $this->selectedOptionId = $optionId;
        $this->submitAnswer();
    }

    public function submitAnswer()
    {
        if (! $this->currentQuestion || ! $this->selectedOptionId) {
            return;
        }

        $user = Auth::user();

        // Check Free Quota if this is a brand new unique question attempt
        $existingAttempt = PracticeAttempt::where('user_id', $user->id)
            ->where('question_id', $this->currentQuestion->id)
            ->first();

        if (! $existingAttempt && $user->hasExceededFreeQuota($this->course_id)) {
            $this->quotaExceededAlert = true;
            $this->selectedOptionId = null;

            return;
        }

        $option = QuestionOption::find($this->selectedOptionId);
        $this->isCurrentCorrect = $option ? (bool) $option->is_correct : false;
        $this->hasSubmitted = true;

        if ($existingAttempt) {
            $existingAttempt->update([
                'selected_option_id' => $this->selectedOptionId,
                'is_correct' => $this->isCurrentCorrect,
                'attempt_count' => $existingAttempt->attempt_count + 1,
                'last_attempted_at' => now(),
            ]);
        } else {
            PracticeAttempt::create([
                'user_id' => $user->id,
                'course_id' => $this->currentQuestion->course_id,
                'question_id' => $this->currentQuestion->id,
                'selected_option_id' => $this->selectedOptionId,
                'is_correct' => $this->isCurrentCorrect,
                'attempt_count' => 1,
                'last_attempted_at' => now(),
            ]);
        }
    }

    public function nextQuestion()
    {
        if (! $this->hasSubmitted) {
            return;
        }

        if ($this->currentIndex < count($this->questionIds) - 1) {
            $this->currentIndex++;
            $this->loadCurrentQuestion();
        }
    }

    public function previousQuestion()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadCurrentQuestion();
        }
    }

    public function jumpToQuestion($index)
    {
        $index = (int) $index;

        // Prevent moving forward without submitting the current question
        if ($index > $this->currentIndex && ! $this->hasSubmitted) {
            return;
        }

        if (isset($this->questionIds[$index])) {
            $this->currentIndex = $index;
            $this->loadCurrentQuestion();
        }
    }

    public function updatingCurrentIndex($value)
    {
        if ((int) $value > $this->currentIndex && ! $this->hasSubmitted) {
            return false;
        }
    }

    public function toggleBookmark()
    {
        if (! $this->currentQuestion) {
            return;
        }

        $user = Auth::user();
        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('question_id', $this->currentQuestion->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $this->isBookmarked = false;
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'question_id' => $this->currentQuestion->id,
            ]);
            $this->isBookmarked = true;
        }
    }

    public function render()
    {
        $user = Auth::user();

        $courses = Course::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $categories = $this->course_id ? Category::where('course_id', $this->course_id)->where('is_active', true)->orderBy('sort_order')->get() : [];
        $subcategories = $this->category_id ? Subcategory::where('category_id', $this->category_id)->where('is_active', true)->orderBy('sort_order')->get() : [];

        $courseModel = $this->course_id ? Course::find($this->course_id) : null;
        $attemptedCount = $this->course_id ? $user->uniqueQuestionsAttemptedCount($this->course_id) : 0;
        $freeLimit = $courseModel ? ($courseModel->free_question_limit ?? 500) : 500;
        $quotaRemaining = max(0, $freeLimit - $attemptedCount);
        $isSubscribed = $this->course_id ? $user->hasActiveSubscription($this->course_id) : false;

        return view('livewire.student.practice.practice-engine', [
            'courses' => $courses,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'totalQuestions' => count($this->questionIds),
            'attemptedCount' => $attemptedCount,
            'freeLimit' => $freeLimit,
            'quotaRemaining' => $quotaRemaining,
            'isQuotaExhausted' => $this->course_id ? $user->hasExceededFreeQuota($this->course_id) : false,
            'isSubscribed' => $isSubscribed,
        ])->layout('layouts.app');
    }
}
