<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="bg-white rounded-lg shadow-sm px-6 py-4 mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
                    <li class="text-gray-400">/</li>
                    <li><a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">Products</a></li>
                    @if($product->category)
                        <li class="text-gray-400">/</li>
                        <li><a href="{{ route('category.products', $product->category->id) }}" class="text-blue-600 hover:text-blue-800">{{ $product->category->name }}</a></li>
                    @endif
                    <li class="text-gray-400">/</li>
                    <li class="text-gray-600">{{ $product->name }}</li>
                </ol>
            </nav>

            <!-- Product Details -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Product Images -->
                        <div class="space-y-4">
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                @if($product->image)
                                    <img id="main-image" 
                                         src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="space-y-6">
                            <!-- Product Title & Category -->
                            <div>
                                @if($product->category)
                                    <p class="text-sm text-blue-600 font-medium">{{ $product->category->name }}</p>
                                @endif
                                <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>
                                
                                <!-- SKU -->
                                @if($product->sku)
                                    <p class="text-sm text-gray-500 mt-2">SKU: {{ $product->sku }}</p>
                                @endif
                            </div>

                            <!-- Rating & Reviews -->
                            @if($reviewCount > 0)
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($averageRating))
                                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                                </div>
                            @endif

                            <!-- Price -->
                            <div class="space-y-2">
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    <div class="flex items-center space-x-3">
                                        <span class="text-3xl font-bold text-red-600">${{ number_format($product->discount_price, 2) }}</span>
                                        <span class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                        <span class="bg-red-100 text-red-800 text-sm font-semibold px-2 py-1 rounded">
                                            Save ${{ number_format($product->price - $product->discount_price, 2) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>

                            <!-- Stock Status -->
                            <div class="space-y-2">
                                @if($product->stock_quantity > 10)
                                    <p class="text-green-600 font-medium flex items-center">
                                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        In Stock ({{ $product->stock_quantity }} available)
                                    </p>
                                @elseif($product->stock_quantity > 0)
                                    <p class="text-orange-600 font-medium flex items-center">
                                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Low Stock (Only {{ $product->stock_quantity }} left)
                                    </p>
                                @else
                                    <p class="text-red-600 font-medium flex items-center">
                                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Out of Stock
                                    </p>
                                @endif
                            </div>

                            <!-- Description -->
                            <div class="prose max-w-none">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Product Description</h3>
                                <p class="text-gray-700">{{ $product->description }}</p>
                            </div>

                            <!-- Add to Cart Form -->
                            @if($product->stock_quantity > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    
                                    <!-- Quantity Selector -->
                                    <div class="flex items-center space-x-4">
                                        <label for="quantity" class="text-sm font-medium text-gray-700">Quantity:</label>
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <button type="button" onclick="decreaseQuantity()" 
                                                    class="px-3 py-2 text-gray-600 hover:text-gray-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                                                   class="w-16 px-3 py-2 text-center border-0 focus:ring-0">
                                            <button type="button" onclick="increaseQuantity()" 
                                                    class="px-3 py-2 text-gray-600 hover:text-gray-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <button type="submit" 
                                                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"/>
                                            </svg>
                                            Add to Cart
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="space-y-4">
                                    <button disabled 
                                            class="w-full bg-gray-300 text-gray-500 py-3 px-6 rounded-lg font-semibold cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                </div>
                            @endif

                            <!-- Additional Info -->
                            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-200">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Free shipping on orders over $100
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    30-day return policy
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    2-year warranty
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01"/>
                                    </svg>
                                    24/7 customer support
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Tabs -->
                    <div class="mt-16">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button onclick="showTab('specifications')" id="tab-specifications"
                                        class="tab-button border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Specifications
                                </button>
                                <button onclick="showTab('reviews')" id="tab-reviews"
                                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Reviews ({{ $reviewCount }})
                                </button>
                                <button onclick="showTab('shipping')" id="tab-shipping"
                                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Shipping & Returns
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="mt-8">
                            <!-- Specifications Tab -->
                            <div id="content-specifications" class="tab-content">
                                @if(!empty($specifications))
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($specifications as $key => $value)
                                            <div class="flex justify-between py-2 border-b border-gray-100">
                                                <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-gray-600">{{ $value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-600">No specifications available for this product.</p>
                                @endif
                            </div>

                            <!-- Reviews Tab -->
                            <div id="content-reviews" class="tab-content hidden">
                                @if($reviewCount > 0)
                                    <p class="text-gray-600">Reviews will be displayed here.</p>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No reviews yet</h3>
                                        <p class="text-gray-500 mb-4">Be the first to review this product!</p>
                                        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                                            Write a Review
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Shipping Tab -->
                            <div id="content-shipping" class="tab-content hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-4">Shipping Information</h4>
                                        <ul class="space-y-2 text-gray-600">
                                            <li>• Free shipping on orders over $100</li>
                                            <li>• Standard shipping: 3-5 business days</li>
                                            <li>• Express shipping: 1-2 business days</li>
                                            <li>• International shipping available</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-4">Return Policy</h4>
                                        <ul class="space-y-2 text-gray-600">
                                            <li>• 30-day return window</li>
                                            <li>• Items must be in original condition</li>
                                            <li>• Free return shipping</li>
                                            <li>• Full refund or exchange</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Products -->
                    @if($relatedProducts->count() > 0)
                        <div class="mt-16">
                            <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                @foreach($relatedProducts as $relatedProduct)
                                    <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-200">
                                        <div class="relative">
                                            @if($relatedProduct->image)
                                                <img src="{{ asset('storage/' . $relatedProduct->image) }}" 
                                                     alt="{{ $relatedProduct->name }}" 
                                                     class="w-full h-48 object-cover">
                                            @else
                                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            @if($relatedProduct->discount_price && $relatedProduct->discount_price < $relatedProduct->price)
                                                <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-sm">
                                                    Sale
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="p-4">
                                            <h3 class="font-semibold text-gray-800 mb-2">
                                                <a href="{{ route('product.details', $relatedProduct->id) }}" class="hover:text-blue-600">
                                                    {{ $relatedProduct->name }}
                                                </a>
                                            </h3>
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    @if($relatedProduct->discount_price && $relatedProduct->discount_price < $relatedProduct->price)
                                                        <span class="text-lg font-bold text-red-600">
                                                            ${{ number_format($relatedProduct->discount_price, 2) }}
                                                        </span>
                                                        <span class="text-sm text-gray-500 line-through ml-1">
                                                            ${{ number_format($relatedProduct->price, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-lg font-bold text-gray-800">
                                                            ${{ number_format($relatedProduct->price, 2) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($relatedProduct->stock_quantity > 0)
                                                    <form action="{{ route('cart.add', $relatedProduct->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" 
                                                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition duration-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        }

        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            const maxValue = parseInt(quantityInput.getAttribute('max'));
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        }

        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active styles from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById(`content-${tabName}`).classList.remove('hidden');

            // Add active styles to selected tab button
            const selectedButton = document.getElementById(`tab-${tabName}`);
            selectedButton.classList.remove('border-transparent', 'text-gray-500');
            selectedButton.classList.add('border-blue-500', 'text-blue-600');
        }

        // Initialize first tab as active
        document.addEventListener('DOMContentLoaded', function() {
            showTab('specifications');
        });
    </script>
    @endpush
</x-app-layout>