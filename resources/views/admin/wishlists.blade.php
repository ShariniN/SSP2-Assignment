@extends('layouts.admin')

@section('title', 'Wishlists Management')

@section('admin-content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Wishlists Management</h1>
        <p class="text-gray-600 mt-2">View and manage user wishlists</p>
    </div>
    <div class="text-sm text-gray-600 bg-white px-4 py-2 rounded-lg border">
        Total Users with Wishlists: {{ $users->total() }}
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
        <h3 class="text-lg font-medium text-gray-900">Users with Wishlists</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wishlist Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sample Products</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-10 h-10 object-cover rounded-full">
                            @else
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
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
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                            <i class="fas fa-heart mr-1"></i> {{ $user->wishlist->count() }} items
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex -space-x-2">
                            @foreach($user->wishlist->take(3) as $wishlistItem)
                                @if($wishlistItem->product)
                                    @if($wishlistItem->product->image_url)
                                        <img src="{{ asset($wishlistItem->product->image_url) }}" 
                                             alt="{{ $wishlistItem->product->name }}" 
                                             class="w-8 h-8 object-cover rounded-full border-2 border-white"
                                             title="{{ $wishlistItem->product->name }}">
                                    @else
                                        <div class="w-8 h-8 bg-gray-300 rounded-full border-2 border-white flex items-center justify-center" 
                                             title="{{ $wishlistItem->product->name }}">
                                            <i class="fas fa-image text-xs text-gray-500"></i>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                            @if($user->wishlist->count() > 3)
                                <div class="w-8 h-8 bg-gray-100 rounded-full border-2 border-white flex items-center justify-center">
                                    <span class="text-xs text-gray-600">+{{ $user->wishlist->count() - 3 }}</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${{ number_format($user->wishlist->sum(fn($item) => $item->product ? ($item->product->discount_price ?: $item->product->price) : 0), 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->wishlist->max('updated_at')?->diffForHumans() ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewWishlist({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900" title="View Wishlist">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-heart text-4xl text-gray-300 mb-4"></i>
                            <p>No users with wishlists found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
</div>

<!-- Wishlist Details Modal -->
<div id="wishlistModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="wishlistModalTitle">User Wishlist</h3>
                <button id="closeWishlistModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="wishlistDetails" class="space-y-4"></div>
        </div>
    </div>
</div>

<script>
const wishlistModal = document.getElementById('wishlistModal');

function viewWishlist(userId) {
    fetch(`/admin/users/${userId}/wishlist/json`)
        .then(res => res.json())
        .then(data => {
            const user = data.user;
            const wishlist = data.wishlist;

            document.getElementById('wishlistModalTitle').textContent = `${user.name}'s Wishlist`;

            let totalValue = 0;
            const wishlistItemsHTML = wishlist.map(item => {
                const product = item.product;
                if(!product) return '';
                const price = product.discount_price || product.price;
                totalValue += parseFloat(price);

                return `
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            ${product.image_url 
                                ? `<img src="${product.image_url}" alt="${product.name}" class="w-16 h-16 object-cover rounded">`
                                : `<div class="w-16 h-16 bg-gray-300 rounded flex items-center justify-center">
                                     <i class="fas fa-image text-gray-500"></i>
                                   </div>`}
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">${product.name}</h4>
                                <p class="text-sm text-gray-500">${product.category?.name || 'No Category'}</p>
                                <div class="flex items-center mt-1">
                                    <span class="text-sm font-medium text-gray-900">$${parseFloat(price).toFixed(2)}</span>
                                    ${product.discount_price ? `<span class="ml-2 text-xs text-gray-500 line-through">$${parseFloat(product.price).toFixed(2)}</span>` : ''}
                                    <span class="ml-2 px-2 py-1 rounded text-xs ${product.stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${product.stock_quantity > 0 ? 'In Stock' : 'Out of Stock'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">Added ${new Date(item.created_at).toLocaleDateString()}</span>
                            <button onclick="removeWishlistItem(${userId}, '${product.id}')" class="text-red-600 hover:text-red-900" title="Remove from wishlist">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('wishlistDetails').innerHTML = `
                <div class="mb-6 flex justify-between items-center">
                    <div class="flex items-center">
                        ${user.profile_photo_path 
                            ? `<img src="${user.profile_photo_path}" alt="${user.name}" class="w-12 h-12 object-cover rounded-full">`
                            : `<div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                 <i class="fas fa-user text-gray-500"></i>
                               </div>`}
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">${user.name}</h3>
                            <p class="text-gray-600">${user.email}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total Items: <span class="font-medium">${wishlist.length}</span></p>
                        <p class="text-sm text-gray-500">Total Value: <span class="font-medium text-gray-900">$${totalValue.toFixed(2)}</span></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <h4 class="font-medium text-gray-900 mb-3">Wishlist Items</h4>
                    ${wishlist.length > 0 ? wishlistItemsHTML : `<div class="text-center py-8 text-gray-500"><i class="fas fa-heart text-4xl text-gray-300 mb-4"></i><p>No items in wishlist</p></div>`}
                </div>
            `;
            wishlistModal.classList.remove('hidden');
        })
        .catch(err => { console.error(err); alert('Error loading wishlist'); });
}

function removeWishlistItem(userId, productId) {
    if(confirm('Are you sure you want to remove this item from the wishlist?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/wishlists/${userId}/${productId}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
}

document.getElementById('closeWishlistModal').addEventListener('click', () => wishlistModal.classList.add('hidden'));
wishlistModal.addEventListener('click', e => { if(e.target === wishlistModal) wishlistModal.classList.add('hidden'); });
</script>
@endsection