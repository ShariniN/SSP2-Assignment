@extends('layouts.app')

@section('title', 'Home')

@section('content')

<h1 class="text-3xl font-bold mb-6">Latest Products</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($products as $product)
        <div class="bg-white shadow-md rounded p-4 flex flex-col">
            <img src="{{ $product->image ?? 'https://via.placeholder.com/200' }}" alt="{{ $product->name }}" class="h-48 w-full object-cover mb-4 rounded">
            <h2 class="text-xl font-semibold mb-2">{{ $product->name }}</h2>
            <p class="text-gray-600 mb-2">{{ Str::limit($product->description, 50) }}</p>
            <div class="mt-auto flex justify-between items-center">
                <span class="font-bold text-blue-600">${{ $product->price }}</span>
                <a href="#" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-500">Add to Cart</a>
            </div>
        </div>
    @endforeach
</div>

@endsection
