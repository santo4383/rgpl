<?php

namespace App\Livewire\Admin\Subcategories;

use App\Models\Category;
use App\Models\Subcategory;
use Livewire\Component;

class SubcategoryForm extends Component
{
    public ?Subcategory $subcategory = null;

    public $category_id = '';

    public $name = '';

    public $description = '';

    public $is_active = true;

    public $sort_order = 0;

    protected $rules = [
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function mount(Subcategory $subcategory)
    {
        if ($subcategory->exists) {
            $this->subcategory = $subcategory;
            $this->category_id = $subcategory->category_id;
            $this->name = $subcategory->name;
            $this->description = $subcategory->description;
            $this->is_active = $subcategory->is_active;
            $this->sort_order = $subcategory->sort_order;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->subcategory && $this->subcategory->exists) {
            $this->subcategory->update($this->only(['category_id', 'name', 'description', 'is_active', 'sort_order']));
        } else {
            Subcategory::create($this->only(['category_id', 'name', 'description', 'is_active', 'sort_order']));
        }

        session()->flash('message', 'Subcategory saved successfully.');

        return redirect()->route('admin.subcategories.index');
    }

    public function render()
    {
        return view('livewire.admin.subcategories.subcategory-form', [
            'categories' => Category::with('course')->where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
