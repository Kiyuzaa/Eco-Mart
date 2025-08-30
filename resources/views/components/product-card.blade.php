{{-- resources/views/components/product-card.blade.php --}}
@props(['product' => null, 'showActions' => true])

@php
  use Illuminate\Support\Facades\Route as R;
  use Illuminate\Support\Str;

  $id       = data_get($product, 'id');
  $slug     = data_get($product, 'slug');
  $name     = data_get($product, 'name') ?? data_get($product, 'title', 'Produk');
  $cat      = data_get($product, 'category.name') ?? data_get($product, 'category');
  $price    = (float) data_get($product, 'price', 0);
  $compare  = (float) data_get($product, 'compare_at_price', 0);  // harga coret (opsional)
  $stock    = data_get($product, 'stock');                        // opsional

  $img   = data_get($product,'image')
        ?? data_get($product,'image_url')
        ?? 'https://images.unsplash.com/photo-1519744792095-2f2205e87b6f?q=80&w=800&auto=format&fit=crop';

  $badge = $cat ? Str::limit($cat, 14) : 'Eco';

  // URL detail produk (aman untuk berbagai nama route)
  if ($slug) {
    $detailUrl = R::has('product.show')  ? route('product.show', $slug)
               : (R::has('products.show') ? route('products.show', $slug) : url('/products/'.$slug));
  } elseif ($id) {
    $detailUrl = R::has('product.show')  ? route('product.show', $id)
               : (R::has('products.show') ? route('products.show', $id) : url('/products/'.$id));
  } else {
    $detailUrl = url('/products');
  }

  // Route aksi
  $cartStoreUrl       = R::has('cart.store') ? route('cart.store') : null;
  $wishlistToggleUrl  = ($id && R::has('wishlist.toggle')) ? route('wishlist.toggle', $id) : null;

  // Status wishlist (jika relasi ada)
  $inWishlist = auth()->check() && $id
      ? (method_exists(auth()->user(),'wishlists')
          ? auth()->user()->wishlists()->where('product_id',$id)->exists()
          : false)
      : false;

  // Format harga Rupiah
  $fmt = fn($n) => 'Rp '.number_format((float)$n, 0, ',', '.');

  // Diskon
  $hasDiscount = $compare > $price && $price > 0;
  $discPercent = $hasDiscount ? max(0, round((1 - ($price / $compare)) * 100)) : 0;
@endphp

