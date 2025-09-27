@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

    @if($cartItems->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Billing Details -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Billing Details</h2>
            <form action="{{ route('checkout.process') }}" method="POST">
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
                        <img src="{{ asset('storage/' . $item->product->image) }}" class="w-16 h-16 object-cover rounded">
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
                <label><input type="radio" name="payment_method" value="cod" checked> Cash on Delivery</label>
                <label><input type="radio" name="payment_method" value="card"> Credit / Debit Card</label>
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
@endsection
