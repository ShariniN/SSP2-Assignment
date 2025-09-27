@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-16 text-center">
    <h1 class="text-3xl font-bold mb-6 text-green-600">Thank you for your order!</h1>
    <p class="mb-4">Your order has been successfully placed.</p>

    @isset($order)
    <p class="mb-2">Order ID: <strong>{{ $order->id }}</strong></p>
    <p>Total: <strong>${{ number_format($order->total, 2) }}</strong></p>
    @endisset

    <a href="{{ route('products.index') }}"
       class="inline-block mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
       Continue Shopping
    </a>
</div>
@endsection
