@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @csrf
            
            <!-- Billing Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Billing Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" id="first_name" name="first_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-500 @enderror" 
                               value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-500 @enderror" 
                               value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                               value="{{ old('email') }}" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                        <input type="tel" id="phone" name="phone" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror" 
                               value="{{ old('phone') }}" required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                    <textarea id="address" name="address" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror" 
                              required>{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                        <input type="text" id="city" name="city" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('city') border-red-500 @enderror" 
                               value="{{ old('city') }}" required>
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                        <input type="text" id="state" name="state" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('state') border-red-500 @enderror" 
                               value="{{ old('state') }}" required>
                        @error('state')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">ZIP Code *</label>
                        <input type="text" id="zip_code" name="zip_code" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('zip_code') border-red-500 @enderror" 
                               value="{{ old('zip_code') }}" required>
                        @error('zip_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                    <select id="country" name="country" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country') border-red-500 @enderror" 
                            required>
                        <option value="">Select Country</option>
                        <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                        <option value="CA" {{ old('country') == 'CA' ? 'selected' : '' }}>Canada</option>
                        <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                        <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>Australia</option>
                    </select>
                    @error('country')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Information -->
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="credit_card" 
                                   class="mr-2" {{ old('payment_method') == 'credit_card' ? 'checked' : '' }} required>
                            <span class="text-sm">Credit Card</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="debit_card" 
                                   class="mr-2" {{ old('payment_method') == 'debit_card' ? 'checked' : '' }}>
                            <span class="text-sm">Debit Card</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="paypal" 
                                   class="mr-2" {{ old('payment_method') == 'paypal' ? 'checked' : '' }}>
                            <span class="text-sm">PayPal</span>
                        </label>
                    </div>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="card-details" class="space-y-4">
                    <div>
                        <label for="card_name" class="block text-sm font-medium text-gray-700 mb-2">Name on Card</label>
                        <input type="text" id="card_name" name="card_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('card_name') border-red-500 @enderror" 
                               value="{{ old('card_name') }}">
                        @error('card_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('card_number') border-red-500 @enderror" 
                               value="{{ old('card_number') }}">
                        @error('card_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="card_expiry" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('card_expiry') border-red-500 @enderror" 
                                   value="{{ old('card_expiry') }}">
                            @error('card_expiry')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="card_cvv" class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                            <input type="text" id="card_cvv" name="card_cvv" placeholder="123"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('card_cvv') border-red-500 @enderror" 
                                   value="{{ old('card_cvv') }}">
                            @error('card_cvv')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 h-fit">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Order Summary</h2>
                
                <!-- Cart Items -->
                <div class="space-y-4 mb-6">
                    @foreach($cartItems as $id => $item)
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800">{{ $item['name'] }}</h4>
                                <p class="text-sm text-gray-600">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-800">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Totals -->
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="text-gray-800">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="text-gray-800">${{ number_format($shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax:</span>
                        <span class="text-gray-800">${{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between text-lg font-semibold">
                            <span class="text-gray-800">Total:</span>
                            <span class="text-gray-800">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Place Order Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
                    Place Order
                </button>
                
                <p class="text-xs text-gray-500 text-center mt-4">
                    By placing your order, you agree to our Terms & Conditions and Privacy Policy.
                </p>
            </div>
        </form>
    </div>
</div>

<script>
    // Show/hide card details based on payment method
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const cardDetails = document.getElementById('card-details');
        
        function toggleCardDetails() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (selectedMethod && (selectedMethod.value === 'credit_card' || selectedMethod.value === 'debit_card')) {
                cardDetails.style.display = 'block';
                // Make card fields required
                document.getElementById('card_number').required = true;
                document.getElementById('card_expiry').required = true;
                document.getElementById('card_cvv').required = true;
                document.getElementById('card_name').required = true;
            } else {
                cardDetails.style.display = 'none';
                // Remove required from card fields
                document.getElementById('card_number').required = false;
                document.getElementById('card_expiry').required = false;
                document.getElementById('card_cvv').required = false;
                document.getElementById('card_name').required = false;
            }
        }
        
        paymentMethods.forEach(method => {
            method.addEventListener('change', toggleCardDetails);
        });
        
        // Initial check
        toggleCardDetails();
        
        // Format card number input
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
            e.target.value = formattedValue;
        });
        
        // Format expiry date input
        document.getElementById('card_expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
        
        // Format CVV input
        document.getElementById('card_cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });
    });
</script>
@endsection