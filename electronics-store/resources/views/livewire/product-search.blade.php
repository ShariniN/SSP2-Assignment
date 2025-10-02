<div class="relative w-full" x-data="{ showResults: false }">
    <!-- Search Input -->
    <input
        type="text"
        wire:model.live.debounce.300ms="query"
        wire:keyup="search"
        @focus="showResults = true"
        @click.outside="showResults = false"
        placeholder="Search for products..."
        class="w-full px-4 py-3 pl-12 pr-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition"
        autocomplete="off"
    />

    <!-- Search Icon -->
    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
        <i class="fas fa-search text-gray-400"></i>
    </div>

    <!-- Loading Spinner -->
    <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 flex items-center pr-4">
        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- Results Dropdown -->
    @if(strlen($query ?? '') >= 2)
        <div x-show="showResults" 
             x-transition
             class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl max-h-96 overflow-y-auto">
            
            @if(!empty($results) && count($results) > 0)
                <!-- Results List -->
                @foreach($results as $product)
                    <a href="{{ route('product.show', $product['id']) }}" 
                       class="flex items-center gap-3 p-3 border-b border-gray-100 hover:bg-blue-50 transition-colors group last:border-b-0"
                       @click="showResults = false">
                        
                        <!-- Product Image -->
                        @if(!empty($product['image_url']))
                            <img src="{{ asset($product->image_url) }}"
                                 alt="{{ $product['name'] }}" 
                                 class="w-14 h-14 object-cover rounded-lg flex-shrink-0 border border-gray-200"
                                 onerror="this.parentElement.innerHTML='<div class=\'w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center\'><i class=\'fas fa-image text-gray-400\'></i></div>'">
                        @else
                            <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-mobile-alt text-gray-400 text-xl"></i>
                            </div>
                        @endif
                        
                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate group-hover:text-blue-600">
                                {{ $product['name'] }}
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                @if(!empty($product['sku']))
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                        SKU: {{ $product['sku'] }}
                                    </span>
                                @endif
                                @if($product['discount_price'])
                                    <span class="text-sm font-semibold text-blue-600">
                                        ${{ number_format($product['discount_price'], 2) }}
                                    </span>
                                    <span class="text-xs text-gray-400 line-through">
                                        ${{ number_format($product['price'], 2) }}
                                    </span>
                                @else
                                    <span class="text-sm font-semibold text-blue-600">
                                        ${{ number_format($product['price'], 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Arrow Icon -->
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 flex-shrink-0 transition-colors"></i>
                    </a>
                @endforeach
            @else
                <!-- No Results -->
                <div class="p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-search text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">No products found</p>
                    <p class="text-sm text-gray-500 mt-1">Try searching for something else</p>
                </div>
            @endif
        </div>
    @endif
</div>