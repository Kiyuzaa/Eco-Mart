{{-- resources/views/product-detail.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $product->name ?? $product->title ?? 'Product Detail' }} — EcoMart</title>

  {{-- CSRF untuk AJAX --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css','resources/js/app.js'])
  @else
      <link rel="preconnect" href="https://fonts.bunny.net" />
      <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
      <script src="https://cdn.tailwindcss.com"></script>
  @endif

  <style>
    html,body{font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial}
    .dot{width:28px;height:28px;border-radius:9999px;border:1px solid #e5e7eb;display:inline-flex;align-items:center;justify-content:center}
    .dot input{display:none}
    .dot.active{outline:2px solid #111827; outline-offset:2px}
    .size-btn{border:1px solid #e5e7eb;border-radius:.5rem;padding:.5rem .75rem;min-width:44px;text-align:center}
    .size-btn.active{border-color:#111827;background:#111827;color:#fff}
    .tab-btn{padding:.75rem 1rem;border-bottom:2px solid transparent}
    .tab-btn.active{border-color:#111827;font-weight:600}
  </style>
</head>
<body class="bg-white text-slate-900">

  {{-- NAVBAR --}}
  <x-navbar />

  {{-- ===== GUARDS / FALLBACKS ===== --}}
  @php
    // Images
    $images = isset($images) ? collect($images) : collect();
    if ($images->isEmpty()) {
        $images = collect([
            data_get($product, 'image_url')
            ?? data_get($product, 'image')
            ?? 'https://images.unsplash.com/photo-1519744792095-2f2205e87b6f?q=80&w=1200&auto=format&fit=crop'
        ]);
    }

    // Sizes & Colors
    $sizes  = isset($sizes)  ? collect($sizes)  : collect(['S','M','L','XL']);
    $colors = isset($colors) ? collect($colors) : collect(['#111827','#4B5563','#9CA3AF']);

    // Reviews
    $reviews = isset($reviews) ? collect($reviews) : collect();

    // Compare price
    if (!isset($compare_at)) {
        $compare_at = data_get($product, 'compare_at_price') ?? data_get($product, 'old_price');
    }

    // Apakah produk ini sudah ada di wishlist user yang login?
    $inWishlist = auth()->check() ? auth()->user()->hasInWishlist($product->id) : false;
  @endphp

  {{-- MAIN --}}
  <main class="max-w-6xl mx-auto px-4 py-8">
    <div class="grid md:grid-cols-2 gap-8">
      {{-- LEFT: Gallery --}}
      <div>
        <div class="aspect-square bg-slate-100 rounded-xl overflow-hidden">
          <img id="mainImage" src="{{ $images->first() }}" alt="{{ $product->name ?? 'Product' }}"
               class="w-full h-full object-cover">
        </div>
        <div class="mt-3 grid grid-cols-4 gap-3">
          @foreach ($images->take(4) as $img)
            <button type="button"
                    class="aspect-[4/3] bg-slate-100 rounded-lg overflow-hidden border hover:shadow"
                    onclick="swapMain('{{ e($img) }}')">
              <img src="{{ $img }}" class="w-full h-full object-cover" alt="thumb">
            </button>
          @endforeach
        </div>
      </div>

      {{-- RIGHT: Info --}}
      <div>
        <h1 class="text-2xl md:text-3xl font-semibold">
          {{ $product->name ?? $product->title ?? 'Product Name' }}
        </h1>

        @php $badges = ['Sustainable','Comfortable','Ethically Made']; @endphp
        <div class="mt-1 text-sm text-slate-500 space-x-2">
          @foreach($badges as $b)
            <span>{{ $loop->first ? '' : '·' }} {{ $b }}</span>
          @endforeach
        </div>

        <div class="mt-3 flex items-end gap-3">
          <div class="text-2xl font-semibold">
            ${{ number_format((float)($product->price ?? 0), 2) }}
          </div>
          @if($compare_at)
            <div class="text-slate-400 line-through">
              ${{ number_format((float)$compare_at, 2) }}
            </div>
            @php
              $save = ($compare_at > 0 && ($product->price ?? 0) > 0)
                      ? round((1 - (($product->price ?? 0) / $compare_at)) * 100)
                      : null;
            @endphp
            @if($save)
              <span class="text-emerald-600 text-sm font-medium">Save {{ $save }}%</span>
            @endif
          @endif
        </div>

        {{-- SIZE --}}
        <div class="mt-6">
          <div class="text-sm font-medium mb-2">Size</div>
          <div id="sizeGroup" class="flex flex-wrap gap-2">
            @foreach($sizes as $size)
              <button type="button" class="size-btn" data-value="{{ $size }}">{{ $size }}</button>
            @endforeach
          </div>
        </div>

        {{-- COLOR --}}
        <div class="mt-4">
          <div class="text-sm font-medium mb-2">Color</div>
          <div id="colorGroup" class="flex items-center gap-2">
            @foreach($colors as $hex)
              <label class="dot" data-value="{{ $hex }}" style="background: {{ $hex }}">
                <input type="radio" name="color" value="{{ $hex }}">
                <span class="sr-only">{{ $hex }}</span>
              </label>
            @endforeach
          </div>
        </div>

        {{-- ADD TO CART + WISHLIST --}}
        <form class="mt-5" method="POST" action="{{ route('cart.store') }}">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <input type="hidden" name="size" id="sizeInput">
          <input type="hidden" name="color" id="colorInput">

          <div class="flex items-center gap-3">
            <div class="flex items-center border rounded-lg">
              <button type="button" class="px-3 py-2" onclick="qtyStep(-1)">−</button>
              <input id="qty" name="quantity" type="number" value="1" min="1"
                     class="w-12 text-center border-x py-2 outline-none">
              <button type="button" class="px-3 py-2" onclick="qtyStep(1)">＋</button>
            </div>

            <button type="submit"
                    class="flex-1 h-11 rounded-lg bg-slate-900 text-white font-medium hover:opacity-95">
              Add to Cart
            </button>

            {{-- Tombol Wishlist (UI sama; hanya diberi data-atribut) --}}
            <button type="button" title="Add to Wishlist"
                    class="h-11 aspect-square rounded-lg border flex items-center justify-center"
                    data-wishlist-button
                    data-product-id="{{ $product->id }}"
                    data-url="{{ route('wishlist.toggle', $product->id) }}"
                    aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
              <svg class="w-5 h-5 {{ $inWishlist ? 'text-red-500' : 'text-gray-600' }}"
                   fill="{{ $inWishlist ? 'currentColor' : 'none' }}"
                   stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </button>
          </div>
        </form>

        {{-- Perks --}}
        <ul class="mt-5 space-y-2 text-sm text-slate-600">
          <li class="flex items-center gap-2"><span>🚚</span> Free shipping on orders over $60</li>
          <li class="flex items-center gap-2"><span>↩️</span> 30-day easy returns</li>
          <li class="flex items-center gap-2"><span>🌱</span> 100% eco-friendly materials</li>
        </ul>
      </div>
    </div>

    {{-- TABS --}}
    <section class="mt-10">
      <div class="border-b flex gap-4">
        <button class="tab-btn active" data-tab="desc">Description</button>
        <button class="tab-btn" data-tab="specs">Specifications</button>
        <button class="tab-btn" data-tab="reviews">Reviews ({{ $reviews->count() }})</button>
      </div>

      <div id="tab-desc" class="pt-5">
        <p class="text-slate-700 leading-7">
          {{ $product->description ?? 'No description provided yet.' }}
        </p>
        <ul class="mt-4 list-disc list-inside text-slate-700 space-y-1">
          <li>Made from 100% certified organic cotton</li>
          <li>Pre-shrunk for consistent sizing</li>
          <li>Washer-safe comfort and breathability</li>
          <li>Ethically manufactured in fair-trade facilities</li>
        </ul>
      </div>

      <div id="tab-specs" class="pt-5 hidden">
        @php
          $specs = data_get($product, 'specs', [
            'Material' => 'Organic Cotton 100%',
            'Weight'   => '180 gsm',
            'Fit'      => 'Regular',
            'Care'     => 'Machine wash cold',
          ]);
        @endphp
        <dl class="grid sm:grid-cols-2 gap-x-8 gap-y-3">
          @foreach($specs as $k => $v)
            <div>
              <dt class="text-slate-500 text-sm">{{ $k }}</dt>
              <dd class="font-medium">{{ $v }}</dd>
            </div>
          @endforeach
        </dl>
      </div>

      <div id="tab-reviews" class="pt-5 hidden">
        @if($reviews->isEmpty())
          <p class="text-slate-600">No reviews yet. Be the first to review!</p>
        @else
          <div class="space-y-5">
            @foreach($reviews as $r)
              <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                  <div class="font-medium">{{ $r->author ?? 'Anonymous' }}</div>
                  <div class="text-amber-500">{{ str_repeat('★', $r->rating ?? 5) }}</div>
                </div>
                <p class="mt-2 text-slate-700">{{ $r->body }}</p>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>

    {{-- RELATED --}}
    <section class="mt-12">
      <h2 class="text-lg font-semibold mb-4">You may also like</h2>
      <div class="grid sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @forelse($related as $p)
          @php
            $plink = $p->slug
              ? (Route::has('products.show') ? route('products.show', $p->slug) : url('/products/'.$p->slug))
              : url('/products/'.$p->id);
            $pimg = data_get($p,'image_url') ?? data_get($p,'image') ?? $images->first();
          @endphp
          <a href="{{ $plink }}" class="group border rounded-xl overflow-hidden hover:shadow-sm">
            <div class="aspect-[4/3] bg-slate-100">
              <img src="{{ $pimg }}"
                   class="w-full h-full object-cover group-hover:scale-[1.02] transition"
                   alt="{{ $p->name ?? 'Product' }}">
            </div>
            <div class="p-3">
              <div class="line-clamp-1 font-medium">{{ $p->name ?? $p->title ?? 'Product' }}</div>
              <div class="text-sm text-slate-600 mt-1">
                ${{ number_format((float)($p->price ?? 0), 2) }}
              </div>
            </div>
          </a>
        @empty
          <div class="text-slate-500">No related products.</div>
        @endforelse
      </div>
    </section>
  </main>

  {{-- FOOTER --}}
  <x-footer />

  <script>
    // Gallery
    function swapMain(src){ document.getElementById('mainImage').src = src; }

    // Qty
    function qtyStep(n){
      const el = document.getElementById('qty');
      const v = Math.max(1, parseInt(el.value || 1,10) + n);
      el.value = v;
    }

    // Size select
    const sizeGroup = document.getElementById('sizeGroup');
    const sizeInput = document.getElementById('sizeInput');
    if(sizeGroup){
      sizeGroup.querySelectorAll('.size-btn').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          sizeGroup.querySelectorAll('.size-btn').forEach(b=>b.classList.remove('active'));
          btn.classList.add('active');
          sizeInput.value = btn.dataset.value;
        });
      });
      const first = sizeGroup.querySelector('.size-btn'); if(first){ first.click(); }
    }

    // Color select
    const colorGroup = document.getElementById('colorGroup');
    const colorInput = document.getElementById('colorInput');
    if(colorGroup){
      colorGroup.querySelectorAll('.dot').forEach(dot=>{
        dot.addEventListener('click', ()=>{
          colorGroup.querySelectorAll('.dot').forEach(d=>d.classList.remove('active'));
          dot.classList.add('active');
          colorInput.value = dot.dataset.value;
        });
      });
      const firstDot = colorGroup.querySelector('.dot'); if(firstDot){ firstDot.click(); }
    }

    // Tabs
    const tabs = {
      desc: document.getElementById('tab-desc'),
      specs: document.getElementById('tab-specs'),
      reviews: document.getElementById('tab-reviews'),
    };
    document.querySelectorAll('.tab-btn').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const key = btn.dataset.tab;
        Object.entries(tabs).forEach(([k,el])=>{
          if(el) el.classList.toggle('hidden', k !== key);
        });
      });
    });

    // ===== Wishlist toggle (tanpa ubah UI) =====
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-wishlist-button]');
      if (!btn) return;

      e.preventDefault();

      const url   = btn.dataset.url;
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        if (!res.ok) { console.error('Wishlist toggle failed', res.status); return; }

        const data  = await res.json();
        const added = data?.state === 'added';

        // update ikon pada tombol ini
        const svg = btn.querySelector('svg');
        if (svg) {
          svg.classList.toggle('text-red-500', added);
          svg.classList.toggle('text-gray-600', !added);
          svg.setAttribute('fill', added ? 'currentColor' : 'none');
        }
        btn.setAttribute('aria-pressed', added ? 'true' : 'false');

        // update navbar (opsional: hanya jika id ada)
        const favHeart = document.getElementById('nav-fav-heart');   // svg hati di navbar
        const wlBadge  = document.getElementById('wishlist-badge');  // badge angka wishlist
        const wlCount  = Number(data?.wishlist_count ?? 0);
        const hasAny   = wlCount > 0;

        if (favHeart) {
          favHeart.classList.toggle('text-red-600', hasAny);
          favHeart.classList.toggle('text-gray-500', !hasAny);
          favHeart.setAttribute('fill', hasAny ? 'currentColor' : 'none');
        }
        if (wlBadge) {
          wlBadge.textContent = wlCount;
          wlBadge.classList.toggle('hidden', !hasAny);
        }

        // jika badge cart adalah total gabungan
        const cartBadge = document.getElementById('cart-badge');
        if (cartBadge && typeof data?.badge_total === 'number') {
          cartBadge.textContent = data.badge_total;
          cartBadge.dataset.wishlistCount = String(wlCount);
        }
      } catch (err) {
        console.error(err);
      }
    });
  </script>
</body>
</html>
