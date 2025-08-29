{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'EcoMart')</title>

  {{-- CSRF untuk AJAX --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
      <link rel="preconnect" href="https://fonts.bunny.net" />
      <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
      <script src="https://cdn.tailwindcss.com"></script>
      <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
      <style>
        html,body{
          font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
          background:#F9FAFB;color:#0F172A
        }
      </style>
  @endif

  <script>
    // base url untuk fallback jika tombol hanya punya data-product-id
    window.APP = { wishlistToggleBase: "{{ url('/wishlist/toggle') }}" };
  </script>
</head>
<body class="antialiased bg-gray-50 text-gray-800">

  {{-- NAVBAR --}}
  <x-navbar />

  {{-- PAGE CONTENT --}}
  <main class="max-w-[1400px] mx-auto px-4 lg:px-6 py-6 lg:py-8">
    @yield('content')
  </main>

  {{-- FOOTER --}}
  <x-footer />

  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>

  {{-- Handler global toggle wishlist (dipakai card & detail) --}}
  <script>
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-wishlist-button]');
    if (!btn) return;

    e.preventDefault();

    const explicit = btn.dataset.url || '';
    const pid      = btn.dataset.productId || '';
    const base     = (window.APP && window.APP.wishlistToggleBase) ? window.APP.wishlistToggleBase : '';
    const url      = explicit || (base && pid ? `${base}/${pid}` : '');

    if (!url) {
      console.error('Wishlist toggle URL missing.');
      return;
    }

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

      if (!res.ok) {
        console.error('Wishlist toggle failed', res.status);
        return;
      }

      const data  = await res.json();
      const added = data?.state === 'added';

      // Ubah ikon pada tombol yang diklik
      const svg = btn.querySelector('svg');
      if (svg) {
        svg.classList.toggle('text-red-500', added);
        svg.classList.toggle('text-gray-600', !added);
        svg.setAttribute('fill', added ? 'currentColor' : 'none');
      }
      btn.setAttribute('aria-pressed', added ? 'true' : 'false');

      // Sinkron ke ikon hati navbar + badge wishlist
      const favHeart = document.getElementById('nav-fav-heart');
      const wlBadge  = document.getElementById('wishlist-badge');
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

      // (opsional) badge total di cart jika kamu pakai gabungan cart+wishlist
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

  {{-- tempat script tambahan halaman --}}
  @stack('scripts')
</body>
</html>
