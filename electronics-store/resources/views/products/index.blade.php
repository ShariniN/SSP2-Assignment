@extends('layouts.app')

@section('title', 'All Products - ElectroStore')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">All Products</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Discover our complete collection of premium electronics and cutting-edge gadgets</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Sidebar (Categories) -->
            <aside class="lg:w-80 lg:flex-shrink-0">
                <div class="sticky top-4 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                            <h2 class="text-lg font-semibold text-white flex items-center">
                                <i class="fas fa-layer-group mr-3"></i>Categories
                            </h2>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('products.index') }}" 
                                       class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ !request('category') ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                                        <div class="flex items-center">
                                            <i class="fas fa-th-large mr-3 {{ !request('category') ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                            <span>All Products</span>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ !request('category') ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $products->total() ?? 0 }}
                                        </span>
                                    </a>
                                </li>
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('products.index', ['category' => $category->slug ?? $category->id]) }}" 
                                           class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ request('category') == ($category->slug ?? $category->id) ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                                            <div class="flex items-center">
                                                <i class="fas fa-{{ $category->icon ?? 'microchip' }} mr-3 {{ request('category') == ($category->slug ?? $category->id) ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                                <span>{{ $category->name }}</span>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ request('category') == ($category->slug ?? $category->id) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $category->products_count ?? 0 }}
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 min-w-0">
                <!-- Sort Bar -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-search mr-2 text-gray-400"></i>
                            <span>
                                Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() ?? 0 }}</span> to 
                                <span class="font-semibold text-gray-900">{{ $products->lastItem() ?? 0 }}</span> of 
                                <span class="font-semibold text-gray-900">{{ $products->total() ?? 0 }}</span> results
                            </span>
                        </div>
                        <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-3">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
                            <select name="sort" id="sort" onchange="this.form.submit()" 
                                class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-blue-500">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            </select>
                        </form>
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 mb-8">
                        @foreach($products as $product)
                            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden transform hover:-translate-y-1">
                                <!-- Product Image -->
                                <div class="relative overflow-hidden">
                                    @if($product->image_url)
                                        <img src="{{ asset('storage/' . $product->image_url) }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                            <i class="fas fa-image text-4xl text-gray-400"></i>
                                        </div>
                                    @endif

                                    <!-- Badges -->
                                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                                        @if($product->discount_price)
                                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                                <i class="fas fa-fire mr-1"></i>Sale
                                            </span>
                                        @endif
                                        @if($product->is_featured)
                                            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                                <i class="fas fa-star mr-1"></i>Featured
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Product Details -->
                                <div class="p-6">
                                    <h3 class="font-bold text-gray-900 mb-2 text-lg line-clamp-2">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {{ $product->description }}
                                    </p>

                                    <div class="mb-4">
                                        @if($product->discount_price)
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-2xl font-bold text-red-600">
                                                    ${{ number_format($product->discount_price, 2) }}
                                                </span>
                                                <span class="text-lg text-gray-500 line-through">
                                                    ${{ number_format($product->price, 2) }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-2xl font-bold text-gray-900">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Buttons -->
                                    <div class="flex gap-3">
                                        <a href="{{ route('product.show', $product->id) }}" 
                                           class="flex-1 bg-gray-100 text-gray-700 text-center py-3 rounded-xl font-medium hover:bg-gray-200 transition-all duration-200 text-sm">
                                            <i class="fas fa-info-circle mr-2"></i>View Details
                                        </a>
                                        @auth
                                            @if($product->stock_quantity > 0)
                                                <button wire:click.prevent="$emit('addToCart', {{ $product->id }})"
                                                    class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 text-sm shadow-lg">
                                                    <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                                                </button>
                                            @else
                                                <button disabled class="flex-1 bg-gray-200 text-gray-400 py-3 rounded-xl text-sm">
                                                    <i class="fas fa-times mr-2"></i>Out of Stock
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" 
                                               class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 text-sm shadow-lg">
                                                <i class="fas fa-sign-in-alt mr-2"></i>Login to Buy
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                        <h3 class="text-2xl font-bold text-gray-700 mb-4">No Products Found</h3>
                        <p class="text-gray-500 text-lg mb-8">Try adjusting your filters or check back later.</p>
                        <a href="{{ route('products.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-xl">Clear Filters</a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

@endsection