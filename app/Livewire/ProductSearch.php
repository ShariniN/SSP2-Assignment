<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Reactive;

class ProductSearch extends Component
{
    public $query = '';
    public $results = [];
    public $debug = [];

    public function mount()
    {
        $this->results = [];
        $this->debug = ['Component initialized'];
    }

    // This is the correct way for Livewire 3
    public function updated($property)
    {
        if ($property === 'query') {
            $this->search();
        }
    }

    public function search()
    {
        $this->debug = ['Searching for: "' . $this->query . '"'];

        // Reset if empty or too short
        if (empty($this->query) || strlen(trim($this->query)) < 2) {
            $this->results = [];
            $this->debug[] = 'Query too short';
            return;
        }

        try {
            $searchTerm = trim($this->query);
            
            $this->debug[] = 'Executing database query...';
            
            // Search products
            $products = Product::query()
                ->where('is_active', 1)
                ->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%');
                })
                ->limit(10)
                ->get(['id', 'name', 'sku', 'price', 'discount_price', 'image_url']);

            $this->debug[] = "DB returned: {$products->count()} products";

            // Convert to array
            $this->results = [];
            foreach ($products as $product) {
                $this->results[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => (float) $product->price,
                    'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
                    'image_url' => $product->image_url,
                ];
            }

            $this->debug[] = "Results array has: " . count($this->results) . " items";
            
            if (count($this->results) > 0) {
                $this->debug[] = "First: " . $this->results[0]['name'];
            }

        } catch (\Exception $e) {
            $this->debug[] = 'ERROR: ' . $e->getMessage();
            $this->debug[] = 'Line: ' . $e->getLine();
            $this->results = [];
            Log::error('ProductSearch Error', [
                'query' => $this->query,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}