@extends('layouts.app')

@section('title', $product->name . ' - ElectroStore')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Enhanced Breadcrumb -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    <i class="fas fa-home mr-1"></i>Home
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors">Products</a>
                @if($product->category)
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <a href="{{ route('products.index', ['category' => $product->category->slug ?? $product->category->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium transition-colors">{{ $product->category->name }}</a>
                @endif
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-gray-600 font-medium truncate">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    <!-- Main Product Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    <!-- Enhanced Product Images -->
                    <div class="space-y-6">
                        <div class="relative group">
                            <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl overflow-hidden shadow-lg">
                                @if($product->image)
                                    <img id="main-image" 
                                         src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-6xl text-gray-400"></i>
                                    </div>
                                @endif
                                
                                <!-- Image Zoom Indicator -->
                                @if($product->image)
                                    <div class="absolute top-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-search-plus mr-1"></i>Zoom
                                    </div>
                                @endif
                            </div>

                            <!-- Product Badges -->
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                        <i class="fas fa-fire mr-1"></i>Sale
                                    </span>
                                @endif
                                @if(isset($product->is_featured) && $product->is_featured)
                                    <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                        <i class="fas fa-star mr-1"></i>Featured
                                    </span>
                                @endif
                                @if(isset($product->is_new) && $product->is_new)
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                        <i class="fas fa-sparkles mr-1"></i>New
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Thumbnail Gallery (if multiple images exist) -->
                        <div class="flex space-x-4 overflow-x-auto">
                            @if($product->image)
                                <button class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden border-2 border-blue-500">
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                </button>
                            @endif
                            <!-- Add more thumbnails here if you have multiple images -->
                        </div>
                    </div>

                    <!-- Enhanced Product Info -->
                    <div class="space-y-8">
                        <!-- Product Header -->
                        <div>
                            @if($product->category)
                                <div class="flex items-center mb-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-{{ $product->category->icon ?? 'tag' }} mr-2"></i>
                                        {{ $product->category->name }}
                                    </span>
                                </div>
                            @endif
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3 leading-tight">{{ $product->name }}</h1>
                            @if($product->sku)
                                <p class="text-gray-500 font-medium">SKU: <span class="text-gray-700">{{ $product->sku }}</span></p>
                            @endif
                        </div>

                        <!-- Enhanced Rating & Reviews -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-3">
                                @if(isset($reviewCount) && $reviewCount > 0)
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($averageRating ?? 0))
                                                <i class="fas fa-star text-yellow-400 text-lg"></i>
                                            @elseif($i - 0.5 <= ($averageRating ?? 0))
                                                <i class="fas fa-star-half-alt text-yellow-400 text-lg"></i>
                                            @else
                                                <i class="far fa-star text-gray-300 text-lg"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="text-sm">
                                        <span class="font-semibold text-gray-900">{{ number_format($averageRating ?? 0, 1) }}</span>
                                        <span class="text-gray-600">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-gray-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="far fa-star text-lg"></i>
                                        @endfor
                                        <span class="ml-2 text-sm">No reviews yet</span>
                                    </div>
                                @endif
                            </div>
                            <button onclick="scrollToReviews()" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                @if(isset($reviewCount) && $reviewCount > 0)
                                    Read Reviews
                                @else
                                    Write First Review
                                @endif
                            </button>
                        </div>

                        <!-- Enhanced Price -->
                        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                            @if($product->discount_price && $product->discount_price < $product->price)
                                <div class="space-y-2">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="text-4xl font-bold text-red-600">${{ number_format($product->discount_price, 2) }}</span>
                                        <span class="text-2xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                            Save ${{ number_format($product->price - $product->discount_price, 2) }}
                                        </span>
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-bold">
                                            {{ number_format((($product->price - $product->discount_price) / $product->price) * 100, 0) }}% OFF
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span class="text-4xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>

                        <!-- Enhanced Stock Status -->
                        <div class="p-4 rounded-xl border-2 {{ $product->stock_quantity > 10 ? 'border-green-200 bg-green-50' : ($product->stock_quantity > 0 ? 'border-orange-200 bg-orange-50' : 'border-red-200 bg-red-50') }}">
                            @if($product->stock_quantity > 10)
                                <div class="flex items-center text-green-700">
                                    <i class="fas fa-check-circle text-xl mr-3"></i>
                                    <div>
                                        <p class="font-bold">In Stock</p>
                                        <p class="text-sm">{{ $product->stock_quantity }} available</p>
                                    </div>
                                </div>
                            @elseif($product->stock_quantity > 0)
                                <div class="flex items-center text-orange-700">
                                    <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                                    <div>
                                        <p class="font-bold">Limited Stock</p>
                                        <p class="text-sm">Only {{ $product->stock_quantity }} left - Order soon!</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center text-red-700">
                                    <i class="fas fa-times-circle text-xl mr-3"></i>
                                    <div>
                                        <p class="font-bold">Out of Stock</p>
                                        <p class="text-sm">This item is currently unavailable</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Enhanced Add to Cart Section with Wishlist -->
                        @auth
                            @if($product->stock_quantity > 0)
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <!-- Add to Cart Component -->
                                    <div class="flex-1">
                                        <livewire:cart-component :product="$product" />
                                    </div>
                                    
                                    <!-- Wishlist Button -->
                                    @php
                                        $inWishlist = auth()->user()->wishlist()->where('product_id', $product->id)->exists();
                                    @endphp
                                    
                                    <form action="{{ $inWishlist ? route('wishlist.remove', $product->id) : route('wishlist.add', $product->id) }}" 
                                          method="POST" class="flex-1 sm:flex-initial">
                                        @csrf
                                        @if($inWishlist)
                                            @method('DELETE')
                                        @endif
                                        <button type="submit" 
                                            class="w-full sm:w-auto bg-pink-500 hover:bg-pink-600 text-white py-4 px-6 rounded-xl font-bold text-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                            <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart mr-3"></i>
                                            {{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="space-y-4">
                                    <button disabled 
                                            class="w-full bg-gray-200 text-gray-400 py-4 px-6 rounded-xl font-bold text-lg cursor-not-allowed">
                                        <i class="fas fa-times mr-3"></i>Out of Stock
                                    </button>
                                    
                                    <!-- Wishlist Button (still available when out of stock) -->
                                    @php
                                        $inWishlist = auth()->user()->wishlist()->where('product_id', $product->id)->exists();
                                    @endphp
                                    
                                    <form action="{{ $inWishlist ? route('wishlist.remove', $product->id) : route('wishlist.add', $product->id) }}" 
                                          method="POST" class="w-full">
                                        @csrf
                                        @if($inWishlist)
                                            @method('DELETE')
                                        @endif
                                        <button type="submit" 
                                            class="w-full bg-pink-500 hover:bg-pink-600 text-white py-4 px-6 rounded-xl font-bold text-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                            <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart mr-3"></i>
                                            {{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                                        </button>
                                    </form>
                                    
                                    <button type="button" 
                                            class="w-full bg-blue-100 text-blue-700 py-3 px-6 rounded-xl font-medium hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-bell mr-2"></i>Notify When Available
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="space-y-4 p-6 bg-yellow-50 rounded-xl border border-yellow-200">
                                <div class="text-center">
                                    <i class="fas fa-user-circle text-4xl text-yellow-600 mb-3"></i>
                                    <h3 class="text-lg font-bold text-yellow-800 mb-2">Sign in to Purchase</h3>
                                    <p class="text-yellow-700 mb-4">Create an account or sign in to add items to your cart and wishlist.</p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <a href="{{ route('login') }}" 
                                       class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 px-6 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 text-center">
                                        <i class="fas fa-sign-in-alt mr-3"></i>Sign In
                                    </a>
                                    <a href="{{ route('register') }}" 
                                       class="w-full bg-gray-100 text-gray-700 py-4 px-6 rounded-xl font-bold text-lg hover:bg-gray-200 transition-all duration-200 border-2 border-gray-200 text-center">
                                        <i class="fas fa-user-plus mr-3"></i>Create Account
                                    </a>
                                </div>
                            </div>
                        @endauth

                        <!-- Product Features -->
                        <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-200">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-shipping-fast text-green-600 mr-2"></i>
                                <span>Free Shipping</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-undo text-blue-600 mr-2"></i>
                                <span>30-Day Returns</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-shield-alt text-purple-600 mr-2"></i>
                                <span>2-Year Warranty</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-headset text-orange-600 mr-2"></i>
                                <span>24/7 Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Product Description -->
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                Product Description
            </h2>
            <div class="prose max-w-none text-gray-700 leading-relaxed text-lg">
                {{ $product->description }}
            </div>
        </div>

        <!-- Enhanced Product Tabs -->
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex space-x-8 px-8" aria-label="Tabs">
                    <button onclick="showTab('specifications')" id="tab-specifications"
                            class="tab-button border-b-2 border-blue-500 text-blue-600 py-6 px-1 font-bold text-lg">
                        <i class="fas fa-list-ul mr-2"></i>Specifications
                    </button>
                    <button onclick="showTab('reviews')" id="tab-reviews"
                            class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-6 px-1 font-bold text-lg">
                        <i class="fas fa-star mr-2"></i>Reviews ({{ $reviewCount ?? 0 }})
                    </button>
                    <button onclick="showTab('shipping')" id="tab-shipping"
                            class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-6 px-1 font-bold text-lg">
                        <i class="fas fa-truck mr-2"></i>Shipping & Returns
                    </button>
                </nav>
            </div>

            <!-- Enhanced Tab Content -->
            <div class="p-8">
                <!-- Specifications -->
                <div id="content-specifications" class="tab-content">
                    @if(!empty($specifications ?? []))
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            @foreach($specifications as $key => $value)
                                <div class="flex justify-between items-center py-4 px-6 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="font-bold text-gray-900 text-lg">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="text-gray-700 font-medium">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Specifications Available</h3>
                            <p class="text-gray-500">Detailed specifications for this product will be added soon.</p>
                        </div>
                    @endif
                </div>

                <!-- Enhanced Reviews -->
                <div id="content-reviews" class="tab-content hidden">
                    @if(isset($reviewCount) && $reviewCount > 0)
                        <div class="space-y-8">
                            <!-- Review Summary -->
                            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl p-8 border border-yellow-200">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-2xl font-bold text-gray-900">Customer Reviews</h3>
                                    <button class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors font-medium">
                                        <i class="fas fa-edit mr-2"></i>Write a Review
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="text-center">
                                        <div class="text-5xl font-bold text-gray-900 mb-2">{{ number_format($averageRating ?? 0, 1) }}</div>
                                        <div class="flex items-center justify-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($averageRating ?? 0))
                                                    <i class="fas fa-star text-yellow-400 text-xl"></i>
                                                @elseif($i - 0.5 <= ($averageRating ?? 0))
                                                    <i class="fas fa-star-half-alt text-yellow-400 text-xl"></i>
                                                @else
                                                    <i class="far fa-star text-gray-300 text-xl"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <p class="text-gray-600">Based on {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        @for($i = 5; $i >= 1; $i--)
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm font-medium text-gray-700">{{ $i }} stars</span>
                                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ rand(10, 90) }}%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">{{ rand(1, 50) }}</span>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Individual Reviews -->
                            <div class="space-y-6">
                                <!-- Sample Review Structure - Replace with actual reviews -->
                                @for($i = 1; $i <= min(5, $reviewCount); $i++)
                                    <div class="border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                                    {{ chr(64 + $i) }}
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-gray-900">Customer {{ $i }}</h4>
                                                    <p class="text-sm text-gray-500">Verified Purchase</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="flex items-center mb-1">
                                                    @for($j = 1; $j <= 5; $j++)
                                                        @if($j <= rand(4, 5))
                                                            <i class="fas fa-star text-yellow-400"></i>
                                                        @else
                                                            <i class="far fa-star text-gray-300"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <p class="text-sm text-gray-500">{{ rand(1, 30) }} days ago</p>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 leading-relaxed">This is a sample review. Replace this section with actual review content from your database.</p>
                                    </div>
                                @endfor
                            </div>

                            <!-- Load More Reviews -->
                            @if($reviewCount > 5)
                                <div class="text-center">
                                    <button class="bg-gray-100 text-gray-700 px-8 py-3 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                                        Load More Reviews
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-comment-alt text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-700 mb-4">No Reviews Yet</h3>
                            <p class="text-gray-500 text-lg mb-8 max-w-md mx-auto">Be the first to share your experience with this product and help other customers make informed decisions!</p>
                            <button class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-bold shadow-lg hover:shadow-xl transform hover:scale-105">
                                <i class="fas fa-edit mr-3"></i>Write the First Review
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Enhanced Shipping -->
                <div id="content-shipping" class="tab-content hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <div class="space-y-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-shipping-fast text-3xl text-green-600 mr-4"></i>
                                <h3 class="text-2xl font-bold text-gray-900">Shipping Information</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3 p-4 bg-green-50 rounded-xl border border-green-200">
                                    <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-green-800">Free Standard Shipping</p>
                                        <p class="text-sm text-green-600">On orders over $100</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <i class="fas fa-clock text-blue-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-blue-800">Standard Delivery: 3-5 business days</p>
                                        <p class="text-sm text-blue-600">Most orders ship within 24 hours</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-4 bg-purple-50 rounded-xl border border-purple-200">
                                    <i class="fas fa-bolt text-purple-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-purple-800">Express Delivery: 1-2 business days</p>
                                        <p class="text-sm text-purple-600">Additional charges may apply</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-4 bg-orange-50 rounded-xl border border-orange-200">
                                    <i class="fas fa-globe text-orange-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-orange-800">International Shipping Available</p>
                                        <p class="text-sm text-orange-600">Delivery times vary by location</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-undo-alt text-3xl text-blue-600 mr-4"></i>
                                <h3 class="text-2xl font-bold text-gray-900">Return Policy</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <i class="fas fa-calendar-alt text-blue-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-blue-800">30-Day Return Window</p>
                                        <p class="text-sm text-blue-600">Returns accepted within 30 days of purchase</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-4 bg-green-50 rounded-xl border border-green-200">
                                    <i class="fas fa-box text-green-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-green-800">Original Condition Required</p>
                                        <p class="text-sm text-green-600">Items must be unused and in original packaging</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-4 bg-purple-50 rounded-xl border border-purple-200">
                                    <i class="fas fa-shipping-fast text-purple-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-purple-800">Free Return Shipping</p>
                                        <p class="text-sm text-purple-600">We'll cover the return shipping costs</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                                    <i class="fas fa-exchange-alt text-yellow-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-yellow-800">Full Refund or Exchange</p>
                                        <p class="text-sm text-yellow-600">Choose between refund or product exchange</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Related Products -->
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="mt-12 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-layer-group text-blue-600 mr-3"></i>
                        Related Products
                    </h2>
                    <a href="{{ route('products.index', ['category' => $product->category->slug ?? $product->category->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="group bg-gray-50 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 transform hover:-translate-y-2">
                            <div class="relative overflow-hidden">
                                @if($relatedProduct->image)
                                    <img src="{{ asset('storage/' . $relatedProduct->image) }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-56 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                    </div>
                                @endif
                                
                                <!-- Product Badges -->
                                <div class="absolute top-3 left-3 flex flex-col gap-2">
                                    @if($relatedProduct->discount_price && $relatedProduct->discount_price < $relatedProduct->price)
                                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                            <i class="fas fa-fire mr-1"></i>Sale
                                        </span>
                                    @endif
                                </div>

                                <!-- Quick Actions Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                        <a href="{{ route('product.show', $relatedProduct->id) }}" 
                                           class="bg-white text-gray-800 px-4 py-2 rounded-xl font-medium hover:bg-gray-100 transition-colors shadow-lg">
                                            <i class="fas fa-eye mr-2"></i>Quick View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors text-lg">
                                    <a href="{{ route('product.show', $relatedProduct->id) }}">
                                        {{ $relatedProduct->name }}
                                    </a>
                                </h3>
                                
                                <!-- Category -->
                                @if(isset($relatedProduct->category))
                                    <p class="text-sm text-blue-600 font-medium mb-3">{{ $relatedProduct->category->name }}</p>
                                @endif

                                <!-- Price -->
                                <div class="mb-4">
                                    @if($relatedProduct->discount_price && $relatedProduct->discount_price < $relatedProduct->price)
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xl font-bold text-red-600">${{ number_format($relatedProduct->discount_price, 2) }}</span>
                                            <span class="text-sm text-gray-500 line-through">${{ number_format($relatedProduct->price, 2) }}</span>
                                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">
                                                {{ number_format((($relatedProduct->price - $relatedProduct->discount_price) / $relatedProduct->price) * 100, 0) }}% OFF
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xl font-bold text-gray-900">${{ number_format($relatedProduct->price, 2) }}</span>
                                    @endif
                                </div>

                                <!-- Stock Status -->
                                <div class="mb-4">
                                    @if($relatedProduct->stock_quantity > 10)
                                        <div class="flex items-center text-green-600 text-sm">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            <span>In Stock</span>
                                        </div>
                                    @elseif($relatedProduct->stock_quantity > 0)
                                        <div class="flex items-center text-orange-600 text-sm">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Low Stock</span>
                                        </div>
                                    @else
                                        <div class="flex items-center text-red-600 text-sm">
                                            <i class="fas fa-times-circle mr-2"></i>
                                            <span>Out of Stock</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <a href="{{ route('product.show', $relatedProduct->id) }}" 
                                       class="flex-1 bg-gray-100 text-gray-700 text-center py-3 rounded-xl font-medium hover:bg-gray-200 transition-colors text-sm">
                                        View Details
                                    </a>
                                    @auth
                                        @if($relatedProduct->stock_quantity > 0)
                                            <button wire:click="addRelatedToCart({{ $relatedProduct->id }})" 
                                                    class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 text-sm shadow-lg hover:shadow-xl transform hover:scale-105">
                                                <i class="fas fa-cart-plus mr-1"></i>Add to Cart
                                            </button>
                                        @else
                                            <button disabled 
                                                    class="flex-1 bg-gray-200 text-gray-400 py-3 rounded-xl cursor-not-allowed font-medium text-sm">
                                                Out of Stock
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" 
                                           class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 text-sm shadow-lg hover:shadow-xl transform hover:scale-105">
                                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Reset all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        const selectedContent = document.getElementById('content-' + tabName);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
        
        // Highlight selected tab button
        const selectedButton = document.getElementById('tab-' + tabName);
        if (selectedButton) {
            selectedButton.classList.remove('border-transparent', 'text-gray-500');
            selectedButton.classList.add('border-blue-500', 'text-blue-600');
        }
    }

    function scrollToReviews() {
        showTab('reviews');
        // Smooth scroll to the tabs section
        document.getElementById('tab-reviews').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }

    // Initialize the first tab on page load
    document.addEventListener('DOMContentLoaded', function() {
        showTab('specifications');
    });
</script>
@endpush