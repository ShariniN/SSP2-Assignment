<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Shopping Cart
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
                <p class="text-gray-600 mt-2">Review your items before checkout</p>
            </div>

            @if(isset($cartItems) && count($cartItems) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Cart Items ({{ count($cartItems) }})
                                </h3>
                            </div>
                            
                            <div class="divide-y divide-gray-200">
                                @foreach($cartItems as $item)
                                    <div class="p-6 flex items-center space-x-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <img class="h-20 w-20 rounded-lg object-cover" 
                                                 src="{{ $item->product->image ?? '/images/placeholder-product.jpg' }}" 
                                                 alt="{{ $item->product->name }}">
                                        </div>
                                        
                                        <!-- Product Details -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-lg font-medium text-gray-900">
                                                <a href="{{ route('product.details', $item->product->id) }}" 
                                                   class="hover:text-blue-600 transition-colors">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $item->product->category->name ?? 'Uncategorized' }}
                                            </p>
                                            @if($item->product->description)
                                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                                    {{ Str::limit($item->product->description, 100) }}
                                                </p>
                                            @endif
                                        </div>
                                        
                                        <!-- Quantity Controls -->
                                        <div class="flex items-center space-x-3">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center space-x-2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" 
                                                        class="quantity-decrease w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors"
                                                        onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       max="{{ $item->product->stock ?? 99 }}"
                                                       class="w-16 text-center border border-gray-300 rounded-md py-1 px-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                       onchange="updateQuantity({{ $item->id }}, this.value)">
                                                
                                                <button type="button" 
                                                        class="quantity-increase w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors"
                                                        onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Price and Remove -->
                                        <div class="text-right">
                                            <div class="text-lg font-semibold text-gray-900">
                                                ${{ number_format($item->product->price * $item->quantity, 2) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ${{ number_format($item->product->price, 2) }} each
                                            </div>
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors"
                                                        onclick="return confirm('Are you sure you want to remove this item?')">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Continue Shopping -->
                            <div class="p-6 bg-gray-50 rounded-b-lg">
                                <a href="{{ route('products.index') }}" 
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                                
                                <!-- Summary Details -->
                                <div class="space-y-3 mb-6">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                                        <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Shipping</span>
                                        <span class="font-medium">
                                            @if($subtotal >= 100)
                                                <span class="text-green-600">Free</span>
                                            @else
                                                ${{ number_format($shipping, 2) }}
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tax</span>
                                        <span class="font-medium">${{ number_format($tax, 2) }}</span>
                                    </div>
                                    
                                    @if($discount > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Discount</span>
                                            <span class="font-medium text-green-600">-${{ number_format($discount, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Free Shipping Notice -->
                                @if($subtotal < 100)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div class="text-sm text-blue-800">
                                                <span class="font-medium">Free shipping</span> on orders over $100
                                            </div>
                                        </div>
                                        <div class="mt-2 text-sm text-blue-700">
                                            Add ${{ number_format(100 - $subtotal, 2) }} more to qualify!
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Promo Code -->
                                <div class="mb-6">
                                    <form action="{{ route('cart.apply-coupon') }}" method="POST">
                                        @csrf
                                        <div class="flex space-x-2">
                                            <input type="text" 
                                                   name="coupon_code" 
                                                   placeholder="Promo code" 
                                                   class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                                                Apply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Total -->
                                <div class="border-t border-gray-200 pt-4 mb-6">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">Total</span>
                                        <span class="text-xl font-bold text-gray-900">${{ number_format($total, 2) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Checkout Button -->
                                <button onclick="proceedToCheckout()" 
                                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Proceed to Checkout
                                </button>
                                
                                <!-- Payment Methods -->
                                <div class="mt-4 text-center">
                                    <p class="text-xs text-gray-500 mb-2">We accept</p>
                                    <div class="flex justify-center space-x-2">
                                        <img src="/images/visa.svg" alt="Visa" class="h-6">
                                        <img src="/images/mastercard.svg" alt="Mastercard" class="h-6">
                                        <img src="/images/paypal.svg" alt="PayPal" class="h-6">
                                        <img src="/images/apple-pay.svg" alt="Apple Pay" class="h-6">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Security Notice -->
                        <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <div class="text-sm text-green-800">
                                    <span class="font-medium">Secure checkout</span> with SSL encryption
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="text-center py-16">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H19M7 13v4a2 2 0 002 2h2m6-6v4a2 2 0 01-2 2h-2m-6-6h10"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
                    <p class="text-gray-600 mb-8">Looks like you haven't added any items yet.</p>
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for cart functionality -->
    <script>
        // Update quantity function
        function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) {
                if (confirm('Remove this item from cart?')) {
                    removeFromCart(itemId);
                }
                return;
            }
            
            fetch(`/cart/update/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to show updated totals
                } else {
                    alert(data.message || 'Failed to update quantity');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update quantity');
            });
        }
        
        // Remove item from cart
        function removeFromCart(itemId) {
            fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to remove item');
            });
        }
        
        // Proceed to checkout
        function proceedToCheckout() {
            @auth
                window.location.href = "{{ route('checkout') }}";
            @else
                if (confirm('Please log in to continue with checkout. Would you like to log in now?')) {
                    window.location.href = "{{ route('login') }}?redirect={{ urlencode(route('checkout')) }}";
                }
            @endauth
        }
        
        // Apply coupon code
        document.addEventListener('DOMContentLoaded', function() {
            const couponForm = document.querySelector('form[action="{{ route('cart.apply-coupon') }}"]');
            if (couponForm) {
                couponForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Invalid coupon code');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to apply coupon');
                    });
                });
            }
        });
    </script>
    
    <!-- Add these styles for line-clamp if not already available -->
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>