@extends('layouts.app')

@section('title', 'All Products - ElectroStore')

@section('content')
<div class="max-w-full px-4 sm:px-6 lg:px-8 py-12">

    <!-- Page Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">All Products</h1>
        <p class="text-gray-600">Browse through our wide range of electronics and gadgets</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        <!-- Sidebar Categories -->
        <aside class="lg:w-1/4">
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                <h2 class="text-lg font-semibold mb-6 text-gray-800">Categories</h2>
                <ul class="space-y-2">
                    <li class="flex justify-between items-center py-1">
                        <a href="{{ route('products.index') }}" 
                           class="text-gray-700 hover:text-blue-600 transition duration-200 {{ !request('category') ? 'font-semibold text-blue-600' : '' }}">
                            All Products
                        </a>
                        <span class="text-sm text-gray-500">{{ $products->total() ?? 0 }}</span>
                    </li>
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            <li class="flex justify-between items-center py-1">
                                <a href="{{ route('products.index', ['category' => $category->slug ?? $category->id]) }}" 
                                   class="text-gray-700 hover:text-blue-600 transition duration-200 {{ request('category') == ($category->slug ?? $category->id) ? 'font-semibold text-blue-600' : '' }}">
                                    {{ $category->name }}
                                </a>
                                <span class="text-sm text-gray-500">{{ $category->products_count ?? 0 }}</span>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="lg:w-3/4">

            <!-- Sort Bar -->
            <div class="flex justify-between items-center mb-6 p-4 bg-white rounded-lg shadow border border-gray-200">
                <p class="text-gray-600">
                    Showing <span class="font-semibold">{{ $products->firstItem() ?? 0 }}</span> to 
                    <span class="font-semibold">{{ $products->lastItem() ?? 0 }}</span> of 
                    <span class="font-semibold">{{ $products->total() ?? 0 }}</span> results
                </p>
                <form method="GET" action="{{ route('products.index') }}">
                    <select name="sort" onchange="this.form.submit()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </form>
            </div>

            @if(isset($products) && $products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden flex flex-col min-w-0">
                        <div class="relative w-full aspect-w-1 aspect-h-1">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="object-cover w-full h-full">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                </div>
                            @endif

                            @if(isset($product->discount_price) && $product->discount_price > 0)
                                <div class="absolute top-3 left-3">
                                    <span class="bg-red-500 text-white px-3 py-1 rounded text-sm font-semibold">Sale</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-4 flex flex-col gap-2 flex-1">
                            <h3 class="font-semibold text-gray-800 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm line-clamp-2">{{ $product->description }}</p>

                            <div class="mt-auto flex gap-2">
                                <a href="{{ route('product.details', $product->id) }}" 
                                   class="flex-1 bg-gray-100 text-gray-800 py-2 rounded hover:bg-gray-200 text-sm text-center">View</a>
                                @if($product->stock_quantity > 0)
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 text-sm">
                                            <i class="fas fa-cart-plus mr-1"></i>Add
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="flex-1 bg-gray-300 text-gray-500 py-2 rounded text-sm cursor-not-allowed">Out of Stock</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
                    <p class="text-gray-500 mb-6">We couldn't find any products. Please check back later!</p>
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </a>
                </div>
            </div>
            @endif

        </main>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
