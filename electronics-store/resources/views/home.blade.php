<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronics Store - Latest Tech & Gadgets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-bolt text-blue-600"></i>
                        ElectroStore
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="hidden md:block flex-1 max-w-lg mx-8">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Search for products..." 
                               class="w-full px-4 py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ request('q') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-arrow-right text-gray-400 hover:text-blue-600"></i>
                        </button>
                    </form>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-blue-600 transition duration-200">
                        <i class="fas fa-th-large mr-1"></i> Products
                    </a>
                    <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-blue-600 transition duration-200 relative">
                        <i class="fas fa-shopping-cart mr-1"></i> Cart
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 transition duration-200">
                            <i class="fas fa-user mr-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 transition duration-200">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 text-center">
            {{ session('success') }}
        </div>
    @endif

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Welcome to Electronics Store 🚀
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-200">
                    Discover the latest tech and gadgets at unbeatable prices
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#featured-products" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-200">
                        Shop Now
                    </a>
                    <a href="{{ route('products.index') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-200">
                        View All Products
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    @if($categories->count() > 0)
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Shop by Category</h2>
                <p class="text-gray-600">Find exactly what you're looking for</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('category', $category->slug) }}" class="group">
                        <div class="bg-gray-50 rounded-lg p-6 text-center hover:bg-blue-50 transition duration-200 card-hover">
                            <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition duration-200">
                                <i class="fas fa-{{ $category->icon ?? 'microchip' }} text-2xl text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition duration-200">
                                {{ $category->name }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $category->products_count }} products
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Products Section -->
    <section id="featured-products" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Featured Products</h2>
                <p class="text-gray-600">Discover our latest and most popular items</p>
            </div>

            @if($featuredProducts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover">
                            <div class="relative">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                    </div>
                                @endif
                                
                                @if($product->discount_price > 0)
                                    <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-semibold">
                                        Sale
                                    </div>
                                @endif
                                
                                @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                    <div class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 rounded text-sm">
                                        Low Stock
                                    </div>
                                @elseif($product->stock_quantity == 0)
                                    <div class="absolute top-2 right-2 bg-gray-500 text-white px-2 py-1 rounded text-sm">
                                        Out of Stock
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                    {{ $product->description }}
                                </p>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        @if($product->discount_price > 0)
                                            <span class="text-lg font-bold text-red-600">
                                                ${{ number_format($product->discount_price, 2) }}
                                            </span>
                                            <span class="text-sm text-gray-500 line-through ml-1">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                        @else
                                            <span class="text-lg font-bold text-gray-800">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('products.show', $product->id) }}" 
                                       class="flex-1 bg-gray-100 text-gray-800 text-center py-2 rounded-md hover:bg-gray-200 transition duration-200 text-sm">
                                        View Details
                                    </a>
                                    @if($product->stock_quantity > 0)
                                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" 
                                                    class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm">
                                                <i class="fas fa-cart-plus mr-1"></i> Add to Cart
                                            </button>
                                        </form>
                                    @else
                                        <button disabled 
                                                class="flex-1 bg-gray-300 text-gray-500 py-2 rounded-md cursor-not-allowed text-sm">
                                            Out of Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No products available</h3>
                    <p class="text-gray-500">Check back soon for new arrivals!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Best Sellers Section -->
    @if($bestSellers->count() > 0)
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Best Sellers</h2>
                <p class="text-gray-600">Most popular products this month</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($bestSellers as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover border border-gray-200">
                        <div class="relative">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            
                            <div class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded text-sm font-semibold">
                                <i class="fas fa-star mr-1"></i> Best Seller
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">{{ $product->name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-800">
                                    ${{ number_format($product->price, 2) }}
                                </span>
                                @if($product->stock_quantity > 0)
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" 
                                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Newsletter Section -->
    <section class="py-16 bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold mb-4">Stay Updated</h2>
                <p class="text-gray-300 mb-8">Get the latest deals and product updates delivered to your inbox</p>
                
                <form class="max-w-md mx-auto flex gap-4">
                    <input type="email" placeholder="Enter your email address" 
                           class="flex-1 px-4 py-3 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">
                        <i class="fas fa-bolt text-blue-600"></i>
                        ElectroStore
                    </h3>
                    <p class="text-gray-400 mb-4">Your trusted partner for all electronics and tech gadgets.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition duration-200">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('products.index') }}" class="text-gray-400 hover:text-white transition duration-200">All Products</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-200">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-200">Contact</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-200">FAQ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Customer Service</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-200">Shipping Info</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-200">Returns</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-200">Warranty</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-200">Support</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@electrostore.com</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> 123 Tech Street, Digital City</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} ElectroStore. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>