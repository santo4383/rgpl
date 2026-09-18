<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class CourseIndex extends Component
{
    use WithPagination;

    public function delete(Course $course)
    {
        $course->delete();
        session()->flash('message', 'Course deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.courses.course-index', [
            'courses' => Course::paginate(10),
        ])->layout('layouts.app');
    }
}
