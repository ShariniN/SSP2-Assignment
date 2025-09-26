<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Confirmation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div class="bg-green-50 border border-green-200 rounded-md p-6 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-green-800">Order Placed Successfully!</h3>
                        <p class="mt-1 text-sm text-green-700">
                            Thank you for your order. We've received your payment and will process your order shortly.
                        </p>
                    </div>
                </div>
            </div>

            @if(session('order'))
                @php $order = session('order'); @endphp
                
                <!-- Order Details -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
                                <p class="text-sm text-gray-600 mt-1">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Shipping Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Information</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="font-medium text-gray-900">{{ $order->full_name }}</p>
                                    <p class="text-gray-600">{{ $order->email }}</p>
                                    <p class="text-gray-600">{{ $order->phone }}</p>
                                    <div class="mt-2 text-gray-600">
                                        {{ $order->address }}<br>
                                        {{ $order->city }}, {{ $order->state }} {{ $order->zip_code }}<br>
                                        {{ $order->country }}
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">Payment Method:</span>
                                        <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">Payment Status:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center font-medium text-gray-900">
                                        <span>Total Paid:</span>
                                        <span>{{ $order->formatted_total }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Items</h3>
                            <div class="flow-root">
                                <ul role="list" class="-my-4 divide-y divide-gray-200">
                                    @foreach($order->items as $item)
                                        <li class="flex py-4">
                                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                                <img src="{{ $item->product->image ?? 'https://via.placeholder.com/64x64/F3F4F6/9CA3AF?text=Product' }}" 
                                                     alt="{{ $item->name }}" 
                                                     class="h-full w-full object-cover object-center">
                                            </div>
                                            
                                            <div class="ml-4 flex flex-1 flex-col">
                                                <div>
                                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                                        <h4>
                                                            <a href="{{ route('product.details', $item->product_id) }}" 
                                                               class="hover:text-indigo-600">{{ $item->name }}</a>
                                                        </h4>
                                                        <p class="ml-4">{{ $item->formatted_total }}</p>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500">{{ $item->formatted_price }} × {{ $item->quantity }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="max-w-md ml-auto">
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span class="text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Shipping:</span>
                                        <span class="text-gray-900">{{ $order->shipping > 0 ? '$' . number_format($order->shipping, 2) : 'Free' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tax:</span>
                                        <span class="text-gray-900">${{ number_format($order->tax, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-medium">
                                        <span class="text-gray-900">Total:</span>
                                        <span class="text-gray-900">{{ $order->formatted_total }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('products.index') }}" 
                               class="bg-white border border-gray-300 rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-center">
                                Continue Shopping
                            </a>
                            @auth
                                <a href="{{ route('dashboard') }}" 
                                   class="bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-center">
                                    View Order History
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- What's Next -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-md p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">What happens next?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100">
                                    <span class="text-sm font-medium text-blue-600">1</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-blue-900">Order Processing</h4>
                                <p class="mt-1 text-xs text-blue-700">We'll prepare your order for shipment within 1-2 business days.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100">
                                    <span class="text-sm font-medium text-blue-600">2</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-blue-900">Shipping</h4>
                                <p class="mt-1 text-xs text-blue-700">You'll receive tracking information once your order ships.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100">
                                    <span class="text-sm font-medium text-blue-600">3</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-blue-900">Delivery</h4>
                                <p class="mt-1 text-xs text-blue-700">Your order will arrive within 3-5 business days.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success') && !session('order'))
                <!-- Fallback success message if order data is not available -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-16 w-16 text-green-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Thank You!</h2>
                        <p class="text-gray-600 mb-6">{{ session('success') }}</p>
                        <div class="space-x-4">
                            <a href="{{ route('products.index') }}" 
                               class="bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>