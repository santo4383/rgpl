<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    public function delete(Category $category)
    {
        $category->delete();
        session()->flash('message', 'Category deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.categories.category-index', [
            'categories' => Category::with('course')->paginate(10),
        ])->layout('layouts.app');
    }
}
