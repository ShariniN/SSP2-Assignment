@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Featured Products</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($products as $product)
        <div class="border rounded-lg p-4 bg-white shadow hover:shadow-lg">
            <h3 class="text-xl font-semibold">{{ $product->name }}</h3>
            <p class="text-gray-600">{{ $product->description }}</p>
            <p class="text-lg font-bold mt-2">${{ $product->price }}</p>
            <a href="{{ route('product.details', $product->id) }}" class="inline-block mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">View</a>
        </div>
        @endforeach
    </div>
</div>
@endsection
