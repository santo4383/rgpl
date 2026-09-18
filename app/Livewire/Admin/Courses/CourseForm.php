<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Component;

class CourseForm extends Component
{
    public ?Course $course = null;

    public $name = '';

    public $description = '';

    public $is_active = true;

    public $sort_order = 0;

    public $free_question_limit = 500;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'free_question_limit' => 'integer|min:0',
    ];

    public function mount(Course $course)
    {
        if ($course->exists) {
            $this->course = $course;
            $this->name = $course->name;
            $this->description = $course->description;
            $this->is_active = $course->is_active;
            $this->sort_order = $course->sort_order;
            $this->free_question_limit = $course->free_question_limit;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->course && $this->course->exists) {
            $this->course->update($this->only(['name', 'description', 'is_active', 'sort_order', 'free_question_limit']));
        } else {
            Course::create($this->only(['name', 'description', 'is_active', 'sort_order', 'free_question_limit']));
        }

        session()->flash('message', 'Course saved successfully.');

        return redirect()->route('admin.courses.index');
    }

    public function render()
    {
        return view('livewire.admin.courses.course-form')->layout('layouts.app');
    }
}
