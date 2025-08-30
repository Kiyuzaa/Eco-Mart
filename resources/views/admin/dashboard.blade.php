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
    <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-200">
      {{ session('success') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-600 text-sm border border-red-200">
      <ul class="list-disc pl-5 space-y-0.5">
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  {{-- ========== ADD NEW PRODUCT (Collapsible) ========== --}}
  <details class="group bg-white border border-slate-200 rounded-xl shadow-sm mb-6 open:mb-6">
    <summary
      class="cursor-pointer list-none px-6 py-4 border-b border-slate-200 flex items-center justify-between select-none"
    >
      <div>
        <h3 class="text-[15px] font-semibold text-slate-800">Add New Product</h3>
        <p class="text-xs text-slate-500">Quickly add a new item to your catalog</p>
      </div>
      <svg class="w-5 h-5 text-slate-500 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none"
           xmlns="http://www.w3.org/2000/svg">
        <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </summary>

    <div class="p-6">
      <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Row 1 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-600 mb-1">Product Name</label>
            <input type="text" name="name" required placeholder="Enter product name" value="{{ old('name') }}"
                   class="w-full h-10 rounded-md border border-slate-300 px-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Slug</label>
            <input type="text" name="slug" placeholder="product-slug" value="{{ old('slug') }}"
                   class="w-full h-10 rounded-md border border-slate-300 px-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
          </div>
        </div>

        {{-- Row 2 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-600 mb-1">Category</label>
            <select name="category_id" required
                    class="w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm
                           focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
              <option value="">Select category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Price</label>
            <input type="number" step="0.01" min="0" name="price" required placeholder="0.00" value="{{ old('price') }}"
                   class="w-full h-10 rounded-md border border-slate-300 px-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
          </div>
        </div>

        {{-- Row 3 --}}
        <div>
          <label class="block text-sm text-slate-600 mb-1">Stock</label>
          <input type="number" step="1" min="0" inputmode="numeric" name="stock" value="{{ old('stock', 0) }}" required
                 class="w-full h-10 rounded-md border border-slate-300 px-3 text-sm
                        focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        {{-- Row 4: Dropzone --}}
        <div>
          <label class="block text-sm text-slate-600 mb-2">Product Image</label>
          <label class="block w-full rounded-md border-2 border-dashed border-slate-300 hover:border-slate-400 transition cursor-pointer">
            <div class="h-36 md:h-40 flex flex-col items-center justify-center text-center px-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400 mb-2" viewBox="0 0 24 24" fill="none">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                      d="M3 15a4 4 0 004 4h10a4 4 0 004-4M7 10l5-5 5 5M12 5v10"/>
              </svg>
              <p class="text-sm text-slate-600">Click to upload or drag and drop</p>
              <p class="text-xs text-slate-400">PNG, JPG up to 10MB</p>
            </div>
            <input type="file" name="image" class="hidden" accept=".png,.jpg,.jpeg">
          </label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 pt-2">
          <button class="h-10 px-4 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-black transition">
            Save Product
          </button>
          <button type="reset"
                  class="h-10 px-4 rounded-md border border-slate-300 text-slate-700 text-sm hover:bg-slate-50 transition">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </details>

  {{-- ========== PRODUCTS LIST (langsung terlihat) ========== --}}
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
      <h3 class="font-semibold text-slate-800 text-lg">Products List</h3>
      <form method="GET" class="flex items-center gap-2">
        <div class="relative">
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..."
                 class="peer h-10 w-56 rounded-lg border border-slate-300 bg-white px-3 pr-9 text-sm text-slate-700 placeholder:text-slate-400
                        focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"/>
          <svg xmlns="http://www.w3.org/2000/svg"
               class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400 peer-focus:text-emerald-500"
               viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                  d="m21 21-4.3-4.3m1.8-4.7a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
          </svg>
        </div>
        <button
          class="h-10 px-4 rounded-lg border border-emerald-500 text-emerald-600 text-sm font-medium
                 hover:bg-emerald-50 active:bg-emerald-100 transition">
          Search
        </button>
      </form>
    </div>

    <div class="overflow-x-auto">
      {{-- table kamu yang sebelumnya (tidak diubah) --}}
      {!! /* paste tabel Products dari versi kamu di sini */ '' !!}
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 sticky top-0 z-10">
          <tr>
            <th class="text-left px-5 py-3 font-semibold">Product</th>
            <th class="text-left px-5 py-3 font-semibold">Category</th>
            <th class="text-left px-5 py-3 font-semibold">Price</th>
            <th class="text-left px-5 py-3 font-semibold">Stock</th>
            <th class="text-left px-5 py-3 font-semibold">Image</th>
            <th class="text-left px-5 py-3 font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($products as $p)
            <tr class="hover:bg-slate-50/60">
              <td class="px-5 py-3">
                <div class="font-medium text-slate-900">{{ $p->name }}</div>
                <div class="text-slate-500 text-xs">{{ $p->slug }}</div>
              </td>
              <td class="px-5 py-3">
                @php $cat = optional($p->category)->name; @endphp
                @if($cat)
                  <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs text-slate-700">
                    {{ $cat }}
                  </span>
                @else
                  <span class="text-slate-400">—</span>
                @endif
              </td>
              <td class="px-5 py-3 font-medium text-slate-800">
                ${{ number_format($p->price, 2) }}
              </td>
              <td class="px-5 py-3">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                  {{ $p->stock ?? 0 }}
                </span>
              </td>
              <td class="px-5 py-3">
                @if($p->image)
                  <img src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}"
                       class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                @else
                  <span class="text-slate-400">No image</span>
                @endif
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.products.edit', $p) }}"
                     class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700
                            hover:bg-slate-50 active:bg-slate-100 transition">
                    Edit
                  </a>
                  <form action="{{ route('admin.products.destroy', $p) }}" method="POST"
                        onsubmit="return confirm('Delete this product?')">
                    @csrf @method('DELETE')
                    <button
                      class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-600
                             hover:bg-red-50 active:bg-red-100 transition">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-5 py-8 text-center text-slate-500">No products found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($products->hasPages())
      <div class="px-5 py-3 border-t border-slate-200 bg-white">
        {{ $products->links() }}
      </div>
    @endif
  </div>
@endsection
