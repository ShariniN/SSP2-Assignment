<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ElectroStore - Premium Electronics & Gadgets')</title>
    <meta name="description" content="@yield('description', 'Discover the latest tech and premium electronics at ElectroStore. Shop laptops, smartphones, headphones, gaming gear and more with free shipping and warranty.')">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- Navigation -->
    <nav x-data="{ open: false, searchOpen: false }" class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">

                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-2 rounded-xl group-hover:scale-105 transition-transform">
                            <i class="fas fa-bolt text-white text-xl"></i>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                ElectroStore
                            </h1>
                            <p class="text-xs text-gray-500 -mt-1">Electronics & Gadgets</p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-2">
                    <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')">
                        <i class="fas fa-th-large mr-2"></i>All Products
                    </x-nav-link>

                    <!-- Categories Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center transition">
                            <i class="fas fa-layer-group mr-2"></i>Categories
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                            @foreach($categories ?? [] as $category)
                                <a href="{{ route('products.index', ['category' => $category->slug ?? $category->id]) }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-{{ $category->icon ?? 'microchip' }} mr-3 text-blue-500"></i>{{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Search (Livewire Desktop) -->
                <div class="hidden md:flex flex-1 max-w-lg mx-8">
                    <livewire:product-search />
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-4">

                    <!-- Wishlist -->
                    @auth
                        @php
                            $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
                        @endphp
                        <a href="{{ route('wishlist.index') }}" class="relative p-2 text-gray-600 hover:text-blue-600 flex items-center">
                            <i class="fas fa-heart text-lg"></i>
                            @if($wishlistCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $wishlistCount }}
                                </span>
                            @endif
                        </a>
                    @endauth

                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>

                    <!-- Admin -->
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="hidden lg:flex items-center px-3 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 font-medium">
                            <i class="fas fa-cog mr-2"></i>Admin
                        </a>
                    @endif

                    <!-- User -->
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block font-medium">{{ Str::limit(Auth::user()->name, 10) }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-user mr-3"></i>Profile Settings
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600">
                                        <i class="fas fa-sign-out-alt mr-3"></i>Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium">Sign In</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700">Sign Up</a>
                        </div>
                    @endauth

                    <!-- Mobile menu toggle -->
                    <button @click="open = !open" class="lg:hidden p-2 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                </div>
            </div>

            <!-- Mobile Search (Livewire) -->
            <div x-show="searchOpen" x-transition class="md:hidden px-4 pb-4">
                <livewire:product-search />
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Back to Top -->
    <button id="back-to-top" class="fixed bottom-8 right-8 bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-3 rounded-full shadow-lg hover:scale-110 transform hidden z-40">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Livewire Scripts -->
    @livewireScripts

    @stack('scripts')

    <script>
        // Back to top
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            backToTop.classList.toggle('hidden', window.pageYOffset < 300);
        });
        backToTop.addEventListener('click', () => window.scrollTo({top: 0, behavior: 'smooth'}));
    </script>
</body>
</html>
