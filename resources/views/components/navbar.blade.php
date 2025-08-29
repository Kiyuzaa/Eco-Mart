{{-- resources/views/components/navbar.blade.php --}}
@php
  $wish = (int) ($wishlistCount ?? 0);
  $cart = (int) ($cartCount ?? 0);
@endphp

<nav class="bg-white border-gray-200 sticky top-0 z-50 shadow-sm">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">

    {{-- Brand / Logo --}}
    <a href="{{ route('home') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
      <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="EcoMart Logo" />
      <span class="self-center text-2xl font-semibold whitespace-nowrap text-gray-900">EcoMart</span>
    </a>

    {{-- Right icons --}}
    <div class="flex items-center md:order-2 gap-3">

      {{-- WISHLIST --}}
      <a href="{{ route('wishlist') }}"
         class="relative hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 rounded-lg text-sm p-2.5">

        {{-- Ikon heart --}}
        <svg xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-6 h-6 {{ $wish>0 ? 'text-pink-600' : 'text-gray-500' }}"
             fill="{{ $wish>0 ? 'currentColor' : 'none' }}">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5
                   -1.935 0-3.597 1.126-4.312 2.733
                   -.715-1.607-2.377-2.733-4.313-2.733
                   C5.1 3.75 3 5.765 3 8.25c0 7.22
                   9 12 9 12s9-4.78 9-12Z"/>
        </svg>
        <span class="sr-only">Favorites</span>

        {{-- Badge wishlist --}}
        <span id="wishlist-badge"
              style="{{ $wish>0 ? '' : 'display:none' }}"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center
                     px-1.5 py-0.5 text-xs font-bold text-white bg-pink-600 rounded-full">
          {{ $wish }}
        </span>
      </a>

      {{-- CART --}}
      <a href="{{ route('cart.index') }}"
         class="relative hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 rounded-lg text-sm p-2.5">

        {{-- Ikon cart --}}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-6 h-6 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218
                   c1.121 0 2.1-.738 2.356-1.822l1.334-5.334A1.125 1.125 0 0 0 21.312 6H6.272
                   M7.5 14.25 5.106 5.272
                   M10.5 20.25a.75.75 0 1 1-1.5 0
                   .75.75 0 0 1 1.5 0Zm9 0a.75.75 0 1 1-1.5 0
                   .75.75 0 0 1 1.5 0Z" />
        </svg>
        <span class="sr-only">Cart</span>

        {{-- Badge cart --}}
        <span id="cart-badge"
              style="{{ $cart>0 ? '' : 'display:none' }}"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center
                     px-1.5 py-0.5 text-xs font-bold text-white bg-indigo-600 rounded-full">
          {{ $cart }}
        </span>
      </a>

      {{-- Profile --}}
      @auth
        <a href="{{ route('profile') }}" class="text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 rounded-lg text-sm p-2.5">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
          </svg>
          <span class="sr-only">Profile</span>
        </a>
      @else
        <a href="{{ route('login') }}" class="text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 rounded-lg text-sm p-2.5">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
          </svg>
          <span class="sr-only">Login</span>
        </a>
      @endauth

      {{-- Hamburger (mobile) --}}
      <button data-collapse-toggle="navbar-search" type="button"
        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden
               hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
        aria-controls="navbar-search" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
      </button>
    </div>

        <div class="flex items-center gap-3">
    {{-- Link admin khusus admin --}}
    @auth
      @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}"
           class="px-3 py-2 bg-indigo-600 text-white rounded">
           Admin Panel
        </a>
      @endif
    @endauth
    

    {{-- Menu items --}}
    <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-search">
      <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white">
        <li><a href="{{ route('home') }}" class="{{ request()->is('/') ? 'text-blue-700' : 'text-gray-900 hover:text-blue-700' }} block py-2 px-3 rounded md:p-0">Home</a></li>
        <li><a href="{{ route('product.index') }}" class="{{ request()->is('product*') ? 'text-blue-700' : 'text-gray-900 hover:text-blue-700' }} block py-2 px-3 rounded md:p-0">Shop</a></li>
        <li><a href="{{ route('ai.bot') }}" class="text-gray-900 hover:text-blue-700 block py-2 px-3 rounded md:p-0">AI Assistant</a></li>
        <li><a href="{{ route('about') }}" class="text-gray-900 hover:text-blue-700 block py-2 px-3 rounded md:p-0">About</a></li>
        <li><a href="#contact" class="text-gray-900 hover:text-blue-700 block py-2 px-3 rounded md:p-0">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<script>
  // Helper untuk update badge TANPA refresh
  window.updateCartBadge = function (count) {
    const el = document.getElementById('cart-badge');
    if (!el) return;
    const n = Number(count ?? 0);
    el.textContent = n;
    el.style.display = n > 0 ? 'inline-flex' : 'none';
  };
  window.updateWishlistBadge = function (count) {
    const el = document.getElementById('wishlist-badge');
    if (!el) return;
    const n = Number(count ?? 0);
    el.textContent = n;
    el.style.display = n > 0 ? 'inline-flex' : 'none';
  };
</script>
{{-- Akhir navbar --}}
