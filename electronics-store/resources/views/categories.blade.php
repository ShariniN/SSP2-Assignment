@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Shop by Categories</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($categories as $category)
        <div class="border rounded-lg p-6 bg-white shadow hover:shadow-lg text-center">
            <h3 class="text-xl font-semibold">{{ $category->name }}</h3>
            <a href="{{ route('category.products', $category->id) }}" class="mt-3 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">View Products</a>
        </div>
        @endforeach
    </div>
</div>
@endsection
