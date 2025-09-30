@extends('layouts.admin')

@section('title', 'Users Management')

@section('admin-content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
        <p class="text-gray-600 mt-2">View and manage registered users</p>
    </div>
    <div>
        <select id="statusFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All Users</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
        </select>
    </div>
</div>

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
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                        <button onclick="viewUser({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="toggleUserStatus({{ $user->id }})" class="text-{{ $user->banned_at ? 'green' : 'yellow' }}-600 hover:text-{{ $user->banned_at ? 'green' : 'yellow' }}-900" title="{{ $user->banned_at ? 'Activate' : 'Ban' }} User">
                            <i class="fas fa-{{ $user->banned_at ? 'check' : 'ban' }}"></i>
                        </button>
                        @if($user->orders_count === 0)
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

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="userModalTitle">User Details</h3>
                <button id="closeUserModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="userDetails" class="space-y-4"></div>
        </div>
    </div>
</div>

<script>
const userModal = document.getElementById('userModal');

function viewUser(id) {
    fetch(`/admin/users/${id}/json`)
        .then(res => res.json())
        .then(user => {
            document.getElementById('userModalTitle').textContent = `${user.name} - User Details`;
            let detailsHTML = `
                <div class="flex items-center mb-6">
                    ${user.profile_photo_path ? `<img src="/storage/${user.profile_photo_path}" class="w-20 h-20 object-cover rounded-full">`
                    : `<div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center">
                           <i class="fas fa-user text-gray-500 text-2xl"></i>
                       </div>`}
                    <div class="ml-6">
                        <h2 class="text-xl font-semibold text-gray-900">${user.name}</h2>
                        <p class="text-gray-600">${user.email}</p>
                        <div class="mt-2 flex space-x-2">
                            ${user.email_verified_at ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Email Verified</span>'
                            : '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Email Unverified</span>'}
                            ${user.banned_at ? '<span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Banned</span>'
                            : '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>'}
                        </div>
                    </div>
                </div>`;
            document.getElementById('userDetails').innerHTML = detailsHTML;
            userModal.classList.remove('hidden');
        });
}

function toggleUserStatus(id) {
    if(confirm('Are you sure you want to toggle this user\'s status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${id}/toggle-status`;
        form.innerHTML = `@csrf @method('PATCH')`;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteUser(id) {
    if(confirm('Are you sure you want to delete this user?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${id}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
}

document.getElementById('closeUserModal').addEventListener('click', () => userModal.classList.add('hidden'));
userModal.addEventListener('click', e => { if(e.target === userModal) userModal.classList.add('hidden'); });

document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    let url = new URL(window.location);
    if(status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    window.location.href = url.toString();
});
</script>
@endsection
