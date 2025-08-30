{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'EcoMart')</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css','resources/js/app.js'])
  @else
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <style>
      html{scroll-behavior:smooth}
      html,body{font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
    </style>
  @endif

  <script>
    window.APP = { wishlistToggleBase: "{{ url('/wishlist/toggle') }}" };
  </script>

  @stack('head')
</head>
<body class="antialiased bg-gray-50 text-gray-800">

  <x-navbar />

  <main class="max-w-[1400px] mx-auto px-4 lg:px-6 py-6 lg:py-8">
    @yield('content')
  </main>

  <x-footer />

  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>

  <script>
  // Handler global untuk tombol favorit
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-wishlist-button]');
    if (!btn) return;

    e.preventDefault();

    const url = btn.dataset.url
      || (window.APP?.wishlistToggleBase && btn.dataset.productId
          ? `${window.APP.wishlistToggleBase}/${btn.dataset.productId}`
          : '');
    if (!url) return console.error('Wishlist toggle URL tidak tersedia.');

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
      if (!res.ok) return console.error('Wishlist toggle gagal', res.status);

      const data  = await res.json();
      const added = data?.state === 'added';

      // toggle ikon tombol yang diklik
      const svg = btn.querySelector('svg');
      if (svg) {
        svg.classList.toggle('text-red-500', added);
        svg.classList.toggle('text-gray-600', !added);
        svg.setAttribute('fill', added ? 'currentColor' : 'none');
      }
      btn.setAttribute('aria-pressed', added ? 'true' : 'false');

      // sinkron badge navbar
      const favHeart = document.getElementById('nav-fav-heart');
      const wlBadge  = document.getElementById('wishlist-badge');
      const wlCount  = Number(data?.wishlist_count ?? 0);
      const hasAny   = wlCount > 0;

      if (favHeart) {
        favHeart.classList.toggle('text-pink-600', hasAny);
        favHeart.classList.toggle('text-gray-500', !hasAny);
        favHeart.setAttribute('fill', hasAny ? 'currentColor' : 'none');
      }
      if (wlBadge) {
        wlBadge.textContent = wlCount;
        wlBadge.style.display = hasAny ? 'inline-flex' : 'none';
      }

      const cartBadge = document.getElementById('cart-badge');
      if (cartBadge && typeof data?.badge_total === 'number') {
        cartBadge.textContent = data.badge_total;
        cartBadge.style.display = data.badge_total > 0 ? 'inline-flex' : 'none';
      }
    } catch (err) {
      console.error(err);
    }
  });
  </script>

  @stack('scripts')
</body>
</html>
