<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Categories Page - Ecommerce')]
class CategoriesPage extends Component
{
    public function render(
    ): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        $categories = Category::where('is_active', 1)->get();
        return view('livewire.categories-page', ['categories' => $categories]);
    }
}
