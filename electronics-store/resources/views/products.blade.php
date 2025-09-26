<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Our Products</h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">Browse our latest electronics</p>

            <!-- Search Bar -->
            <form action="{{ route('products.search') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="flex rounded-lg overflow-hidden shadow-lg">
                    <input type="text" 
                           name="q" 
                           placeholder="Search products, brands, models..." 
                           value="{{ request('q') }}"
                           class="flex-1 px-6 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <button type="submit" 
                            class="bg-yellow-500 hover:bg-yellow-600 px-8 py-4 font-semibold text-gray-900 transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:w-1/4">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
                        
                        <form action="{{ route('products.index') }}" method="GET">
                            <!-- Preserve search query -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <!-- Categories -->
                            @if($categories->count() > 0)
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-700 mb-2">Categories</h4>
                                    <div class="space-y-2">
                                        @foreach($categories as $category)
                                            <label class="flex items-center">
                                                <input type="radio" 
                                                       name="category" 
                                                       value="{{ $category->slug }}"
                                                       {{ request('category') == $category->slug ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-600">{{ $category->name }}</span>
                                            </label>
                                        @endforeach
                                        <label class="flex items-center">
                                            <input type="radio" 
                                                   name="category" 
                                                   value=""
                                                   {{ !request('category') ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-600">All Categories</span>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <!-- Price Range -->
                            @if($priceRange)
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-700 mb-2">Price Range</h4>
                                    <div class="flex space-x-2">
                                        <input type="number" 
                                               name="min_price" 
                                               value="{{ request('min_price') }}"
                                               placeholder="Min ${{ number_format($priceRange->min_price, 0) }}"
                                               class="flex-1 rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <input type="number" 
                                               name="max_price" 
                                               value="{{ request('max_price') }}"
                                               placeholder="Max ${{ number_format($priceRange->max_price, 0) }}"
                                               class="flex-1 rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>
                            @endif

                            <!-- Sort -->
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Sort By</h4>
                                <select name="sort" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                                </select>
                            </div>

                            <div class="flex space-x-2">
                                <button type="submit" 
                                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition duration-200 text-sm">
                                    Apply Filters
                                </button>
                                <a href="{{ route('products.index') }}" 
                                   class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-400 transition duration-200 text-sm text-center">
                                    Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="lg:w-3/4">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <!-- Results Header -->
                            <div class="flex justify-between items-center mb-6">
                                <p class="text-gray-600">
                                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} 
                                    of {{ $products->total() }} products
                                </p>
                            </div>

                            @if($products->count() > 0)
                                <!-- Products Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($products as $product)
                                        <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-200">
                                            <div class="relative">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="w-full h-48 object-cover">
                                                @else
                                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                
                                                @if($product->is_on_sale)
                                                    <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-sm">
                                                        -{{ $product->savings_percentage }}%
                                                    </div>
                                                @endif

                                                @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                                    <div class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 rounded text-sm">
                                                        Low Stock
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="p-4">
                                                <div class="mb-2">
                                                    @if($product->category)
                                                        <span class="text-xs text-blue-600 font-medium">{{ $product->category->name }}</span>
                                                    @endif
                                                </div>
                                                
                                                <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
                                                    <a href="{{ route('product.details', $product->id) }}" class="hover:text-blue-600">
                                                        {{ $product->name }}
                                                    </a>
                                                </h3>
                                                
                                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                                                
                                                <div class="flex items-center justify-between mb-3">
                                                    <div class="flex flex-col">
                                                        @if($product->is_on_sale)
                                                            <span class="text-lg font-bold text-red-600">
                                                                ${{ number_format($product->discount_price, 2) }}
                                                            </span>
                                                            <span class="text-sm text-gray-500 line-through">
                                                                ${{ number_format($product->price, 2) }}
                                                            </span>
                                                        @else
                                                            <span class="text-lg font-bold text-gray-800">
                                                                ${{ number_format($product->price, 2) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="text-right">
                                                        @if($product->stock_quantity > 0)
                                                            <p class="text-xs text-green-600">In Stock</p>
                                                        @else
                                                            <p class="text-xs text-red-600">Out of Stock</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('product.details', $product->id) }}" 
                                                       class="flex-1 bg-blue-600 text-white py-2 px-4 rounded text-sm hover:bg-blue-700 transition duration-200 text-center">
                                                        View Details
                                                    </a>
                                                    
                                                    @if($product->stock_quantity > 0)
                                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-shrink-0">
                                                            @csrf
                                                            <input type="hidden" name="quantity" value="1">
                                                            <button type="submit" 
                                                                    class="bg-green-600 text-white p-2 rounded hover:bg-green-700 transition duration-200">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                <div class="mt-8">
                                    {{ $products->appends(request()->query())->links() }}
                                </div>
                            @else
                                <!-- No Products Found -->
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM10 18V8l5 4v6h-5zm-1 0h-5v-6l5-4v10z" clip-rule="evenodd"/>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No products found</h3>
                                    <p class="text-gray-500 mb-4">Try adjusting your search criteria or browse all products.</p>
                                    <a href="{{ route('products.index') }}" 
                                       class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition duration-200">
                                        View All Products
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Add to cart with AJAX feedback
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form[action*="cart/add"]');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('button[type="submit"]');
                    const originalText = button.innerHTML;
                    
                    // Show loading state
                    button.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Adding...
                    `;
                    button.disabled = true;
                    
                    // Reset after 2 seconds
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }, 2000);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>