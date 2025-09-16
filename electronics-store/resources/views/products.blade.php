@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Our Products</h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">Browse our latest electronics</p>

            <!-- Search Bar -->
            <form action="{{ route('products.search') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="flex rounded-lg overflow-hidden shadow-lg">
                    <input type="text" 
                           name="q" 
                           placeholder="Search products, brands, models..." 
                           value="{{ request('q') }}"
                           class="flex-1 px-6 py-4 text-gray-900 focus:outline-none">
                    <button type="submit" 
                            class="bg-yellow-500 hover:bg-yellow-600 px-8 py-4 font-semibold text-gray-900 transition duration-200">
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <!-- Products Grid -->
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Products</h2>

            @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($products as $product)
                <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden">
                    <a href="{{ route('products.show', $product->id) }}" class="block">
                        <!-- Product Image -->
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                            @if($product->image)
                                <img src="{{ $product->image }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <svg class="w-20 h-20 text-gray-400 group-hover:text-blue-500 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Product Badge -->
                            <div class="absolute top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition duration-200">
                                {{ $product->name }}
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ $product->description }}
                            </p>
                            <p class="text-lg font-semibold text-gray-900 mb-4">
                                ${{ number_format($product->price, 2) }}
                            </p>

                            <!-- Add to Cart Button -->
                            @if($product->stock > 0)
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                    Add to Cart
                                </button>
                            </form>
                            @else
                            <button type="button" 
                                    class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg font-medium cursor-not-allowed">
                                Out of Stock
                            </button>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                {{ $products->links() }}
            </div>

            @else
            <!-- No Products -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">No Products Found</h3>
                <p class="text-gray-600">Products will appear here once they are added or match your search criteria.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
