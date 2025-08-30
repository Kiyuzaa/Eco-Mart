{{-- resources/views/components/navbar.blade.php --}}
@php
  $wish = (int) ($wishlistCount ?? 0);
  $cart = (int) ($cartCount ?? 0);
  $is = fn(string $pattern) => request()->is($pattern) ? 'text-emerald-800 font-semibold' : 'text-gray-900 hover:text-emerald-800';
@endphp

<nav class="bg-white/95 backdrop-blur border-b border-gray-100 sticky top-0 z-50 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">

    {{-- Brand --}}
    <a href="{{ route('home') }}" class="flex items-center gap-2">
      <img src="{{ asset('images/logoEcomart.png') }}" class="h-8 w-8 object-contain" alt="EcoMart Logo">
      <span class="text-xl font-semibold text-emerald-900">EcoMart</span>
    </a>

    {{-- Menu desktop --}}
    <ul class="hidden md:flex items-center gap-8 font-medium">
      <li><a href="{{ route('home') }}" class="{{ $is('/') }}">Beranda</a></li>
      <li><a href="{{ route('product.index') }}" class="{{ $is('product*') }}">Produk</a></li>
      <li><a href="{{ route('ai.bot') }}" class="{{ $is('ai*') }}">Asisten AI</a></li>
      <li><a href="{{ route('about') }}" class="{{ $is('about') }}">Tentang</a></li>
      <li><a href="{{ route('contact') }}" class="{{ $is('contact*') }}">Kontak</a></li>
      @auth
        @if(method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin())
          <li>
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white hover:bg-emerald-800">Admin Panel</a>
          </li>
        @endif
      @endauth
    </ul>

    {{-- Aksi kanan --}}
    <div class="flex items-center gap-2 md:gap-3">
      {{-- Wishlist --}}
      <a href="{{ route('wishlist') }}" class="relative inline-flex items-center justify-center rounded-xl p-2 text-gray-600 hover:bg-gray-100" aria-label="Wishlist">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $wish>0 ? 'text-pink-600' : 'text-gray-500' }}"
             fill="{{ $wish>0 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5
            -1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733
            C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
        </svg>
        <span class="sr-only">Wishlist</span>
        <span id="wishlist-badge" style="{{ $wish>0 ? '' : 'display:none' }}"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold text-white bg-pink-600 rounded-full">
          {{ $wish }}
        </span>
      </a>

      {{-- Cart --}}
      <a href="{{ route('cart.index') }}" class="relative inline-flex items-center justify-center rounded-xl p-2 text-gray-600 hover:bg-gray-100" aria-label="Keranjang">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="w-6 h-6 text-gray-500" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218
            c1.121 0 2.1-.738 2.356-1.822l1.334-5.334A1.125 1.125 0 0 0 21.312 6H6.272
            M7.5 14.25 5.106 5.272
            M10.5 20.25a.75.75 0 1 1-1.5 0
            .75.75 0 0 1 1.5 0Zm9 0a.75.75 0 1 1-1.5 0
            .75.75 0 0 1 1.5 0Z" />
        </svg>
        <span class="sr-only">Keranjang</span>
        <span id="cart-badge" style="{{ $cart>0 ? '' : 'display:none' }}"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold text-white bg-amber-600 rounded-full">
          {{ $cart }}
        </span>
      </a>

      {{-- Profil / Login --}}
      @auth
        <a href="{{ route('profile') }}" class="inline-flex items-center justify-center rounded-xl p-2 text-gray-600 hover:bg-gray-100" aria-label="Profil">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z"/>
          </svg>
        </a>
      @else
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl p-2 text-gray-600 hover:bg-gray-100" aria-label="Masuk">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z"/>
          </svg>
        </a>
      @endauth

      {{-- Hamburger --}}
      <button id="hamburger-btn" type="button"
              class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 hover:bg-gray-50"
              aria-controls="mobile-panel" aria-expanded="false" aria-label="Buka menu">
        <svg id="icon-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 17 14" fill="none">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
        <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none">
          <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/>
        </svg>
      </button>
    </div>
  </div>

  {{-- Dropdown mobile (wrapper relative, panel absolute) --}}
  <div id="mobile-menu" class="md:hidden relative">
    <div id="mobile-panel" class="hidden absolute right-4 top-2 w-[92%] sm:w-80 rounded-2xl border border-gray-200 bg-white shadow-xl z-50">
      <ul class="p-2">
        <li><a href="{{ route('home') }}" class="block px-4 py-2.5 rounded-lg {{ $is('/') }}">Beranda</a></li>
        <li><a href="{{ route('product.index') }}" class="block px-4 py-2.5 rounded-lg {{ $is('product*') }}">Produk</a></li>
        <li><a href="{{ route('ai.bot') }}" class="block px-4 py-2.5 rounded-lg {{ $is('ai*') }}">Asisten AI</a></li>
        <li><a href="{{ route('about') }}" class="block px-4 py-2.5 rounded-lg {{ $is('about') }}">Tentang</a></li>
        <li><a href="{{ route('contact') }}" class="block px-4 py-2.5 rounded-lg {{ $is('contact*') }}">Kontak</a></li>
        @auth
          @if(method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin())
            <li class="px-4 py-2.5">
              <a href="{{ route('admin.dashboard') }}" class="block w-full text-center rounded-xl bg-emerald-700 px-4 py-2 text-white hover:bg-emerald-800">
                Admin Panel
              </a>
            </li>
          @endif
        @endauth
      </ul>

      <div class="px-4 pb-4">
        @auth
          <div class="flex gap-2">
            <a href="{{ route('profile') }}" class="flex-1 rounded-xl border px-4 py-2 text-gray-700 hover:border-emerald-700 hover:text-emerald-800 text-center">Profil</a>
            <form action="{{ route('logout') }}" method="POST" class="flex-1">@csrf
              <button class="w-full rounded-xl bg-red-600 px-4 py-2 text-white hover:bg-red-700">Keluar</button>
            </form>
          </div>
        @else
          <div class="flex gap-2">
            <a href="{{ route('login') }}" class="flex-1 rounded-xl border px-4 py-2 text-gray-700 hover:border-emerald-700 hover:text-emerald-800 text-center">Masuk</a>
            <a href="{{ route('register') }}" class="flex-1 rounded-xl bg-emerald-700 px-4 py-2 text-white hover:bg-emerald-800 text-center">Daftar</a>
          </div>
        @endauth
      </div>
    </div>
  </div>
