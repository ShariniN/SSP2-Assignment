<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ElectroStore')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-50">

    {{-- NAVBAR --}}
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-bolt text-blue-600"></i> ElectroStore
                    </a>
                </div>

                {{-- Links --}}
                <div class="hidden space-x-8 sm:flex sm:ms-10">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.index')">
                        {{ __('Cart') }}
                        @if(isset($cartCount) && $cartCount > 0)
                            <span class="ml-1 text-red-500 font-bold">({{ $cartCount }})</span>
                        @endif
                    </x-nav-link>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin') }}
                        </x-nav-link>
                    @endif
                </div>

                {{-- User Dropdown / Auth Links --}}
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @auth
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border text-sm rounded-md text-gray-500 bg-white hover:text-gray-700">
                                    {{ Auth::user()->name }}
                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link>
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="flex space-x-4">
                            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Login</a>
                            <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900">Register</a>
                        </div>
                    @endauth
                </div>

                {{-- Hamburger --}}
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- Responsive Menu --}}
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.index')">{{ __('Cart') }}</x-responsive-nav-link>
            @if(Auth::check() && Auth::user()->role === 'admin')
                <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">{{ __('Admin') }}</x-responsive-nav-link>
            @endif
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="py-6">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
