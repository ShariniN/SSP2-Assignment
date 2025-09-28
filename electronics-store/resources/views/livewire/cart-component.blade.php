<div>
    @if($showRelatedProducts && $relatedProducts)
        <!-- Related Products Section -->
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
                                        <button wire:click="addRelatedToCart({{ $relatedProduct->id }})" wire:loading.attr="disabled"
                                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 text-sm shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-75">
                                            <span wire:loading.remove wire:target="addRelatedToCart({{ $relatedProduct->id }})">
                                                <i class="fas fa-cart-plus mr-1"></i>Add to Cart
                                            </span>
                                            <span wire:loading wire:target="addRelatedToCart({{ $relatedProduct->id }})">
                                                Adding...
                                            </span>
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
    @elseif($showAddToCart && $product)
        <!-- Add to Cart Section (for product pages) -->
        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <label class="text-lg font-medium text-gray-700">Quantity:</label>
                <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                    <button type="button" wire:click="decreaseAddQuantity" 
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors {{ $addToCartQuantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="w-16 px-4 py-3 text-center text-lg font-medium">{{ $addToCartQuantity }}</span>
                    <button type="button" wire:click="increaseAddQuantity" 
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors {{ $addToCartQuantity >= $product->stock_quantity ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button wire:click="addToCart" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 px-6 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none">
                    <i class="fas fa-cart-plus mr-3" wire:loading.remove></i>
                    <span wire:loading.remove>Add to Cart</span>
                    <span wire:loading>Adding...</span>
                </button>
                <button type="button" 
                        class="w-full bg-gray-100 text-gray-700 py-4 px-6 rounded-xl font-bold text-lg hover:bg-gray-200 transition-all duration-200 border-2 border-gray-200">
                    <i class="fas fa-heart mr-3"></i>Add to Wishlist
                </button>
            </div>
        </div>
    @else
        <!-- Regular Cart Display -->
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

                @if($cartItems && $cartItems->count() > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Cart Items -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-900">Cart Items ({{ $cartItems->count() }})</h2>
                                </div>
                                
                                <div class="divide-y divide-gray-200">
                                    @foreach($cartItems as $item)
                                        <div class="p-6 flex items-center space-x-4">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0">
                                                @if($item->product->image)
                                                    <img class="w-20 h-20 object-cover rounded-lg" 
                                                         src="{{ asset('storage/' . $item->product->image) }}" 
                                                         alt="{{ $item->product->name }}">
                                                @else
                                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Details -->
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-lg font-medium text-gray-900 truncate">
                                                    {{ $item->product->name }}
                                                </h3>
                                                @if($item->product->category)
                                                    <p class="text-sm text-gray-500">{{ $item->product->category->name }}</p>
                                                @endif
                                                <p class="text-lg font-semibold text-gray-900 mt-1">
                                                    ${{ number_format($item->product->price, 2) }}
                                                </p>
                                            </div>

                                            <!-- Quantity Controls -->
                                            <div class="flex items-center space-x-3">
                                                <div class="flex items-center border border-gray-300 rounded-md">
                                                    <button wire:click="decreaseQuantity({{ $item->id }})" 
                                                            class="px-3 py-1 text-gray-600 hover:text-gray-800 {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="px-4 py-1 text-gray-900 font-medium">{{ $item->quantity }}</span>
                                                    <button wire:click="increaseQuantity({{ $item->id }})" 
                                                            class="px-3 py-1 text-gray-600 hover:text-gray-800">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Item Total -->
                                                <div class="text-right">
                                                    <p class="text-lg font-semibold text-gray-900">
                                                        ${{ number_format($item->product->price * $item->quantity, 2) }}
                                                    </p>
                                                </div>
                                                
                                                <!-- Remove Button -->
                                                <button wire:click="removeItem({{ $item->id }})" 
                                                        wire:confirm="Are you sure you want to remove this item?"
                                                        class="text-red-600 hover:text-red-800 p-1">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Cart Actions -->
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('products.index') }}" 
                                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                                        </a>
                                        
                                        <button wire:click="clearCart" 
                                                wire:confirm="Are you sure you want to clear your entire cart?"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-300 rounded-md hover:bg-red-200">
                                            <i class="fas fa-trash mr-2"></i>Clear Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="text-gray-900">${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Shipping</span>
                                        <span class="text-gray-900">{{ $shipping > 0 ? '$'.number_format($shipping,2) : 'Free' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tax</span>
                                        <span class="text-gray-900">${{ number_format($tax, 2) }}</span>
                                    </div>
                                    <hr class="border-gray-200">
                                    <div class="flex justify-between font-semibold text-lg">
                                        <span class="text-gray-900">Total</span>
                                        <span class="text-gray-900">${{ number_format($total, 2) }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <a href="{{ route('checkout') }}" 
                                       class="w-full bg-blue-600 text-white text-center py-3 px-4 rounded-md font-medium hover:bg-blue-700 transition duration-200 block">
                                        <i class="fas fa-credit-card mr-2"></i>Proceed to Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty Cart -->
                    <div class="text-center py-16">
                        <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
                        </div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your cart is empty</h2>
                        <p class="text-gray-600 mb-8">Start shopping to add items to your cart.</p>
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-shopping-bag mr-2"></i>Start Shopping
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>