</nav>

<script>
(function () {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }

  function init() {
    const btn = document.getElementById('hamburger-btn');
    const menu = document.getElementById('mobile-menu');     // wrapper (relative)
    const panel = document.getElementById('mobile-panel');   // panel (absolute)
    const openIcon  = document.getElementById('icon-open');
    const closeIcon = document.getElementById('icon-close');

    if (!btn || !menu || !panel || !openIcon || !closeIcon) return;

    // cegah double-binding saat komponen dirender ulang
    if (btn.dataset.bound === '1') return;
    btn.dataset.bound = '1';

    const isOpen = () => !panel.classList.contains('hidden');

    function setState(opened) {
      panel.classList.toggle('hidden', !opened);
      openIcon.classList.toggle('hidden', opened);
      closeIcon.classList.toggle('hidden', !opened);
      btn.setAttribute('aria-expanded', opened ? 'true' : 'false');
      panel.setAttribute('aria-hidden', opened ? 'false' : 'true');
    }

    // start tertutup
    setState(false);

    // toggle via tombol
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      setState(!isOpen());
    });

    // klik di luar menutup
    document.addEventListener('click', (e) => {
      if (!isOpen()) return;
      if (!btn.contains(e.target) && !panel.contains(e.target)) {
        setState(false);
      }
    });

    // ESC menutup
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && isOpen()) {
        setState(false);
        btn.focus();
      }
    });

    // handle perubahan breakpoint md
    let lastIsMobile = window.innerWidth < 768;
    window.addEventListener('resize', () => {
      const isMobile = window.innerWidth < 768;
      if (isMobile !== lastIsMobile) {
        setState(false);
        lastIsMobile = isMobile;
      }
    });
  }
})();
</script>
