@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Checkout</h1>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Your Cart</h2>
        @foreach($cartItems as $item)
        <div class="flex justify-between border-b py-2">
            <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
            <span>${{ $item->product->price * $item->quantity }}</span>
        </div>
        @endforeach
        <div class="flex justify-between font-bold mt-4">
            <span>Total:</span>
            <span>${{ $total }}</span>
        </div>
        <form method="POST" action="{{ route('checkout.process') }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Place Order</button>
        </form>
    </div>
</div>
@endsection
