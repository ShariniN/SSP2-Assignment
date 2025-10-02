@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">My Wishlist</h1>

    @if($items->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($items as $item)
                @php $product = $item->product; @endphp
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition duration-200">
                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-t-xl">
                    <div class="p-4">
                        <h2 class="text-lg font-semibold mb-2">{{ $product->name }}</h2>
                        <p class="text-gray-600 text-sm mb-2">
                            {{ \Illuminate\Support\Str::limit($product->description, 60) }}
                        </p>
                        <p class="text-xl font-bold text-blue-600 mb-4">
                            ${{ number_format($product->price, 2) }}
                        </p>

                        <div class="flex gap-3">
                            <form action="{{ route('wishlist.remove', $product->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex-1 bg-red-500 text-white py-2 rounded-lg font-medium hover:opacity-90 text-sm shadow">
                                    <i class="fas fa-heart mr-1"></i>Remove
                                </button>
                            </form>

                            <button wire:click.prevent="$emit('addToCart', {{ $product->id }})"
                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-2 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 text-sm shadow hover:shadow-lg">
                                <i class="fas fa-cart-plus mr-1"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600">Your wishlist is empty.</p>
    @endif
</div>
@endsection
