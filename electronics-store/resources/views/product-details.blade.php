=@extends('layouts.app')

@section('title', $product->name . ' - Electronics Store')

@section('content')
<div class="bg-white">
    <!-- Breadcrumb -->
    <nav class="bg-gray-50 px-4 py-3">
        <div class="max-w-7xl mx-auto">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Home</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">Products</a></li>
                @if($product->category)
                    <li class="text-gray-400">/</li>
                    <li><a href="{{ route('category', $product->category->slug) }}" class="text-blue-600 hover:text-blue-800">{{ $product->category->name }}</a></li>
                @endif
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">{{ $product->name }}</li>
            </ol>
        </div>
    </nav>

    <!-- Product Details -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                    @if($product->image)
                        <img id="main-image" 
                             src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-image text-6xl text-gray-300"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Image thumbnails (if you have multiple images) -->
                <div class="flex space-x-2 overflow-x-auto">
                    @if($product->image)
                        <button class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-blue-500">
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        </button>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <!-- Product Title & Category -->
                <div>
                    @if($product->category)
                        <p class="text-sm text-blue-600 font-medium">{{ $product->category->name }}</p>
                    @endif
                    <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>
                    
                    <!-- SKU -->
                    @if($product->sku)
                        <p class="text-sm text-gray-500 mt-2">SKU: {{ $product->sku }}</p>
                    @endif
                </div>

                <!-- Rating & Reviews -->
                @if($reviewCount > 0)
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i <= ceil($averageRating))
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-gray-300"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                    </div>
                @endif

                <!-- Price -->
                <div class="space-y-2">
                    @if($product->discount_price && $product->discount_price < $product->price)
                        <div class="flex items-center space-x-3">
                            <span class="text-3xl font-bold text-red-600">${{ number_format($product->discount_price, 2) }}</span>
                            <span class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                            <span class="bg-red-100 text-red-800 text-sm font-semibold px-2 py-1 rounded">
                                Save ${{ number_format($product->price - $product->discount_price, 2) }}
                            </span>
                        </div>
                    @else
                        <span class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="space-y-2">
                    @if($product->stock_quantity > 10)
                        <p class="text-green-600 font-medium">
                            <i class="fas fa-check-circle mr-1"></i>
                            In Stock ({{ $product->stock_quantity }} available)
                        </p>
                    @elseif($product->stock_quantity > 0)
                        <p class="text-orange-600 font-medium">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Low Stock (Only {{ $product->stock_quantity }} left)
                        </p>
                    @else
                        <p class="text-red-600 font-medium">
                            <i class="fas fa-times-circle mr-1"></i>
                            Out of Stock
                        </p>
                    @endif
                </div>

                <!-- Description -->
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Product Description</h3>
                    <p class="text-gray-700">{{ $product->description }}</p>
                </div>

                <!-- Add to Cart Form -->
                @if($product->stock_quantity > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <!-- Quantity Selector -->
                        <div class="flex items-center space-x-4">
                            <label for="quantity" class="text-sm font-medium text-gray-700">Quantity:</label>
                            <div class="flex items-center border border-gray-300 rounded-md">
                                <button type="button" onclick="decreaseQuantity()" 
                                        class="px-3 py-2 text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                                       class="w-16 px-3 py-2 text-center border-0 focus:ring-0">
                                <button type="button" onclick="increaseQuantity()" 
                                        class="px-3 py-2 text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" 
                                    class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                                <i class="fas fa-cart-plus mr-2"></i>
                                Add to Cart
                            </button>
                            
                            <button type="button" onclick="addToWishlist({{ $product->id }})"
                                    class="flex-1 border border-gray-300 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-50 transition duration-200 flex items-center justify-center">
                                <i class="far fa-heart mr-2" id="wishlist-icon-{{ $product->id }}"></i>
                                Add to Wishlist
                            </button>
                        </div>

                        <!-- Buy Now Button -->
                        <button type="button" onclick="buyNow()" 
                                class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-200">
                            <i class="fas fa-bolt mr-2"></i>
                            Buy Now
                        </button>
                    </form>
                @else
                    <div class="space-y-4">
                        <button disabled 
                                class="w-full bg-gray-300 text-gray-500 py-3 px-6 rounded-lg font-semibold cursor-not-allowed">
                            Out of Stock
                        </button>
                        
                        <button type="button" 
                                class="w-full border border-blue-600 text-blue-600 py-3 px-6 rounded-lg font-semibold hover:bg-blue-50 transition duration-200">
                            Notify When Available
                        </button>
                    </div>
                @endif

                <!-- Additional Info -->
                <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-200">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-truck mr-2"></i>
                        Free shipping on orders over $100
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-undo mr-2"></i>
                        30-day return policy
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-shield-alt mr-2"></i>
                        2-year warranty
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-headset mr-2"></i>
                        24/7 customer support
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="mt-16">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button onclick="showTab('specifications')" id="tab-specifications"
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Specifications
                    </button>
                    <button onclick="showTab('reviews')" id="tab-reviews"
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Reviews ({{ $reviewCount }})
                    </button>
                    <button onclick="showTab('shipping')" id="tab-shipping"
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Shipping & Returns
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-8">
                <!-- Specifications Tab -->
                <div id="content-specifications" class="tab-content">
                    @if(!empty($specifications))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($specifications as $key => $value)
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="text-gray-600">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No specifications available for this product.</p>
                    @endif
                </div>

                <!-- Reviews Tab -->
                <div id="content-reviews" class="tab-content hidden">
                    @if($reviewCount > 0)
                        <!-- Add review display logic here -->
                        <p class="text-gray-600">Reviews will be displayed here.</p>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-star text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No reviews yet</h3>
                            <p class="text-gray-500 mb-4">Be the first to review this product!</p>
                            <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                                Write a Review
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Shipping Tab -->
                <div id="content-shipping" class="tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Shipping Information</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li>• Free shipping on orders over $100</li>
                                <li>• Standard shipping: 3-5 business days</li>
                                <li>• Express shipping: 1-2 business days</li>
                                <li>• International shipping available</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Return Policy</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li>• 30-day return window</li>
                                <li>• Items must be in original condition</li>
                                <li>• Free return shipping</li>
                                <li>• Full refund or exchange</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                            <div class="relative">
                                @if($relatedProduct->image)
                                    <img src="{{ asset('storage/' . $relatedProduct->image) }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                    </div>
                                @endif
                                
                                @if($relatedProduct->discount_price > 0)
                                    <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-sm">
                                        Sale
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 mb-2">
                                    <a href="{{ route('products.show', $relatedProduct->id) }}" class="hover:text-blue-600">
                                        {{ $relatedProduct->name }}
                                    </a>
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($relatedProduct->discount_price > 0)
                                            <span class="text-lg font-bold text-red-600">
                                                ${{ number_format($relatedProduct->discount_price, 2) }}
                                            </span>
                                            <span class="text-sm text-gray-500 line-through ml-1">
                                                ${{ number_format($relatedProduct->price, 2) }}
                                            </span>
                                        @else
                                            <span class="text-lg font-bold text-gray-800">
                                                ${{ number_format($relatedProduct->price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($relatedProduct->stock_quantity > 0)
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" 
                                                    class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition duration-200">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    @endif
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