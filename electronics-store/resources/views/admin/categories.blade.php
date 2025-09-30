@extends('layouts.admin')

@section('title', 'Categories Management')

@section('admin-content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Categories Management</h1>
        <p class="text-gray-600 mt-2">Manage product categories</p>
    </div>
    <button id="addCategoryBtn" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-plus mr-2"></i>Add Category
    </button>
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

<!-- Categories Table -->
<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">All Categories ({{ $categories->total() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products Count</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ $category->products_count }} products
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $category->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="editCategory({{ $category->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.categories.delete', $category) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this category?')" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No categories found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $categories->links() }}
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Category</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="categoryForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="categoryId" name="_method" value="">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" name="name" id="categoryName" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="categoryDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="categoryActive" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" id="cancelBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <span id="submitText">Add Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const categoryModal = document.getElementById('categoryModal');
const categoryForm = document.getElementById('categoryForm');
const modalTitle = document.getElementById('modalTitle');
const submitText = document.getElementById('submitText');

// Open add modal
document.getElementById('addCategoryBtn').addEventListener('click', () => {
    categoryForm.action = "{{ route('admin.categories.store') }}";
    categoryForm.querySelector('[name="_method"]').value = '';
    modalTitle.textContent = 'Add Category';
    submitText.textContent = 'Add Category';
    categoryForm.reset();
    document.getElementById('categoryActive').checked = true;
    categoryModal.classList.remove('hidden');
});

// Open edit modal
function editCategory(id) {
    fetch(`/admin/categories/${id}/edit-json`)
        .then(res => res.json())
        .then(data => {
            categoryForm.action = `/admin/categories/${id}`;
            categoryForm.querySelector('[name="_method"]').value = 'PUT';
            modalTitle.textContent = 'Edit Category';
            submitText.textContent = 'Update Category';
            document.getElementById('categoryId').value = data.id;
            document.getElementById('categoryName').value = data.name;
            document.getElementById('categoryDescription').value = data.description || '';
            document.getElementById('categoryActive').checked = data.is_active;
            categoryModal.classList.remove('hidden');
        })
        .catch(err => alert('Error loading category data'));
}

// Close modal
document.getElementById('closeModal').addEventListener('click', () => categoryModal.classList.add('hidden'));
document.getElementById('cancelBtn').addEventListener('click', () => categoryModal.classList.add('hidden'));
categoryModal.addEventListener('click', e => { if(e.target===categoryModal) categoryModal.classList.add('hidden'); });
</script>
@endsection
