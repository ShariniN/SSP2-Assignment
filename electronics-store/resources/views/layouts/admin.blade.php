@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md hidden md:block">
        <div class="p-6">
            <h2 class="text-xl font-bold mb-6">Admin Panel</h2>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-2 rounded hover:bg-blue-50 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 font-semibold' : '' }}">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>

                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center px-4 py-2 rounded hover:bg-blue-50 {{ request()->routeIs('admin.products.*') ? 'bg-blue-100 font-semibold' : '' }}">
                    <i class="fas fa-box mr-3"></i> Products
                </a>

                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center px-4 py-2 rounded hover:bg-blue-50 {{ request()->routeIs('admin.categories.*') ? 'bg-blue-100 font-semibold' : '' }}">
                    <i class="fas fa-layer-group mr-3"></i> Categories
                </a>

                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center px-4 py-2 rounded hover:bg-blue-50 {{ request()->routeIs('admin.orders.*') ? 'bg-blue-100 font-semibold' : '' }}">
                    <i class="fas fa-shopping-cart mr-3"></i> Orders
                </a>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-2 rounded hover:bg-blue-50 {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 font-semibold' : '' }}">
                    <i class="fas fa-users mr-3"></i> Users
                </a>

                <a href="{{ route('admin.wishlists.index') }}" 
                   class="flex items-center px-4 py-2 rounded hover:bg-blue-50 {{ request()->routeIs('admin.wishlists.*') ? 'bg-blue-100 font-semibold' : '' }}">
                    <i class="fas fa-heart mr-3"></i> Wishlists
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
        @yield('admin-content')
    </main>

</div>
@endsection
