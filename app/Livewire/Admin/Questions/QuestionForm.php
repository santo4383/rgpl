<?php

namespace App\Livewire\Admin\Questions;

use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\Subcategory;
use Illuminate\Support\Str;
use Livewire\Component;

class QuestionForm extends Component
{
    public ?Question $question = null;

    public $course_id = '';

    public $category_id = '';

    public $subcategory_id = '';

    public $question_html = '';

    public $explanation_html = '';

    public $difficulty = 'Medium';

    public $status = 'DRAFT';

    public $is_free = true;

    public $options = [
        ['uid' => 'opt_1', 'option_key' => 'A', 'option_html' => '', 'is_correct' => false],
        ['uid' => 'opt_2', 'option_key' => 'B', 'option_html' => '', 'is_correct' => false],
        ['uid' => 'opt_3', 'option_key' => 'C', 'option_html' => '', 'is_correct' => false],
        ['uid' => 'opt_4', 'option_key' => 'D', 'option_html' => '', 'is_correct' => false],
    ];

    protected $rules = [
        'course_id' => 'required|exists:courses,id',
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'nullable|exists:subcategories,id',
        'question_html' => 'required|string',
        'explanation_html' => 'nullable|string',
        'difficulty' => 'required|string',
        'status' => 'required|string',
        'is_free' => 'boolean',
        'options.*.option_html' => 'required|string',
        'options.*.is_correct' => 'boolean',
    ];

    public function mount(Question $question)
    {
        if ($question->exists) {
            $this->question = $question;
            $this->course_id = $question->course_id;
            $this->category_id = $question->category_id;
            $this->subcategory_id = $question->subcategory_id;
            $this->question_html = $question->question_html;
            $this->explanation_html = $question->explanation_html;
            $this->difficulty = $question->difficulty;
            $this->status = $question->status;
            $this->is_free = $question->is_free;

            $this->options = $question->options->map(function ($opt) {
                return [
                    'uid' => 'opt_'.$opt->id,
                    'id' => $opt->id,
                    'option_key' => $opt->option_key,
                    'option_html' => $opt->option_html,
                    'is_correct' => (bool) $opt->is_correct,
                ];
            })->toArray();
        }
    }

    public function updatedCourseId($courseId)
    {
        $this->category_id = '';
        $this->subcategory_id = '';
    }

    public function updatedCategoryId($categoryId)
    {
        $this->subcategory_id = '';
    }

    public function addOption()
    {
        $count = count($this->options);
        $nextKey = $count > 0 ? chr(ord($this->options[$count - 1]['option_key']) + 1) : 'A';
        $this->options[] = [
            'uid' => 'opt_'.uniqid(),
            'option_key' => $nextKey,
            'option_html' => '',
            'is_correct' => false,
        ];
    }

    public function removeOption($index)
    {
        if (count($this->options) > 2) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
            // Re-key option labels (A, B, C...)
            foreach ($this->options as $i => &$opt) {
                $opt['option_key'] = chr(65 + $i);
            }
        }
    }

    public function toggleCorrectOption($index)
    {
        if (isset($this->options[$index])) {
            $this->options[$index]['is_correct'] = (bool) ($this->options[$index]['is_correct'] ?? false);
        }
    }

    public function getCalculatedQuestionDetails()
    {
        if (! $this->course_id) {
            return [
                'number' => null,
                'code' => 'N/A',
                'total_course_questions' => 0,
            ];
        }

        $totalCourseQuestions = Question::where('course_id', $this->course_id)->count();

        // If editing existing question and course/category/sub haven't changed, retain original question_number & question_code
        if (
            $this->question && $this->question->exists &&
            $this->question->course_id == $this->course_id &&
            $this->question->category_id == $this->category_id &&
            $this->question->subcategory_id == $this->subcategory_id &&
            $this->question->question_code
        ) {
            return [
                'number' => $this->question->question_number,
                'code' => $this->question->question_code,
                'total_course_questions' => $totalCourseQuestions,
            ];
        }

        $query = Question::where('course_id', $this->course_id);
        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }
        if ($this->subcategory_id) {
            $query->where('subcategory_id', $this->subcategory_id);
        }

        $nextNumber = ((int) $query->max('question_number')) + 1;

        $course = Course::find($this->course_id);
        $category = $this->category_id ? Category::find($this->category_id) : null;
        $subcategory = $this->subcategory_id ? Subcategory::find($this->subcategory_id) : null;

        $parts = [];
        if ($course) {
            $parts[] = Str::upper(Str::slug(Str::limit($course->name, 10, '')));
        }
        if ($category) {
            $parts[] = Str::upper(Str::slug(Str::limit($category->name, 10, '')));
        }
        if ($subcategory) {
            $parts[] = Str::upper(Str::slug(Str::limit($subcategory->name, 10, '')));
        }
        $parts[] = sprintf('%04d', $nextNumber);

        $code = implode('-', array_filter($parts));

        return [
            'number' => $nextNumber,
            'code' => $code,
            'total_course_questions' => $totalCourseQuestions,
        ];
    }

    public function save()
    {
        $this->validate();

        $hasCorrect = false;
        foreach ($this->options as $opt) {
            if (! empty($opt['is_correct'])) {
                $hasCorrect = true;
                break;
            }
        }
        if (! $hasCorrect) {
            $this->addError('options', 'At least one option must be marked as correct.');

            return;
        }

        $questionText = strip_tags($this->question_html);
        $explanationText = strip_tags($this->explanation_html);
        $details = $this->getCalculatedQuestionDetails();

        $data = [
            'course_id' => $this->course_id,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id ?: null,
            'question_number' => $details['number'],
            'question_code' => $details['code'],
            'question_html' => $this->question_html,
            'question_text' => $questionText,
            'explanation_html' => $this->explanation_html,
            'explanation_text' => $explanationText,
            'difficulty' => $this->difficulty,
            'status' => $this->status,
            'is_free' => $this->is_free,
        ];

        if ($this->question && $this->question->exists) {
            $data['updated_by'] = auth()->id();
            $this->question->update($data);
        } else {
            $data['created_by'] = auth()->id();
            $this->question = Question::create($data);
        }

        // Sync options
        $this->question->options()->delete();
        foreach ($this->options as $idx => $opt) {
            $this->question->options()->create([
                'option_key' => $opt['option_key'],
                'option_html' => $opt['option_html'],
                'option_text' => strip_tags($opt['option_html']),
                'is_correct' => ! empty($opt['is_correct']),
                'sort_order' => $idx,
            ]);
        }

        session()->flash('message', 'Question saved successfully.');

        return redirect()->route('admin.questions.index');
    }

    public function render()
    {
        $questionDetails = $this->getCalculatedQuestionDetails();

        return view('livewire.admin.questions.question-form', [
            'available_courses' => Course::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'available_categories' => $this->course_id ? Category::where('course_id', $this->course_id)->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() : [],
            'available_subcategories' => $this->category_id ? Subcategory::where('category_id', $this->category_id)->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() : [],
            'questionDetails' => $questionDetails,
        ])->layout('layouts.app');
    }
}
