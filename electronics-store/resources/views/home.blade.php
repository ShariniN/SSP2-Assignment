@extends('layouts.app')

@section('content')
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 text-green-800 px-6 py-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
        <div class="absolute inset-0 bg-black bg-opacity-10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="text-center">
                <div class="mb-8">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white mb-6">
                        <i class="fas fa-bolt mr-2 text-yellow-300"></i>
                        Latest Tech & Gadgets
                    </span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight">
                    Welcome to
                    <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
                        Electronics Store
                    </span>
                </h1>
                <p class="text-xl md:text-2xl mb-10 text-blue-100 max-w-3xl mx-auto leading-relaxed">
                    Discover cutting-edge technology and premium gadgets at unbeatable prices. Your one-stop destination for all things tech.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="#featured-products" 
                       class="group inline-flex items-center bg-white text-blue-700 px-8 py-4 rounded-xl font-semibold hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-shopping-bag mr-2 group-hover:scale-110 transition-transform"></i>
                        Shop Now
                    </a>
                    <a href="{{ route('products.index') }}" 
                       class="group inline-flex items-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-blue-700 transition-all duration-300 backdrop-blur-sm">
                        <i class="fas fa-grid-3x3 mr-2 group-hover:scale-110 transition-transform"></i>
                        View All Products
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    @if($categories->count() > 0)
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Shop by Category</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Explore our carefully curated categories to find exactly what you need</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('category.show', ['id' => $category->id]) }}" 
                       class="group block">
                        <div class="bg-white rounded-2xl p-6 text-center hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200 transform hover:-translate-y-2">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-all duration-300 shadow-lg">
                                <i class="fas fa-{{ $category->icon ?? 'microchip' }} text-xl text-white"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors duration-200 mb-1">
                                {{ $category->name }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Products Section -->
    <section id="featured-products" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Featured Products</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Discover our handpicked selection of the latest and most innovative products</p>
            </div>

            @if($featuredProducts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($featuredProducts as $product)
                        <div class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden transform hover:-translate-y-2">
                            <div class="relative overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ asset($product->image_url) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-56 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                    </div>
                                @endif
                                
                                <!-- Badges -->
                                <div class="absolute top-3 left-3 flex flex-col gap-2">
                                    @if($product->discount_price > 0)
                                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                            <i class="fas fa-fire mr-1"></i>Sale
                                        </span>
                                    @endif
                                </div>

                                <div class="absolute top-3 right-3 flex flex-col gap-2">
                                    @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                        <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg">
                                            Only {{ $product->stock_quantity }} left
                                        </span>
                                    @elseif($product->stock_quantity == 0)
                                        <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg">
                                            Out of Stock
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ $product->description }}
                                </p>
                                
                                <!-- Price -->
                                <div class="mb-4">
                                    @if($product->discount_price > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl font-bold text-red-600">
                                                ${{ number_format($product->discount_price, 2) }}
                                            </span>
                                            <span class="text-sm text-gray-500 line-through">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium">
                                                Save {{ number_format((($product->price - $product->discount_price) / $product->price) * 100, 0) }}%
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-2xl font-bold text-gray-900">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex gap-3">
                                    <a href="{{ route('product.show', $product->id) }}" 
                                       class="flex-1 bg-gray-100 text-gray-700 text-center py-3 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                                        View Details
                                    </a>
                                    @if($product->stock_quantity > 0)
                                        <button wire:click.prevent="addToCart({{ $product->id }}, 1)" 
                                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                            <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                                        </button>
                                    @else
                                        <button disabled 
                                                class="flex-1 bg-gray-200 text-gray-400 py-3 rounded-xl cursor-not-allowed font-medium">
                                            Out of Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20">
                    <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-4">No Products Yet</h3>
                    <p class="text-gray-500 text-lg max-w-md mx-auto">We're working hard to bring you amazing products. Check back soon for exciting new arrivals!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Best Sellers Section -->
    @if($bestSellers->count() > 0)
    <section class="py-20 bg-gradient-to-br from-yellow-50 to-orange-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center mb-4">
                    <i class="fas fa-trophy text-3xl text-yellow-500 mr-3"></i>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Best Sellers</h2>
                </div>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Join thousands of happy customers who chose these popular products</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($bestSellers as $index => $product)
                    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-yellow-200 overflow-hidden transform hover:-translate-y-2">
                        <div class="relative">
                            @if($product->image_url)
                                <img src="{{ asset( $product->image_url) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            
                            <div class="absolute top-3 left-3">
                                <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center">
                                    <i class="fas fa-star mr-1"></i>
                                    #{{ $index + 1 }} Best Seller
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                {{ $product->name }}
                            </h3>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-900">
                                    ${{ number_format($product->price, 2) }}
                                </span>
                                @if($product->stock_quantity > 0)
                                    <button wire:click.prevent="addToCart({{ $product->id }}, 1)" 
                                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                @else
                                    <span class="text-red-500 font-medium text-sm">Out of Stock</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Newsletter Section -->
    <section class="py-20 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-8">
                <i class="fas fa-envelope text-4xl text-blue-400 mb-4"></i>
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">Stay in the Loop</h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Be the first to know about exclusive deals, new arrivals, and special offers. Join our newsletter today!
                </p>
            </div>
            
            <form class="max-w-md mx-auto">
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <input type="email" 
                               placeholder="Enter your email address" 
                               required
                               class="w-full px-6 py-4 rounded-xl text-gray-800 bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition-all duration-200 shadow-lg">
                        <i class="fas fa-envelope absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button type="submit" 
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 whitespace-nowrap">
                        <i class="fas fa-paper-plane mr-2"></i>Subscribe
                    </button>
                </div>
            </form>
            
            <div class="mt-6 flex items-center justify-center text-sm text-gray-400">
                <i class="fas fa-shield-alt mr-2"></i>
                <span>We respect your privacy. Unsubscribe at any time.</span>
            </div>
        </div>
    </section>
@endsection
