<?php

namespace App\Livewire;

use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Categories extends Component
{
    public $editing = false;
    public $name = '';
    public $parent = null;

    public array $stateCategories = [];

    public function mount(): void
    {
        $this->stateCategories = Category::whereNotNull('parent_id') // only modify children
            ->where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function updatedStateCategories($value, $id)
    {
        Category::find($id)->update([
            'name' => $value
        ]);

        Flux::toast(variant: 'success', text: 'Your changes have been saved.');
    }

    public function render()
    {
        return view('livewire.categories', [
            'categories' => Category::whereNull('parent_id')
                ->where('is_active', true)
                ->with('children')->get(),
        ]);
    }

    #[Computed]
    public function rootCategories()
    {
        return Category::whereNull('parent_id')->where('is_active', true)->get();
    }

    public function save()
    {
        Category::create([
            'parent_id' => $this->parent ? Category::find($this->parent)->id : null,
            'name' => $this->name,
            'is_active' => true,
        ]);

        $this->clear();
        Flux::toast(variant: 'success', text: 'Your changes have been saved.');
    }

    public function clear()
    {
        $this->resetValidation();
        $this->reset('editing', 'name', 'parent');
    }
}
