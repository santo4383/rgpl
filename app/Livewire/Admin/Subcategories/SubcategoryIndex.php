<?php

namespace App\Livewire\Admin\Subcategories;

use App\Models\Subcategory;
use Livewire\Component;
use Livewire\WithPagination;

class SubcategoryIndex extends Component
{
    use WithPagination;

    public function delete(Subcategory $subcategory)
    {
        $subcategory->delete();
        session()->flash('message', 'Subcategory deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.subcategories.subcategory-index', [
            'subcategories' => Subcategory::with('category.course')->paginate(10),
        ])->layout('layouts.app');
    }
}
