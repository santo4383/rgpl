<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Models\Course;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?Category $category = null;

    public $course_id = '';

    public $name = '';

    public $description = '';

    public $is_active = true;

    public $sort_order = 0;

    protected $rules = [
        'course_id' => 'required|exists:courses,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function mount(Category $category)
    {
        if ($category->exists) {
            $this->category = $category;
            $this->course_id = $category->course_id;
            $this->name = $category->name;
            $this->description = $category->description;
            $this->is_active = $category->is_active;
            $this->sort_order = $category->sort_order;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->category && $this->category->exists) {
            $this->category->update($this->only(['course_id', 'name', 'description', 'is_active', 'sort_order']));
        } else {
            Category::create($this->only(['course_id', 'name', 'description', 'is_active', 'sort_order']));
        }

        session()->flash('message', 'Category saved successfully.');

        return redirect()->route('admin.categories.index');
    }

    public function render()
    {
        return view('livewire.admin.categories.category-form', [
            'courses' => Course::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
