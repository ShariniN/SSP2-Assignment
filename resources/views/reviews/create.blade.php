@extends('layouts.app')

@section('title', 'Write a Review - ' . $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('product.show', $product->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Product
        </a>

        <!-- Product Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center gap-4">
                @if($product->image_url)
                    <img src="{{ asset('storage/' . $product->image_url) }}" 
                         alt="{{ $product->name }}" 
                         class="w-20 h-20 object-cover rounded-lg">
                @else
                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $product->name }}</h2>
                    <p class="text-gray-600">{{ $product->category->name ?? 'Uncategorized' }}</p>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Write Your Review</h1>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('reviews.store', $product->id) }}" method="POST">
                @csrf

                <!-- Rating -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Rating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1" id="star-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        class="star text-3xl text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors" 
                                        data-rating="{{ $i }}">
                                    ★
                                </button>
                            @endfor
                        </div>
                        <span id="rating-text" class="text-gray-600 ml-2"></span>
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                    @error('rating')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comment -->
                <div class="mb-6">
                    <label for="comment" class="block text-gray-700 font-semibold mb-2">
                        Your Review <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="comment" 
                        id="comment" 
                        rows="6" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Share your experience with this product... (minimum 10 characters)"
                        required>{{ old('comment') }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">
                        <span id="char-count">0</span> / 1000 characters
                    </p>
                    @error('comment')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button 
                        type="submit" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Submit Review
                    </button>
                    <a 
                        href="{{ route('product.show', $product->id) }}" 
                        class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating-input');
    const ratingText = document.getElementById('rating-text');
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');
    
    // Rating labels
    const ratingLabels = {
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent'
    };
    
    // Initialize rating if old value exists
    if (ratingInput.value) {
        updateStars(parseInt(ratingInput.value));
    }
    
    // Star rating functionality
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            updateStars(rating);
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });
    });
    
    document.getElementById('star-rating').addEventListener('mouseleave', function() {
        const currentRating = parseInt(ratingInput.value) || 0;
        updateStars(currentRating);
    });
    
    function highlightStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
        ratingText.textContent = rating > 0 ? ratingLabels[rating] : '';
    }
    
    function updateStars(rating) {
        highlightStars(rating);
    }
    
    // Character counter
    commentTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 1000) {
            charCount.classList.add('text-red-500');
        } else {
            charCount.classList.remove('text-red-500');
        }
    });
    
    // Initialize character count
    charCount.textContent = commentTextarea.value.length;
});
</script>
@endsection