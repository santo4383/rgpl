<?php

namespace App\Livewire\Admin\Exams;

use App\Models\Category;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ExamForm extends Component
{
    public ?Exam $exam = null;

    public $course_id = '';

    public $category_id = '';

    public $title = '';

    public $description = '';

    public $instructions = '';

    public $duration_minutes = 60;

    public $total_marks = 100.00;

    public $pass_percentage = 40.00;

    public bool $has_negative_marking = false;

    public $default_negative_marks = 0.25;

    public bool $is_published = false;

    public bool $is_premium = false;

    public function mount(?Exam $exam = null)
    {
        if ($exam && $exam->exists) {
            $this->exam = $exam;
            $this->course_id = $exam->course_id;
            $this->category_id = $exam->category_id;
            $this->title = $exam->title;
            $this->description = $exam->description ?? '';
            $this->instructions = $exam->instructions ?? '';
            $this->duration_minutes = $exam->duration_minutes;
            $this->total_marks = $exam->total_marks;
            $this->pass_percentage = $exam->pass_percentage;
            $this->has_negative_marking = (bool) $exam->has_negative_marking;
            $this->default_negative_marks = $exam->default_negative_marks;
            $this->is_published = (bool) $exam->is_published;
            $this->is_premium = (bool) $exam->is_premium;
        } else {
            $defaultCourse = Course::where('is_active', true)->orderBy('sort_order')->first();
            if ($defaultCourse) {
                $this->course_id = $defaultCourse->id;
            }
            $this->instructions = "1. This exam consists of multiple-choice questions.\n2. Each question carries marks as indicated.\n3. Read each question carefully before answering.\n4. You can navigate between questions using the Question Palette.\n5. Click 'Submit Exam' once you have reviewed all answers.";
        }
    }

    public function updatedCourseId()
    {
        $this->category_id = '';
    }

    protected function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'total_marks' => 'required|numeric|min:1|max:10000',
            'pass_percentage' => 'required|numeric|min:1|max:100',
            'has_negative_marking' => 'boolean',
            'default_negative_marks' => 'nullable|numeric|min:0',
            'is_published' => 'boolean',
            'is_premium' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        // Ensure total marks cannot be less than currently assigned marks
        if ($this->exam && $this->exam->exists) {
            $assigned = $this->exam->assignedMarksTotal();
            if ((float) $this->total_marks < ($assigned - 0.001)) {
                $this->addError('total_marks', "Total marks cannot be less than the marks already assigned to questions ({$assigned} marks).");

                return;
            }
        }

        $data = [
            'course_id' => $this->course_id,
            'category_id' => $this->category_id ?: null,
            'title' => $this->title,
            'description' => $this->description,
            'instructions' => $this->instructions,
            'duration_minutes' => $this->duration_minutes,
            'total_marks' => $this->total_marks,
            'pass_percentage' => $this->pass_percentage,
            'has_negative_marking' => $this->has_negative_marking,
            'default_negative_marks' => $this->has_negative_marking ? ($this->default_negative_marks ?: 0.0) : 0.0,
            'is_published' => $this->is_published,
            'is_premium' => $this->is_premium,
        ];

        if ($this->exam && $this->exam->exists) {
            $this->exam->update($data);
            session()->flash('message', 'Exam updated successfully.');
        } else {
            $data['slug'] = Str::slug($this->title).'-'.Str::random(5);
            $data['created_by'] = Auth::id();
            $this->exam = Exam::create($data);
            session()->flash('message', 'Exam created successfully! Now assign questions below.');

            return $this->redirect(route('admin.exams.questions', $this->exam->id), navigate: true);
        }

        return $this->redirect(route('admin.exams.index'), navigate: true);
    }

    public function render()
    {
        $courses = Course::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $categories = $this->course_id ? Category::where('course_id', $this->course_id)->where('is_active', true)->orderBy('sort_order')->get() : [];

        return view('livewire.admin.exams.exam-form', [
            'courses' => $courses,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