<div class="relative bg-white border rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden group">
  {{-- Link seluruh kartu --}}
  <a href="{{ $detailUrl }}" class="absolute inset-0 z-[1]" aria-label="Buka {{ $name }}"></a>

  {{-- Gambar --}}
  <div class="relative bg-gray-100 aspect-[4/3]">
    <img
      src="{{ $img }}"
      alt="{{ e($name) }}"
      loading="lazy" decoding="async"
      class="object-cover w-full h-full"
      onerror="this.src='https://images.unsplash.com/photo-1519744792095-2f2205e87b6f?q=80&w=800&auto=format&fit=crop'">

    {{-- Badge kiri atas --}}
    <div class="absolute top-2 left-2 flex flex-col gap-1 z-[2]">
      <span class="inline-flex items-center rounded-full bg-white/90 border border-emerald-100 px-2.5 py-1 text-[11px] font-medium text-emerald-700">
        {{ $badge }}
      </span>
      @if($hasDiscount)
        <span class="inline-flex items-center rounded-full bg-amber-600 text-white px-2.5 py-0.5 text-[11px] font-semibold">
          -{{ $discPercent }}%
        </span>
      @endif
      @if(isset($stock) && (int)$stock <= 0)
        <span class="inline-flex items-center rounded-full bg-gray-800 text-white px-2.5 py-0.5 text-[11px] font-semibold">
          Habis
        </span>
      @endif
    </div>

    {{-- Tombol wishlist --}}
    @if($id)
      @php $wlDisabled = !$wishlistToggleUrl && !auth()->check(); @endphp
      <button
        type="button"
        class="absolute top-2 right-2 bg-white rounded-full p-1.5 shadow hover:scale-105 transition z-[2]"
        aria-label="{{ $inWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
        data-wishlist-button
        data-product-id="{{ $id }}"
        data-url="{{ $wishlistToggleUrl }}"
        aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
        onclick="event.stopPropagation(); event.preventDefault(); ecoWishlistToggle(this)"
        @if($wlDisabled) disabled title="Masuk untuk menambahkan ke wishlist" @endif
      >
        <svg
          class="w-5 h-5 transition {{ $inWishlist ? 'text-pink-600' : 'text-gray-600' }}"
          fill="{{ $inWishlist ? 'currentColor' : 'none' }}"
          stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
        </svg>
      </button>
    @endif
  </div>

  {{-- Body --}}
  <div class="p-4 relative z-[2]">
    <h3 class="font-semibold text-base line-clamp-2">
      <a href="{{ $detailUrl }}" class="hover:underline">{{ Str::limit($name, 80) }}</a>
    </h3>
    @if($cat)
      <p class="text-gray-500 text-sm mt-0.5">{{ $cat }}</p>
    @endif>

    {{-- Harga + Aksi --}}
    <div class="flex items-end justify-between mt-3">
      <div class="flex flex-col">
        <span class="font-bold text-emerald-800 text-lg">{{ $fmt($price) }}</span>
        @if($hasDiscount)
          <span class="text-xs text-gray-500 line-through">{{ $fmt($compare) }}</span>
        @endif
      </div>

      @if($showActions)
        @if($id && $cartStoreUrl && (!isset($stock) || (int)$stock > 0))
          <form action="{{ $cartStoreUrl }}" method="POST" class="contents" onsubmit="event.stopPropagation();">
            @csrf
            <input type="hidden" name="product_id" value="{{ $id }}">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-3 py-2 text-white text-sm hover:bg-emerald-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2m1.6 8h9l3-8H6.4M7 13l-1.6-8M7 13L4 16m13 1a2 2 0 100 4 2 2 0 000-4m-8 2a2 2 0 11-4 0 2 2 0 014 0"/>
              </svg>
              Tambah ke Keranjang
            </button>
          </form>
        @elseif(isset($stock) && (int)$stock <= 0)
          <span class="inline-flex items-center rounded-xl bg-gray-200 px-3 py-2 text-gray-600 text-sm select-none">Stok Habis</span>
        @else
          <a href="{{ url('/cart') }}"
             class="inline-flex items-center rounded-xl bg-gray-300 px-3 py-2 text-gray-600 text-sm cursor-not-allowed pointer-events-none"
             aria-disabled="true">
            Tambah ke Keranjang
          </a>
        @endif
      @endif
    </div>
  </div>
</div>

@push('scripts')
<script>
/* Toggle wishlist – vanilla JS */
window.ecoWishlistToggle = async function (btn) {
  const url = btn?.dataset?.url;
  const pid = btn?.dataset?.productId;
  if (!url) { console.warn('Wishlist toggle URL tidak tersedia.'); return; }

  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    });

    if (!res.ok) { console.error('Gagal toggle wishlist', res.status); return; }
    const data = await res.json();
    const added = (data?.state === 'added') || !!data?.added;

    // Sinkronkan semua tombol di halaman untuk produk yg sama
    document.querySelectorAll(`[data-wishlist-button][data-product-id="${pid}"]`).forEach(el => {
      const svg = el.querySelector('svg');
      if (!svg) return;
      svg.classList.toggle('text-pink-600', added);
      svg.classList.toggle('text-gray-600', !added);
      svg.setAttribute('fill', added ? 'currentColor' : 'none');
      el.setAttribute('aria-pressed', added ? 'true' : 'false');
      el.setAttribute('aria-label', added ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist');
    });

    // Update badge navbar bila fungsi global tersedia
    const wlCount = Number(data?.wishlist_count ?? 0);
    if (typeof window.updateWishlistBadge === 'function') {
      window.updateWishlistBadge(wlCount);
    } else {
      const wlBadge = document.getElementById('wishlist-badge');
      if (wlBadge) {
        wlBadge.textContent = wlCount;
        wlBadge.style.display = wlCount > 0 ? 'inline-flex' : 'none';
      }
    }
  } catch (e) {
    console.error('Error wishlist:', e);
  }
};
</script>
@endpush
