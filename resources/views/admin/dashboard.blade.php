@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('admin-content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Welcome, Admin!</h1>
    <p class="text-gray-600 mt-2">Here's an overview of your store's performance.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-box text-2xl text-blue-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_products'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-layer-group text-2xl text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Categories</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_categories'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-shopping-cart text-2xl text-purple-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_orders'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_users'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-heart text-2xl text-red-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Wishlists</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_wishlists'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Orders</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_orders'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('admin.products.index') }}" class="bg-white shadow p-6 rounded-lg hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Products</h2>
                <p class="text-gray-600">Manage all products</p>
            </div>
            <i class="fas fa-box text-3xl text-blue-600"></i>
        </div>
    </a>

    <a href="{{ route('admin.categories.index') }}" class="bg-white shadow p-6 rounded-lg hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Categories</h2>
                <p class="text-gray-600">Manage product categories</p>
            </div>
            <i class="fas fa-layer-group text-3xl text-green-600"></i>
        </div>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="bg-white shadow p-6 rounded-lg hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Orders</h2>
                <p class="text-gray-600">View and manage orders</p>
            </div>
            <i class="fas fa-shopping-cart text-3xl text-purple-600"></i>
        </div>
    </a>

    <a href="{{ route('admin.users.index') }}" class="bg-white shadow p-6 rounded-lg hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Users</h2>
                <p class="text-gray-600">View registered users</p>
            </div>
            <i class="fas fa-users text-3xl text-yellow-600"></i>
        </div>
    </a>

    <a href="{{ route('admin.wishlists.index') }}" class="bg-white shadow p-6 rounded-lg hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Wishlists</h2>
                <p class="text-gray-600">View user wishlists</p>
            </div>
            <i class="fas fa-heart text-3xl text-red-600"></i>
        </div>
    </a>
</div>

<!-- Recent Orders and Top Products -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Orders -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
        </div>
        <div class="p-6">
            @if($stats['recent_orders']->count() > 0)
                <div class="space-y-4">
                    @foreach($stats['recent_orders'] as $order)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">#{{ $order->id }}</p>
                            <p class="text-sm text-gray-600">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No recent orders</p>
            @endif
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Top Products</h3>
        </div>
        <div class="p-6">
            @if($stats['top_products']->count() > 0)
                <div class="space-y-4">
                    @foreach($stats['top_products'] as $product)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-300 rounded flex items-center justify-center">
                                    <i class="fas fa-image text-gray-500"></i>
                                </div>
                            @endif
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600">${{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">{{ $product->order_items_count }} orders</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No product data available</p>
            @endif
        </div>
    </div>
</div>
@endsection