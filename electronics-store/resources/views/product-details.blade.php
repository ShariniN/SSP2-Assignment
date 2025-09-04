@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
        <p class="text-gray-700 mb-2">{{ $product->description }}</p>
        <p class="text-2xl font-bold mb-4">${{ $product->price }}</p>
        <form action="{{ route('cart.add', $product->id) }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Add to Cart</button>
        </form>
    </div>
</div>
@endsection
