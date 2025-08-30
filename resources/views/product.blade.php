{{-- resources/views/product.blade.php --}}
@extends('layouts.app')

@section('title', 'Produk Ramah Lingkungan — EcoMart')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[18rem,1fr] gap-6">

  {{-- TOGGLE FILTER (mobile) --}}
  <div class="lg:hidden">
    <button id="toggleFilter"
      class="w-full px-4 py-2 rounded-lg border bg-white text-gray-800 hover:bg-gray-50 flex items-center justify-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round"
           d="M3.75 6.75h16.5M5.25 12h13.5m-10.5 5.25h7.5"/></svg>
      Filter & Urutkan
    </button>
  </div>

  {{-- SIDEBAR / FILTERS --}}
  <aside id="filterPanel" class="lg:sticky lg:top-16 h-max bg-white border rounded-xl p-5 hidden lg:block">
    <h2 class="text-lg font-semibold text-emerald-900 mb-4">Filter</h2>

    <form id="filterForm" action="{{ route('product.index') }}" method="GET" class="space-y-6">
      {{-- Kategori --}}
      <div>
        <h3 class="text-sm font-medium text-gray-700 mb-3">Kategori</h3>
        <ul class="space-y-2 text-sm max-h-56 overflow-auto pr-1">
          @foreach(($categories ?? []) as $cat)
            @php $checked = in_array($cat->slug, $selectedCategories ?? []); @endphp
            <li>
              <label class="flex items-center gap-2">
                <input type="checkbox" name="category[]" value="{{ $cat->slug }}"
                       class="rounded border-gray-300"
                       {{ $checked ? 'checked' : '' }}>
                <span>{{ $cat->name }}</span>
              </label>
            </li>
          @endforeach
        </ul>
      </div>

      {{-- Rentang Harga (Rupiah) --}}
      <div>
        <h3 class="text-sm font-medium text-gray-700 mb-3">Rentang Harga</h3>
        @php $pr = $priceRange ?? ''; @endphp
        <ul class="space-y-2 text-sm">
          <li>
            <label class="flex items-center gap-2">
              <input type="radio" name="price" value="under-100k"
                     class="rounded border-gray-300" {{ $pr==='under-100k'?'checked':'' }}>
              <span>Di bawah Rp 100.000</span>
            </label>
          </li>
          <li>
            <label class="flex items-center gap-2">
              <input type="radio" name="price" value="100k-250k"
                     class="rounded border-gray-300" {{ $pr==='100k-250k'?'checked':'' }}>
              <span>Rp 100.000 – Rp 250.000</span>
            </label>
          </li>
          <li>
            <label class="flex items-center gap-2">
              <input type="radio" name="price" value="250k-500k"
                     class="rounded border-gray-300" {{ $pr==='250k-500k'?'checked':'' }}>
              <span>Rp 250.000 – Rp 500.000</span>
            </label>
          </li>
          <li>
            <label class="flex items-center gap-2">
              <input type="radio" name="price" value="over-500k"
                     class="rounded border-gray-300" {{ $pr==='over-500k'?'checked':'' }}>
              <span>Di atas Rp 500.000</span>
            </label>
          </li>
          <li>
            <label class="flex items-center gap-2">
              <input type="radio" name="price" value=""
                     class="rounded border-gray-300" {{ $pr===''?'checked':'' }}>
              <span>Semua</span>
            </label>
          </li>
        </ul>
      </div>

      {{-- Material (opsional) --}}
      @if(!empty($materials) && count($materials))
        <div>
          <h3 class="text-sm font-medium text-gray-700 mb-3">Material</h3>
          <ul class="space-y-2 text-sm">
            @foreach($materials as $m)
              @php $checked = in_array($m, $selectedMaterials ?? []); @endphp
              <li>
                <label class="flex items-center gap-2">
                  <input type="checkbox" name="material[]" value="{{ $m }}"
                         class="rounded border-gray-300"
                         {{ $checked ? 'checked' : '' }}>
                  <span>{{ $m }}</span>
                </label>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="flex gap-2">
        <button type="submit"
          class="w-full bg-emerald-700 text-white py-2.5 rounded-lg font-medium hover:bg-emerald-800 transition">
          Terapkan Filter
        </button>
        <a href="{{ route('product.index') }}"
           class="w-full text-center bg-white border text-gray-700 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition">
          Atur Ulang
        </a>
      </div>
    </form>
  </aside>

  {{-- KONTEN --}}
  <section class="space-y-5">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <h2 class="text-xl font-bold text-emerald-900">Produk Ramah Lingkungan</h2>
        <p class="text-gray-600 text-sm">
          @php
            $first = $products->firstItem() ?? 1;
            $last  = $products->lastItem()  ?? $products->count();
            $total = method_exists($products, 'total') ? $products->total() : $products->count();
          @endphp
          Menampilkan {{ $first }}–{{ $last }} dari {{ $total }} produk
        </p>
      </div>

      {{-- Urutkan (GET) --}}
      <form action="{{ route('product.index') }}" method="GET" class="flex items-center gap-2">
        {{-- Pertahankan filter lain saat ganti sort --}}
        @foreach((array)request()->query() as $k => $v)
          @continue($k === 'sort' || $k === 'page')
          @if(is_array($v))
            @foreach($v as $vv)
              <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
            @endforeach
          @else
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
          @endif
        @endforeach

        <label class="text-sm text-gray-600">Urutkan</label>
        <select name="sort" class="border rounded-md px-3 py-2 text-sm"
                onchange="this.form.submit()">
          <option value="featured"  {{ ($sort ?? 'featured')==='featured'  ? 'selected' : '' }}>Unggulan</option>
          <option value="price-asc" {{ ($sort ?? '')==='price-asc' ? 'selected' : '' }}>Harga: Rendah → Tinggi</option>
          <option value="price-desc"{{ ($sort ?? '')==='price-desc'? 'selected' : '' }}>Harga: Tinggi → Rendah</option>
          <option value="newest"    {{ ($sort ?? '')==='newest'    ? 'selected' : '' }}>Terbaru</option>
        </select>
      </form>
    </div>

    {{-- GRID PRODUK --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($products as $product)
        <x-product-card :product="$product" />
      @empty
        <div class="col-span-full">
          <div class="p-6 bg-white border rounded-xl text-center text-gray-600">
            Produk tidak ditemukan.
          </div>
        </div>
      @endforelse
    </div>

    {{-- PAGINATION --}}
    @if(method_exists($products, 'links'))
      <div class="pt-2">
        {{ $products->appends(request()->query())->onEachSide(1)->links() }}
      </div>
    @endif
  </section>

</div>

{{-- Toggle filter (mobile) --}}
@push('scripts')
<script>
  (function(){
    const btn = document.getElementById('toggleFilter');
    const panel = document.getElementById('filterPanel');
    if(!btn || !panel) return;
    btn.addEventListener('click', () => {
      panel.classList.toggle('hidden');
    });
  })();
</script>
@endpush
@endsection
