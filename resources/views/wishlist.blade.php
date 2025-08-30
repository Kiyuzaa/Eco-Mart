{{-- resources/views/wishlist.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Keinginan — EcoMart')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-500 mb-6">
    <ol class="flex items-center gap-2">
      <li><a href="{{ url('/') }}" class="hover:underline">Beranda</a></li>
      <li>›</li>
      <li class="text-gray-800 font-medium">Daftar Keinginan</li>
    </ol>
  </nav>

  {{-- Heading --}}
  <div class="text-center mb-8">
    <h1 class="text-3xl font-bold text-emerald-900">Daftar Keinginan <span class="align-middle">🖤</span></h1>
    <p class="text-gray-600 mt-2">Pantau produk ramah lingkungan favoritmu.</p>
  </div>

  @if($products->count() === 0)
    {{-- Empty State --}}
    <div class="bg-white border rounded-xl p-10 text-center">
      <div class="text-5xl mb-3">🌿</div>
      <h3 class="text-xl font-semibold text-emerald-900">Wishlist kamu masih kosong</h3>
      <p class="text-gray-600 mt-1">Jelajahi item ramah lingkungan dan simpan untuk nanti.</p>
      <a href="{{ route('product.index') }}"
         class="inline-flex items-center mt-6 px-4 py-2 rounded-lg bg-emerald-700 text-white font-medium hover:bg-emerald-800">
        Jelajahi Produk
      </a>
    </div>
  @else
    {{-- Toolbar Top --}}
    <div class="bg-white border rounded-xl p-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div class="flex items-center gap-4">
        <span class="text-gray-600">{{ $products->total() }} produk</span>
        <label class="inline-flex items-center gap-2 select-none">
          <input id="selectAll" type="checkbox" class="rounded border-gray-300">
          <span class="text-gray-800">Pilih Semua</span>
        </label>
      </div>

      <div class="flex items-center gap-3">
        {{-- Sort --}}
        <form method="GET" action="{{ route('wishlist') }}">
          <select name="sort" class="rounded-lg border-gray-300 text-gray-800 text-sm"
                  onchange="this.form.submit()">
            <option value="newest"     {{ ($sort ?? request('sort','newest'))==='newest'?'selected':'' }}>Urutkan: Terbaru</option>
            <option value="price-asc"  {{ ($sort ?? request('sort'))==='price-asc'?'selected':'' }}>Harga: Rendah → Tinggi</option>
            <option value="price-desc" {{ ($sort ?? request('sort'))==='price-desc'?'selected':'' }}>Harga: Tinggi → Rendah</option>
            <option value="eco-desc"   {{ ($sort ?? request('sort'))==='eco-desc'?'selected':'' }}>Skor Eco: Tertinggi</option>
          </select>
        </form>

        {{-- Aksi Massal --}}
        <div class="flex items-center gap-2">
          {{-- Tambahkan terpilih ke keranjang --}}
          <form id="bulkAddForm" action="{{ route('wishlist.bulkAddToCart') }}" method="POST">
            @csrf
            <button type="submit"
              class="px-4 py-2 rounded-lg bg-emerald-700 text-white text-sm font-semibold hover:bg-emerald-800 disabled:opacity-50"
              data-bulk="add" disabled>
              Tambahkan ke Keranjang
            </button>
          </form>

          {{-- Hapus terpilih --}}
          <form id="bulkRemoveForm" action="{{ route('wishlist.bulk-remove') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
              class="px-4 py-2 rounded-lg border text-sm font-semibold hover:bg-gray-50 disabled:opacity-50"
              data-bulk="remove" disabled>
              Hapus Terpilih
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Form dummy pembungkus grid agar checkbox bisa disalin ke dua form bulk --}}
    <form id="wishlistGridForm" onsubmit="return false">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($products as $p)
          <div class="bg-white border rounded-2xl overflow-hidden relative flex flex-col">
            {{-- Checkbox per item (pojok kiri atas) --}}
            <label class="absolute top-2 left-2 z-10 bg-white/90 backdrop-blur px-2 py-1 rounded-md shadow border flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="rowCheck rounded border-gray-300">
              <span class="text-xs text-gray-700">Pilih</span>
            </label>

            {{-- Gambar / placeholder --}}
            @php
              $img = data_get($p, 'image')
                  ?? data_get($p, 'image_url')
                  ?? 'https://via.placeholder.com/600x400';
            @endphp
            <a href="{{ route('products.show', ['product'=>$p->slug]) }}" class="h-44 bg-gray-100 block overflow-hidden">
              <img src="{{ $img }}" alt="{{ e(data_get($p,'name') ?? data_get($p,'title','Produk')) }}"
                   class="w-full h-full object-cover hover:scale-105 transition"
                   onerror="this.src='https://via.placeholder.com/600x400'">
            </a>

            <div class="p-4 flex-1 flex flex-col">
              @php
                $title = data_get($p,'name') ?? data_get($p,'title','Produk');
                $desc  = data_get($p,'short_description') ?? data_get($p,'description');
                $price = (float) data_get($p,'price',0);
                $cmp   = data_get($p,'compare_at_price');
                $eco   = data_get($p,'eco_score');
              @endphp

              <a href="{{ route('products.show', ['product'=>$p->slug]) }}" class="font-semibold text-gray-900 hover:text-emerald-800 line-clamp-2">{{ \Illuminate\Support\Str::limit($title, 80) }}</a>
              @if($desc)
                <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $desc }}</p>
              @endif

              <div class="flex items-center gap-2 text-gray-600 text-sm mt-2">
                @if(!is_null($eco))
                  <span>🍃 Skor Eco: {{ number_format((float)$eco,1) }}/10</span>
                @endif
              </div>

              <div class="flex items-center justify-between mt-3">
                <div class="text-lg font-semibold text-gray-900">
                  Rp {{ number_format($price, 0, ',', '.') }}
                </div>
                @if(!is_null($cmp))
                  <div class="text-gray-400 line-through text-sm">
                    Rp {{ number_format((float)$cmp, 0, ',', '.') }}
                  </div>
                @endif
              </div>

              <div class="mt-4 grid grid-cols-1 gap-2">
                {{-- Tambah satuan ke keranjang --}}
                <form action="{{ route('cart.store') }}" method="POST" class="contents">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $p->id }}">
                  <input type="hidden" name="quantity" value="1">
                  <button type="submit"
                    class="w-full inline-flex justify-center px-4 py-2 rounded-lg bg-emerald-700 text-white font-semibold hover:bg-emerald-800">
                    Tambah ke Keranjang
                  </button>
                </form>

                {{-- Hapus satuan dari wishlist (pakai bulk-remove dengan 1 id) --}}
                <form action="{{ route('wishlist.bulk-remove') }}" method="POST" class="contents" onsubmit="return confirm('Hapus produk ini dari wishlist?')">
                  @csrf
                  @method('DELETE')
                  <input type="hidden" name="ids[]" value="{{ $p->id }}">
                  <button type="submit"
                    class="w-full inline-flex justify-center px-4 py-2 rounded-lg border hover:bg-gray-50">
                    Hapus dari Wishlist
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </form>

    {{-- Pagination --}}
    <div class="mt-8">
      {{ $products->appends(['sort' => $sort ?? null])->links() }}
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
  const selectAll      = document.getElementById('selectAll');
  const bulkAddForm    = document.getElementById('bulkAddForm');
  const bulkRemoveForm = document.getElementById('bulkRemoveForm');
  const bulkButtons    = Array.from(document.querySelectorAll('[data-bulk]'));

  function rowChecks() {
    return Array.from(document.querySelectorAll('.rowCheck'));
  }

  function selectedIds() {
    return rowChecks().filter(cb => cb.checked).map(cb => cb.value);
  }

  function setBulkEnabled(enabled) {
    bulkButtons.forEach(btn => btn.disabled = !enabled);
  }

  function syncForms() {
    const ids = selectedIds();

    // Bersihkan input hidden lama
    [bulkAddForm, bulkRemoveForm].forEach(form => {
      Array.from(form.querySelectorAll('input[name="ids[]"]')).forEach(el => el.remove());
    });

    // Tambahkan input hidden ids[] sesuai pilihan
    ids.forEach(id => {
      const a = document.createElement('input');
      a.type = 'hidden'; a.name = 'ids[]'; a.value = id;
      bulkAddForm.appendChild(a);

      const r = document.createElement('input');
      r.type = 'hidden'; r.name = 'ids[]'; r.value = id;
      bulkRemoveForm.appendChild(r);
    });

    setBulkEnabled(ids.length > 0);

    // State indeterminate untuk Select All
    if (selectAll) {
      const total = rowChecks().length;
      const selected = ids.length;
      selectAll.checked = (total > 0 && selected === total);
      selectAll.indeterminate = (selected > 0 && selected < total);
    }
  }

  // Select All
  if (selectAll) {
    selectAll.addEventListener('change', () => {
      rowChecks().forEach(cb => cb.checked = selectAll.checked);
      syncForms();
    });
  }

  // Perubahan tiap baris
  document.addEventListener('change', (e) => {
    if (e.target && e.target.classList.contains('rowCheck')) {
      syncForms();
    }
  });

  // Inisialisasi
  syncForms();
})();
</script>
@endpush
