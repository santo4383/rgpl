<?php

namespace App\Livewire\Admin\Plans;

use App\Models\Course;
use App\Models\SubscriptionPlan;
use Livewire\Component;

class PlanForm extends Component
{
    public ?SubscriptionPlan $plan = null;

    public $name = '';

    public $description = '';

    public $price = 0.00;

    public $duration_days = 30;

    public $is_all_courses = false;

    public $selectedCourseIds = [];

    public $is_active = true;

    public $sort_order = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'duration_days' => 'required|integer|min:1',
        'is_all_courses' => 'boolean',
        'selectedCourseIds' => 'nullable|array',
        'selectedCourseIds.*' => 'exists:courses,id',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function mount(SubscriptionPlan $plan)
    {
        if ($plan->exists) {
            $this->plan = $plan;
            $this->name = $plan->name;
            $this->description = $plan->description;
            $this->price = $plan->price;
            $this->duration_days = $plan->duration_days;
            $this->is_all_courses = $plan->is_all_courses;
            $this->selectedCourseIds = $plan->courses()->pluck('courses.id')->toArray();
            $this->is_active = $plan->is_active;
            $this->sort_order = $plan->sort_order;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'is_all_courses' => $this->is_all_courses,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        if ($this->plan && $this->plan->exists) {
            $this->plan->update($data);
        } else {
            $this->plan = SubscriptionPlan::create($data);
        }

        // Sync linked courses unless it's an all-courses plan
        if (! $this->is_all_courses) {
            $this->plan->courses()->sync($this->selectedCourseIds);
        } else {
            $this->plan->courses()->detach();
        }

        session()->flash('message', 'Subscription plan saved successfully.');

        return redirect()->route('admin.plans.index');
    }

    public function render()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.plans.plan-form', [
            'courses' => $courses,
        ])->layout('layouts.app');
    }
}
