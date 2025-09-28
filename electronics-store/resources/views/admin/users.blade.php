@extends('layouts.admin')

@section('title', 'Users Management')

@section('admin-content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
            <p class="text-gray-600 mt-2">View and manage registered users</p>
        </div>
        <div class="flex space-x-2">
            <select id="statusFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Users</option>
                <option value="active">Active</option>
                <option value="banned">Banned</option>
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

<!-- Users Table -->
<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">All Users ({{ $users->total() }})</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Verified</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-12 h-12 object-cover rounded-full">
                            @else
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                            @endif
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Unverified
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ $user->orders_count ?? 0 }} orders
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->banned_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-ban mr-1"></i>
                                Banned
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Active
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                        <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewUser({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="toggleUserStatus({{ $user->id }})" class="text-{{ $user->banned_at ? 'green' : 'yellow' }}-600 hover:text-{{ $user->banned_at ? 'green' : 'yellow' }}-900 mr-3" title="{{ $user->banned_at ? 'Activate' : 'Ban' }} User">
                            <i class="fas fa-{{ $user->banned_at ? 'check' : 'ban' }}"></i>
                        </button>
                        @if($user->orders->count() === 0)
                        <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900" title="Delete User">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
</div>

<!-- User Details Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="userModalTitle">User Details</h3>
                <button id="closeUserModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="userDetails" class="space-y-4">
                <!-- User details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
const userModal = document.getElementById('userModal');

// View User Details
function viewUser(id) {
    fetch(`/admin/users/${id}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('userModalTitle').textContent = `${user.name} - User Details`;
            
            const detailsHTML = `
                <div class="flex items-center mb-6">
                    ${user.profile_photo_path 
                        ? `<img src="/storage/${user.profile_photo_path}" alt="${user.name}" class="w-20 h-20 object-cover rounded-full">`
                        : `<div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center">
                             <i class="fas fa-user text-gray-500 text-2xl"></i>
                           </div>`
                    }
                    <div class="ml-6">
                        <h2 class="text-xl font-semibold text-gray-900">${user.name}</h2>
                        <p class="text-gray-600">${user.email}</p>
                        <div class="mt-2 flex space-x-2">
                            ${user.email_verified_at 
                                ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Email Verified</span>'
                                : '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Email Unverified</span>'
                            }
                            ${user.banned_at 
                                ? '<span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Banned</span>'
                                : '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>'
                            }
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Account Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>User ID:</strong> ${user.id}</p>
                            <p><strong>Phone:</strong> ${user.phone || 'Not provided'}</p>
                            <p><strong>Date of Birth:</strong> ${user.date_of_birth || 'Not provided'}</p>
                            <p><strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                            <p><strong>Last Login:</strong> ${user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never'}</p>
                            ${user.banned_at ? `<p><strong>Banned On:</strong> ${new Date(user.banned_at).toLocaleDateString()}</p>` : ''}
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Activity Summary</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Total Orders:</strong> ${user.orders_count || 0}</p>
                            <p><strong>Total Spent:</strong> ${parseFloat(user.total_spent || 0).toFixed(2)}</p>
                            <p><strong>Wishlist Items:</strong> ${user.wishlist_count || 0}</p>
                            <p><strong>Reviews Written:</strong> ${user.reviews_count || 0}</p>
                        </div>
                    </div>
                </div>
                
                ${user.address ? `
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 mb-3">Default Address</h4>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            ${user.address}
                        </div>
                    </div>
                ` : ''}
                
                ${user.recent_orders && user.recent_orders.length > 0 ? `
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 mb-3">Recent Orders</h4>
                        <div class="space-y-2">
                            ${user.recent_orders.map(order => `
                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                    <div>
                                        <span class="font-medium">#${order.id}</span>
                                        <span class="text-sm text-gray-500 ml-2">${new Date(order.created_at).toLocaleDateString()}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-medium">${parseFloat(order.total_amount).toFixed(2)}</span>
                                        <span class="ml-2 px-2 py-1 rounded text-xs ${getOrderStatusClass(order.status)}">${order.status.toUpperCase()}</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            `;
            
            document.getElementById('userDetails').innerHTML = detailsHTML;
            userModal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading user details');
        });
}

// Toggle User Status
function toggleUserStatus(id) {
    const action = confirm('Are you sure you want to toggle this user\'s status?');
    if (action) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${id}/toggle-status`;
        form.innerHTML = `
            @csrf
            @method('PATCH')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete User
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Helper function for order status classes
function getOrderStatusClass(status) {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Close Modal
document.getElementById('closeUserModal').addEventListener('click', () => {
    userModal.classList.add('hidden');
});

// Close modal when clicking outside
userModal.addEventListener('click', function(e) {
    if (e.target === userModal) {
        userModal.classList.add('hidden');
    }
});

// Status Filter
document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    let url = new URL(window.location);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location.href = url.toString();
});
</script>
@endsection