@extends('admin.layout') {{-- pakai layout adminmu --}}

@section('title','Manajemen Produk')
@section('header-title','Manajemen Produk')
@section('header-subtitle','Kelola persediaan produk Anda')

@section('header-button')
<a href="{{ route('admin.products.index') }}"
    class="px-4 py-2 rounded bg-gray-900 text-white text-sm">
    + Tambah Produk
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

  {{-- ========== TAMBAH PRODUK BARU (Bisa Dibuka/Tutup) ========== --}}
  <details class="group bg-white border border-slate-200 rounded-xl shadow-sm mb-6 open:mb-6">
    <summary
      class="cursor-pointer list-none px-6 py-4 border-b border-slate-200 flex items-center justify-between select-none"
    >
      <div>
        <h3 class="text-[15px] font-semibold text-slate-800">Tambah Produk Baru</h3>
        <p class="text-xs text-slate-500">Tambah item baru ke katalog Anda dengan cepat</p>
      </div>
      <svg class="w-5 h-5 text-slate-500 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none"
           xmlns="http://www.w3.org/2000/svg">
        <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </summary>

    <div class="p-6">
      <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Baris 1 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-600 mb-1">Nama Produk</label>
            <input type="text" name="name" required placeholder="Masukkan nama produk" value="{{ old('name') }}"
                   class="w-full h-10 rounded-md border border-slate-300 px-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Slug</label>
            <input type="text" name="slug" placeholder="slug-produk" value="{{ old('slug') }}"
                   class="w-full h-10 rounded-md border border-slate-300 px-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
          </div>
        </div>

        {{-- Baris 2 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-600 mb-1">Kategori</label>
            <select name="category_id" required
                    class="w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm
                           focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
              <option value="">Pilih kategori</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Harga</label>
            <input type="number" step="0.01" min="0" name="price" required placeholder="0.00" value="{{ old('price') }}"
                   class="w-full h-10 rounded-md border border-slate-300 px-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
          </div>
        </div>

        {{-- Baris 3 --}}
        <div>
          <label class="block text-sm text-slate-600 mb-1">Stok</label>
          <input type="number" step="1" min="0" inputmode="numeric" name="stock" value="{{ old('stock', 0) }}" required
                 class="w-full h-10 rounded-md border border-slate-300 px-3 text-sm
                        focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        {{-- Baris 4: Dropzone --}}
        <div>
          <label class="block text-sm text-slate-600 mb-2">Gambar Produk</label>
          <label class="block w-full rounded-md border-2 border-dashed border-slate-300 hover:border-slate-400 transition cursor-pointer">
            <div class="h-36 md:h-40 flex flex-col items-center justify-center text-center px-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400 mb-2" viewBox="0 0 24 24" fill="none">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                      d="M3 15a4 4 0 004 4h10a4 4 0 004-4M7 10l5-5 5 5M12 5v10"/>
              </svg>
              <p class="text-sm text-slate-600">Klik untuk unggah atau seret & lepas</p>
              <p class="text-xs text-slate-400">PNG, JPG maksimal 10MB</p>
            </div>
            <input type="file" name="image" class="hidden" accept=".png,.jpg,.jpeg">
          </label>
        </div>

        {{-- Aksi --}}
        <div class="flex items-center gap-2 pt-2">
          <button class="h-10 px-4 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-black transition">
            Simpan Produk
          </button>
          <button type="reset"
                  class="h-10 px-4 rounded-md border border-slate-300 text-slate-700 text-sm hover:bg-slate-50 transition">
            Batal
          </button>
        </div>
      </form>
    </div>
  </details>

  {{-- ========== DAFTAR PRODUK (langsung terlihat) ========== --}}
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
      <h3 class="font-semibold text-slate-800 text-lg">Daftar Produk</h3>
      <form method="GET" class="flex items-center gap-2">
        <div class="relative">
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
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
          Cari
        </button>
      </form>
    </div>

    <div class="overflow-x-auto">
      {{-- tabel produk --}}
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 sticky top-0 z-10">
          <tr>
            <th class="text-left px-5 py-3 font-semibold">Produk</th>
            <th class="text-left px-5 py-3 font-semibold">Kategori</th>
            <th class="text-left px-5 py-3 font-semibold">Harga</th>
            <th class="text-left px-5 py-3 font-semibold">Stok</th>
            <th class="text-left px-5 py-3 font-semibold">Gambar</th>
            <th class="text-left px-5 py-3 font-semibold">Aksi</th>
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
                  <span class="text-slate-400">Tidak ada gambar</span>
                @endif
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.products.edit', $p) }}"
                     class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700
                            hover:bg-slate-50 active:bg-slate-100 transition">
                    Ubah
                  </a>
                  <form action="{{ route('admin.products.destroy', $p) }}" method="POST"
                        onsubmit="return confirm('Hapus produk ini?')">
                    @csrf @method('DELETE')
                    <button
                      class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-600
                             hover:bg-red-50 active:bg-red-100 transition">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-5 py-8 text-center text-slate-500">Produk tidak ditemukan.</td>
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
