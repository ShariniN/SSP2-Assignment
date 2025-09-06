=@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
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
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Cart Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">Cart Items ({{ count($cartItems ?? []) }})</h2>
                    </div>

                    <!-- Cart Items List -->
                    <div class="divide-y divide-gray-200">
                        @foreach($cartItems ?? [] as $item)
                        <div class="p-6 flex items-center space-x-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <img src="{{ $item['image'] ?? 'https://via.placeholder.com/80x80' }}" 
                                     alt="{{ $item['name'] ?? 'Product' }}" 
                                     class="w-20 h-20 object-cover rounded-lg border">
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-medium text-gray-900 truncate">
                                    {{ $item['name'] ?? 'Product Name' }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $item['description'] ?? 'Product description' }}
                                </p>
                                @if(isset($item['variant']))
                                <p class="text-sm text-gray-500">
                                    Variant: {{ $item['variant'] }}
                                </p>
                                @endif
                                <p class="text-lg font-semibold text-gray-900 mt-2">
                                    ${{ number_format($item['price'] ?? 0, 2) }}
                                </p>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button type="button" 
                                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-l-lg"
                                            onclick="updateQuantity({{ $item['id'] ?? 0 }}, 'decrease')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <input type="number" 
                                           value="{{ $item['quantity'] ?? 1 }}" 
                                           min="1" 
                                           class="w-16 text-center border-0 focus:ring-0 focus:outline-none"
                                           onchange="updateQuantity({{ $item['id'] ?? 0 }}, this.value)">
                                    <button type="button" 
                                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-r-lg"
                                            onclick="updateQuantity({{ $item['id'] ?? 0 }}, 'increase')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Item Total -->
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900">
                                    ${{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                                </p>
                            </div>

                            <!-- Remove Button -->
                            <button type="button" 
                                    class="text-red-500 hover:text-red-700 p-2"
                                    onclick="removeItem({{ $item['id'] ?? 0 }})">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <!-- Continue Shopping -->
                    <div class="bg-gray-50 px-6 py-4">
                        <a href="{{ route('shop') ?? '#' }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
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
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h3>
                    
                    <!-- Summary Details -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal ({{ array_sum(array_column($cartItems ?? [], 'quantity')) }} items)</span>
                            <span>${{ number_format($subtotal ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>{{ ($shipping ?? 0) > 0 ? '$' . number_format($shipping, 2) : 'Free' }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span>${{ number_format($tax ?? 0, 2) }}</span>
                        </div>
                        <hr class="my-4">
                        <div class="flex justify-between text-lg font-semibold text-gray-900">
                            <span>Total</span>
                            <span>${{ number_format($total ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <!-- Promo Code -->
                    <div class="mb-6">
                        <label for="promo-code" class="block text-sm font-medium text-gray-700 mb-2">
                            Promo Code
                        </label>
                        <div class="flex">
                            <input type="text" 
                                   id="promo-code" 
                                   name="promo_code"
                                   placeholder="Enter code"
                                   class="flex-1 min-w-0 block w-full px-3 py-2 border border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" 
                                    class="px-4 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-700 rounded-r-md hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <button type="button" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"
                            onclick="proceedToCheckout()">
                        Proceed to Checkout
                    </button>

                    <!-- Security Badge -->
                    <div class="mt-4 flex items-center justify-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Secure SSL Checkout
                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H19M7 13v4a2 2 0 002 2h2m6-6v4a2 2 0 01-2 2h-2m-6-6h10"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
            <p class="text-gray-600 mb-8">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('shop') ?? '#' }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Start Shopping
            </a>
        </div>
        @endif
    </div>
</div>

@endsection