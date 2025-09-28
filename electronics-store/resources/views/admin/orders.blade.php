@extends('layouts.admin')

@section('title', 'Orders Management')

@section('admin-content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
            <p class="text-gray-600 mt-2">View and manage customer orders</p>
        </div>
        <div class="flex space-x-2">
            <select id="statusFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Orders</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    {{ session('error') }}
</div>
@endif

<!-- Orders Table -->
<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">All Orders ({{ $orders->total() }})</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                        <div class="text-sm text-gray-500">{{ $order->order_number ?? 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ $order->items->count() }} items
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${{ number_format($order->total_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $order->created_at->format('M d, Y') }}
                        <div class="text-xs text-gray-400">{{ $order->created_at->format('H:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewOrder({{ $order->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="updateStatus({{ $order->id }}, '{{ $order->status }}')" class="text-green-600 hover:text-green-900 mr-3" title="Update Status">
                            <i class="fas fa-edit"></i>
                        </button>
                        @if($order->status === 'cancelled')
                        <button onclick="deleteOrder({{ $order->id }})" class="text-red-600 hover:text-red-900" title="Delete Order">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No orders found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $orders->links() }}
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="orderModalTitle">Order Details</h3>
                <button id="closeOrderModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="orderDetails" class="space-y-4">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Order Status</h3>
                <button id="closeStatusModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="statusForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="orderStatus" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelStatusBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const orderModal = document.getElementById('orderModal');
const statusModal = document.getElementById('statusModal');
const statusForm = document.getElementById('statusForm');

// View Order Details
function viewOrder(id) {
    fetch(`/admin/orders/${id}`)
        .then(response => response.json())
        .then(order => {
            document.getElementById('orderModalTitle').textContent = `Order #${order.id} Details`;
            
            const detailsHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Customer Information</h4>
                        <p><strong>Name:</strong> ${order.user.name}</p>
                        <p><strong>Email:</strong> ${order.user.email}</p>
                        <p><strong>Phone:</strong> ${order.phone || 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Order Information</h4>
                        <p><strong>Status:</strong> <span class="px-2 py-1 rounded text-xs ${getStatusClass(order.status)}">${order.status.toUpperCase()}</span></p>
                        <p><strong>Total:</strong> $${parseFloat(order.total_amount).toFixed(2)}</p>
                        <p><strong>Date:</strong> ${new Date(order.created_at).toLocaleDateString()}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Shipping Address</h4>
                    <p>${order.shipping_address || 'No shipping address provided'}</p>
                </div>
                
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Order Items</h4>
                    <div class="space-y-2">
                        ${order.items.map(item => `
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <img src="${item.product.image ? '/storage/' + item.product.image : '/images/placeholder.png'}" 
                                         alt="${item.product.name}" class="w-12 h-12 object-cover rounded mr-3">
                                    <div>
                                        <p class="font-medium">${item.product.name}</p>
                                        <p class="text-sm text-gray-500">Qty: ${item.quantity}</p>
                                    </div>
                                </div>
                                <p class="font-medium">$${parseFloat(item.price * item.quantity).toFixed(2)}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            
            document.getElementById('orderDetails').innerHTML = detailsHTML;
            orderModal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading order details');
        });
}

// Update Status
function updateStatus(id, currentStatus) {
    statusForm.action = `/admin/orders/${id}/status`;
    document.getElementById('orderStatus').value = currentStatus;
    statusModal.classList.remove('hidden');
}

// Delete Order
function deleteOrder(id) {
    if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/orders/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Helper function for status classes
function getStatusClass(status) {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Close Modals
document.getElementById('closeOrderModal').addEventListener('click', () => {
    orderModal.classList.add('hidden');
});

document.getElementById('closeStatusModal').addEventListener('click', () => {
    statusModal.classList.add('hidden');
});

document.getElementById('cancelStatusBtn').addEventListener('click', () => {
    statusModal.classList.add('hidden');
});

// Close modals when clicking outside
orderModal.addEventListener('click', function(e) {
    if (e.target === orderModal) {
        orderModal.classList.add('hidden');
    }
});

statusModal.addEventListener('click', function(e) {
    if (e.target === statusModal) {
        statusModal.classList.add('hidden');
    }
});
</script>
@endsection