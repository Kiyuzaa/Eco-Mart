@extends('admin.layout') {{-- pakai layout adminmu --}}

@section('title','Product Management')
@section('header-title','Product Management')
@section('header-subtitle','Manage your product inventory')

@section('header-button')
  <a href="{{ route('admin.products.index') }}"
     class="px-4 py-2 rounded bg-gray-900 text-white text-sm">
     + Add Product
  </a>
@endsection

@section('content')
  {{-- ALERT --}}
  @if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-700 text-sm">
      {{ session('success') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 text-red-600 text-sm">
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  {{-- CARD: Add New Product (AKURAT sesuai mockup) --}}
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
      <h3 class="text-[15px] font-semibold text-gray-800">Add New Product</h3>
    </div>

    <div class="p-6">
      <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Row 1: Product Name / Slug --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Product Name</label>
            <input type="text" name="name"
                   class="w-full rounded-md border border-gray-300 focus:border-gray-400 focus:ring-0"
                   placeholder="Enter product name" value="{{ old('name') }}" required>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Slug</label>
            <input type="text" name="slug"
                   class="w-full rounded-md border border-gray-300 focus:border-gray-400 focus:ring-0"
                   placeholder="product-slug" value="{{ old('slug') }}">
          </div>
        </div>

        {{-- Row 2: Category / Price (sesuai mockup) --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Category</label>
            <select name="category_id"
                    class="w-full rounded-md border border-gray-300 bg-white focus:border-gray-400 focus:ring-0" required>
              <option value="">Select category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1">Price</label>
            <input type="number" step="0.01" name="price" min="0"
                   class="w-full rounded-md border border-gray-300 focus:border-gray-400 focus:ring-0"
                   placeholder="0.00" value="{{ old('price') }}" required>
          </div>
        </div>

        {{-- Row 3: Stock (baris sendiri, tidak berdampingan dengan apa pun) --}}
        <div class="mt-4">
          <label class="block text-sm text-gray-600 mb-1">Stock</label>
          <input type="number" name="stock" step="1" min="0" inputmode="numeric"
                 class="w-full rounded-md border border-gray-300 focus:border-gray-400 focus:ring-0"
                 placeholder="0" value="{{ old('stock', 0) }}" required>
        </div>

        {{-- Row 4: Dropzone --}}
        <div class="mt-4">
          <label class="block text-sm text-gray-600 mb-2">Product Image</label>

          <label class="block w-full rounded-md border-2 border-dashed border-gray-300 hover:border-gray-400 transition cursor-pointer">
            <div class="h-32 md:h-40 flex flex-col items-center justify-center text-center px-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-6l-4-4m0 0L9 9m4-4v12"/>
              </svg>
              <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
              <p class="text-xs text-gray-400">PNG, JPG up to 10MB</p>
            </div>
            <input type="file" name="image" class="hidden" accept=".png,.jpg,.jpeg">
          </label>
        </div>

        {{-- Actions --}}
        <div class="mt-4 flex items-center gap-2">
          <button class="px-4 py-2 rounded-md bg-gray-900 text-white hover:bg-black">Save Product</button>
          <a href="{{ route('admin.products.index') }}"
             class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  {{-- CARD: Products List --}}
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
      <h3 class="font-semibold text-gray-800">Products List</h3>
      <form method="GET" class="flex items-center gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..."
               class="rounded border-gray-300 text-sm">
        <button class="px-3 py-1.5 rounded border border-gray-300 text-sm">Search</button>
      </form>
    </div>

    <div class="p-0 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left px-5 py-3">Product</th>
            <th class="text-left px-5 py-3">Category</th>
            <th class="text-left px-5 py-3">Price</th>
            <th class="text-left px-5 py-3">Stock</th>
            <th class="text-left px-5 py-3">Image</th>
            <th class="text-left px-5 py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($products as $p)
            <tr>
              <td class="px-5 py-3">
                <div class="font-medium text-gray-900">{{ $p->name }}</div>
                <div class="text-gray-500 text-xs">{{ $p->slug }}</div>
              </td>
              <td class="px-5 py-3">
                {{ optional($p->category)->name ?? '—' }}
              </td>
              <td class="px-5 py-3">
                ${{ number_format($p->price, 2) }}
              </td>
              <td class="px-5 py-3">
                {{ $p->stock ?? 0 }}
              </td>
              <td class="px-5 py-3">
                @if($p->image)
                  <img src="{{ asset('storage/'.$p->image) }}" alt="" class="w-12 h-12 object-cover rounded border">
                @else
                  <span class="text-gray-400">No image</span>
                @endif
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.products.edit', $p) }}"
                     class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50">Edit</a>

                  <form action="{{ route('admin.products.destroy', $p) }}" method="POST"
                        onsubmit="return confirm('Delete this product?')">
                    @csrf @method('DELETE')
                    <button class="px-2 py-1 border border-red-300 text-red-600 rounded hover:bg-red-50">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-5 py-6 text-center text-gray-500">No products found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($products->hasPages())
      <div class="px-5 py-3 border-t border-gray-100">
        {{ $products->links() }}
      </div>
    @endif
  </div>
@endsection
