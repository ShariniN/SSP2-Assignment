<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class SearchComponent extends Component
{
    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        $this->results = Product::where('name', 'like', "%{$this->query}%")
            ->orWhere('description', 'like', "%{$this->query}%")
            ->take(10)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.search-component');
    }
}
