@extends('layouts.admin')

@section('title', 'Products Management')

@section('admin-content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Products Management</h1>
            <p class="text-gray-600 mt-2">Manage your store products</p>
        </div>
        @if($brands->count() > 0)
        <button id="addProductBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Product
        </button>
        @else
        <div class="text-right">
            <button disabled class="bg-gray-400 text-white px-6 py-2 rounded-lg cursor-not-allowed" title="Create a brand first">
                <i class="fas fa-plus mr-2"></i>Add Product
            </button>
            <p class="text-sm text-red-600 mt-2">
                <a href="{{ route('admin.brands.index') }}" class="underline">Create a brand</a> before adding products
            </p>
        </div>
        @endif
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

@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Products Table -->
<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">All Products ({{ $products->total() }})</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($product->image_url)
                                <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-300 rounded flex items-center justify-center">
                                    <i class="fas fa-image text-gray-500"></i>
                                </div>
                            @endif
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $product->sku ?: 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $product->category->name ?? 'No Category' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>${{ number_format($product->price, 2) }}</div>
                        @if($product->discount_price)
                            <div class="text-xs text-red-600">Sale: ${{ number_format($product->discount_price, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 rounded-full text-xs {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($product->is_featured)
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Featured
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="editProduct({{ $product->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteProduct({{ $product->id }})" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No products found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $products->links() }}
    </div>
</div>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Product</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="productForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" name="name" id="productName" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                        <input type="text" name="sku" id="productSku" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category *</label>
                        <select name="category_id" id="productCategory" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if($brands->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Brand *</label>
                        <select name="brand_id" id="productBrand" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div>
                        <p class="text-sm text-red-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Please create at least one brand before adding products.
                        </p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price *</label>
                        <input type="number" step="0.01" name="price" id="productPrice" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Discount Price</label>
                        <input type="number" step="0.01" name="discount_price" id="productDiscountPrice" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" id="productStock" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="image" id="productImage" accept="image/*" class="mt-1 block w-full">
                        <div id="currentImagePreview" class="mt-2 hidden">
                            <img id="currentImage" src="" alt="Current" class="h-20 w-20 object-cover rounded">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Description *</label>
                    <textarea name="description" id="productDescription" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Specifications (JSON)</label>
                    <textarea name="specifications" id="productSpecifications" rows="2" placeholder='{"color": "red", "size": "large"}' class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Enter valid JSON or leave empty</p>
                </div>
                
                <div class="mt-4 flex space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="productActive" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" id="productFeatured" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Featured</span>
                    </label>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" id="cancelBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <span id="submitText">Add Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const productModal = document.getElementById('productModal');
const productForm = document.getElementById('productForm');
const modalTitle = document.getElementById('modalTitle');
const submitText = document.getElementById('submitText');
const formMethod = document.getElementById('formMethod');

// Add Product
document.getElementById('addProductBtn').addEventListener('click', function() {
    productForm.action = "{{ route('admin.products.store') }}";
    formMethod.value = ''; // default POST
    modalTitle.textContent = 'Add Product';
    submitText.textContent = 'Add Product';
    productForm.reset();
    document.getElementById('productActive').checked = true;
    document.getElementById('productFeatured').checked = false;
    document.getElementById('currentImagePreview').classList.add('hidden');
    productModal.classList.remove('hidden');
});

// Edit Product
function editProduct(id) {
    fetch(`/admin/products/${id}`)
        .then(response => response.json())
        .then(product => {
            productForm.action = `/admin/products/${id}`;
            formMethod.value = 'PUT';
            modalTitle.textContent = 'Edit Product';
            submitText.textContent = 'Update Product';
            
            document.getElementById('productName').value = product.name;
            document.getElementById('productSku').value = product.sku || '';
            document.getElementById('productCategory').value = product.category_id;
            if(document.getElementById('productBrand')) {
                document.getElementById('productBrand').value = product.brand_id || '';
            }
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productDiscountPrice').value = product.discount_price || '';
            document.getElementById('productStock').value = product.stock_quantity;
            document.getElementById('productDescription').value = product.description;
            
            // Handle specifications - convert object to JSON string for display
            if (product.specifications && typeof product.specifications === 'object') {
                document.getElementById('productSpecifications').value = JSON.stringify(product.specifications, null, 2);
            } else {
                document.getElementById('productSpecifications').value = '';
            }
            
            document.getElementById('productActive').checked = product.is_active;
            document.getElementById('productFeatured').checked = product.is_featured;
            
            // Show current image if exists
            if (product.image_url) {
                document.getElementById('currentImage').src = `/storage/${product.image_url}`;
                document.getElementById('currentImagePreview').classList.remove('hidden');
            } else {
                document.getElementById('currentImagePreview').classList.add('hidden');
            }
            
            productModal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading product data');
        });
}

// Delete Product
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/products/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close Modal
document.getElementById('closeModal').addEventListener('click', function() {
    productModal.classList.add('hidden');
});

document.getElementById('cancelBtn').addEventListener('click', function() {
    productModal.classList.add('hidden');
});

// Close modal when clicking outside
productModal.addEventListener('click', function(e) {
    if (e.target === productModal) {
        productModal.classList.add('hidden');
    }
});

// Validate JSON before form submission
productForm.addEventListener('submit', function(e) {
    const specsField = document.getElementById('productSpecifications');
    const specsValue = specsField.value.trim();
    
    if (specsValue) {
        try {
            JSON.parse(specsValue);
        } catch (error) {
            e.preventDefault();
            alert('Invalid JSON in specifications field. Please fix or leave it empty.');
            specsField.focus();
            return false;
        }
    }
});
</script>
@endsection