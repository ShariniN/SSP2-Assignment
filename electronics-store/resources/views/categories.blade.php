<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Electronics Categories
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">Electronics Store</h1>
                <p class="text-xl md:text-2xl mb-8 opacity-90">Discover the Latest in Technology</p>
                
                <!-- Search Bar -->
                <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="flex rounded-lg overflow-hidden shadow-lg">
                        <input type="text" 
                               name="q" 
                               placeholder="Search for electronics, brands, models..." 
                               value="{{ request('q') }}"
                               class="flex-1 px-6 py-4 text-gray-900 focus:outline-none">
                        <button type="submit" 
                                class="bg-yellow-500 hover:bg-yellow-600 px-8 py-4 font-semibold text-gray-900 transition duration-200">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <!-- Categories Grid -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Shop by Category</h2>
                
                @if($categories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($categories as $category)
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden">
                        <a href="{{ route('categories.products', $category->id) }}" class="block">
                            <!-- Category Image -->
                            <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                @if($category->image)
                                    <img src="{{ $category->image }}" 
                                         alt="{{ $category->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <!-- Default Electronics Icons based on category name -->
                                    <div class="flex items-center justify-center h-full">
                                        @if(Str::contains(strtolower($category->name), ['phone', 'mobile', 'smartphone']))
                                            <svg class="w-20 h-20 text-gray-400 group-hover:text-blue-500 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z"></path>
                                            </svg>
                                        @elseif(Str::contains(strtolower($category->name), ['laptop', 'computer', 'pc']))
                                            <svg class="w-20 h-20 text-gray-400 group-hover:text-blue-500 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        @elseif(Str::contains(strtolower($category->name), ['tv', 'television', 'monitor']))
                                            <svg class="w-20 h-20 text-gray-400 group-hover:text-blue-500 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        @elseif(Str::contains(strtolower($category->name), ['headphone', 'audio', 'speaker']))
                                            <svg class="w-20 h-20 text-gray-400 group-hover:text-blue-500 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 11.293c0 1.519-.232 2.979-.64 4.335M12 6.453v11.094m0-11.094c-1.87-.4-3.971-.4-5.536 0M12 6.453c1.87-.4 3.971-.4 5.536 0M7.072 15.628c-.408-1.356-.64-2.816-.64-4.335"></path>
                                            </svg>
                                        @elseif(Str::contains(strtolower($category->name), ['game', 'gaming']))
                                            <svg class="w-20 h-20 text-gray-400 group-hover:text-blue-500 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V5a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                                            </svg>
                                        @elseif(Str::contains(strtolower($category->name), ['camera', 'photo']))
                                            <svg class="w-20 h-20 text-gray-400 group-hover:text-blue-500 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-20 h-20 text-gray-400 group-hover:text-blue-500 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Product Count Badge -->
                                <div class="absolute top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $category->products_count }} {{ Str::plural('item', $category->products_count) }}
                                </div>
                            </div>
                            
                            <!-- Category Info -->
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition duration-200">
                                    {{ $category->name }}
                                </h3>
                                @if($category->description)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {{ $category->description }}
                                    </p>
                                @endif
                                
                                <!-- View Products Button -->
                                <div class="flex items-center justify-between">
                                    <span class="text-blue-600 font-medium group-hover:text-blue-800 transition duration-200">
                                        View Products
                                    </span>
                                    <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-800 group-hover:translate-x-1 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- No Categories -->
                <div class="text-center py-16">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">No Categories Available</h3>
                    <p class="text-gray-600">Categories will appear here once they are added.</p>
                </div>
                @endif
            </div>

            <!-- Featured Section -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-12">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Why Shop With Us?</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Fast Delivery</h4>
                        <p class="text-gray-600">Free shipping on orders over $100. Same-day delivery available in select areas.</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Warranty Protection</h4>
                        <p class="text-gray-600">All products come with manufacturer warranty. Extended protection plans available.</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.944a11.955 11.955 0 018.618 3.04A12.02 12.02 0 0121 9c0 5.591-3.824 10.29-9 11.622C6.824 19.29 3 14.591 3 9a12.02 12.02 0 01.382-2.016A11.955 11.955 0 0112 2.944z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">24/7 Support</h4>
                        <p class="text-gray-600">Expert technical support available around the clock to help with your purchases.</p>
                    </div>
                </div>
            </div>

            <!-- Newsletter Signup -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl text-white p-8 text-center">
                <h3 class="text-2xl font-bold mb-4">Stay Updated with Latest Tech</h3>
                <p class="mb-6 opacity-90">Subscribe to get notifications about new arrivals, exclusive deals, and tech reviews.</p>
                
                <form action="{{ route('newsletter.subscribe') ?? '#' }}" method="POST" class="max-w-md mx-auto flex">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="Enter your email"
                           required
                           class="flex-1 px-4 py-3 rounded-l-lg text-gray-900 focus:outline-none">
                    <button type="submit" 
                            class="bg-yellow-500 hover:bg-yellow-600 px-6 py-3 font-semibold text-gray-900 rounded-r-lg transition duration-200">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add these styles for line-clamp if not already available -->
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>