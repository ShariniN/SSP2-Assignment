@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

    @if($cartItems->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Billing Details -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Billing Details</h2>
            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->name ?? '') }}" required class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label for="address">Address</label>
                        <textarea name="address" required class="w-full border p-2 rounded">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label for="city">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" required class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label for="state">State</label>
                        <input type="text" name="state" value="{{ old('state') }}" required class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label for="zip_code">Zip Code</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code') }}" required class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label for="country">Country</label>
                        <input type="text" name="country" value="{{ old('country') }}" required class="w-full border p-2 rounded">
                    </div>
                </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
            <div class="space-y-4">
                @foreach($cartItems as $item)
                <div class="flex justify-between items-center border-b pb-2">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset($item->product->image_url) }}" class="w-16 h-16 object-cover rounded">
                        <div>
                            <h3>{{ $item->product->name }}</h3>
                            <p>Qty: {{ $item->quantity }}</p>
                        </div>
                    </div>
                    <div>${{ number_format(($item->product->discount_price ?? $item->product->price) * $item->quantity, 2) }}</div>
                </div>
                @endforeach

                <div class="flex justify-between mt-4 font-bold">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between mt-1 font-bold">
                    <span>Shipping</span>
                    <span>${{ $shipping == 0 ? 'Free' : number_format($shipping, 2) }}</span>
                </div>
                <div class="flex justify-between mt-1 font-bold">
                    <span>Tax</span>
                    <span>${{ number_format($tax, 2) }}</span>
                </div>
                <div class="flex justify-between mt-4 text-lg font-bold">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" checked class="mr-2"> 
                        <span>Cash on Delivery</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="payment_method" value="card" class="mr-2"> 
                        <span>Credit / Debit Card</span>
                    </label>
                </div>

                <!-- Card Details Section (Hidden by default) -->
                <div id="cardDetailsSection" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                    <h3 class="text-lg font-semibold mb-3">Card Details</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="card_number" class="block text-sm font-medium mb-1">Card Number</label>
                            <input type="text" 
                                   id="card_number" 
                                   name="card_number" 
                                   placeholder="1234 5678 9012 3456"
                                   maxlength="19"
                                   class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-red-500 text-sm hidden" id="card_number_error">Please enter a valid card number</span>
                        </div>
                        <div>
                            <label for="card_name" class="block text-sm font-medium mb-1">Cardholder Name</label>
                            <input type="text" 
                                   id="card_name" 
                                   name="card_name" 
                                   placeholder="John Doe"
                                   class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-red-500 text-sm hidden" id="card_name_error">Please enter cardholder name</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="expiry_date" class="block text-sm font-medium mb-1">Expiry Date</label>
                                <input type="text" 
                                       id="expiry_date" 
                                       name="expiry_date" 
                                       placeholder="MM/YY"
                                       maxlength="5"
                                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                                <span class="text-red-500 text-sm hidden" id="expiry_error">Invalid expiry date</span>
                            </div>
                            <div>
                                <label for="cvv" class="block text-sm font-medium mb-1">CVV</label>
                                <input type="text" 
                                       id="cvv" 
                                       name="cvv" 
                                       placeholder="123"
                                       maxlength="4"
                                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                                <span class="text-red-500 text-sm hidden" id="cvv_error">Invalid CVV</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-4 w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">Place Order</button>
            </form>
        </div>
    </div>
    @else
        <div class="text-center py-16">
            <h3>Your cart is empty</h3>
            <a href="{{ route('products.index') }}">Continue Shopping</a>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardDetailsSection = document.getElementById('cardDetailsSection');
    const checkoutForm = document.getElementById('checkoutForm');
    
    // Card input fields
    const cardNumber = document.getElementById('card_number');
    const cardName = document.getElementById('card_name');
    const expiryDate = document.getElementById('expiry_date');
    const cvv = document.getElementById('cvv');

    // Toggle card details section
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'card') {
                cardDetailsSection.classList.remove('hidden');
                // Make card fields required
                cardNumber.required = true;
                cardName.required = true;
                expiryDate.required = true;
                cvv.required = true;
            } else {
                cardDetailsSection.classList.add('hidden');
                // Make card fields optional
                cardNumber.required = false;
                cardName.required = false;
                expiryDate.required = false;
                cvv.required = false;
            }
        });
    });

    // Format card number with spaces
    cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Format expiry date
    expiryDate.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        e.target.value = value;
    });

    // Only allow numbers for CVV
    cvv.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });

    // Form validation
    checkoutForm.addEventListener('submit', function(e) {
        const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (selectedPayment === 'card') {
            let isValid = true;

            // Validate card number (basic Luhn algorithm check)
            const cardNumberValue = cardNumber.value.replace(/\s/g, '');
            if (cardNumberValue.length < 13 || cardNumberValue.length > 19 || !luhnCheck(cardNumberValue)) {
                showError('card_number_error');
                isValid = false;
            } else {
                hideError('card_number_error');
            }

            // Validate cardholder name
            if (cardName.value.trim().length < 3) {
                showError('card_name_error');
                isValid = false;
            } else {
                hideError('card_name_error');
            }

            // Validate expiry date
            const expiryValue = expiryDate.value;
            const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
            if (!expiryRegex.test(expiryValue) || !isValidExpiry(expiryValue)) {
                showError('expiry_error');
                isValid = false;
            } else {
                hideError('expiry_error');
            }

            // Validate CVV
            const cvvValue = cvv.value;
            if (cvvValue.length < 3 || cvvValue.length > 4) {
                showError('cvv_error');
                isValid = false;
            } else {
                hideError('cvv_error');
            }

            if (!isValid) {
                e.preventDefault();
            }
        }
    });

    // Helper functions
    function showError(errorId) {
        document.getElementById(errorId).classList.remove('hidden');
    }

    function hideError(errorId) {
        document.getElementById(errorId).classList.add('hidden');
    }

    // Luhn algorithm for card validation
    function luhnCheck(cardNumber) {
        let sum = 0;
        let isEven = false;
        
        for (let i = cardNumber.length - 1; i >= 0; i--) {
            let digit = parseInt(cardNumber[i]);
            
            if (isEven) {
                digit *= 2;
                if (digit > 9) {
                    digit -= 9;
                }
            }
            
            sum += digit;
            isEven = !isEven;
        }
        
        return sum % 10 === 0;
    }

    // Check if expiry date is valid and not expired
    function isValidExpiry(expiry) {
        const [month, year] = expiry.split('/');
        const now = new Date();
        const currentYear = now.getFullYear() % 100;
        const currentMonth = now.getMonth() + 1;
        
        const expMonth = parseInt(month);
        const expYear = parseInt(year);
        
        if (expYear < currentYear) return false;
        if (expYear === currentYear && expMonth < currentMonth) return false;
        
        return true;
    }
});
</script>
@endsection