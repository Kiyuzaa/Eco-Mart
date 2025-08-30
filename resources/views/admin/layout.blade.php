layout.blade.php:
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>@yield('title', 'Panel Admin') — EcoMart</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <style type="text/tailwindcss">
    @layer utilities {
      .nav-link { @apply flex items-center gap-3 w-full h-10 px-3 rounded-md text-sm text-gray-100/90 hover:bg-white/10 transition; }
      .nav-active { @apply bg-white/10 text-white font-semibold; }
      .nav-icon { @apply w-4 h-4 text-gray-300; }
      .section { @apply px-3 text-[11px] uppercase tracking-wide text-gray-300/70 mt-3 mb-1; }
    }
  </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

<div class="min-h-screen flex">

  {{-- ========== SIDEBAR ========== --}}
  <aside
    id="sidebar"
    class="hidden lg:flex lg:w-64 fixed inset-y-0 left-0 z-40 flex-col bg-gradient-to-b from-slate-900 to-slate-800 text-gray-200 border-r border-slate-700 shadow-lg"
    aria-label="Sidebar Admin"
  >
    <div class="h-16 flex items-center justify-center border-b border-slate-700">
      <span class="text-lg font-extrabold tracking-wide text-emerald-400">Panel Admin</span>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
      <p class="section">Utama</p>

      <a href="{{ route('admin.products.index') }}"
         class="nav-link {{ request()->routeIs('admin.products.*') ? 'nav-active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M3 7h18l-2 11H5L3 7zM8 7l1.2-2.4A2 2 0 0 1 11 3h2a2 2 0 0 1 1.8 1.1L16 7"/>
        </svg>
        <span>Produk</span>
      </a>

      <a href="{{ route('admin.customers.index') }}"
         class="nav-link {{ request()->routeIs('admin.customers.*') ? 'nav-active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
          <path d="M16 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4Zm8 0c-.34 0-.68.02-1 .05 1.94 1.01 3 2.21 3 3.95v2h6v-2c0-2.66-5.33-4-8-4Z"/>
        </svg>
        <span>Pelanggan</span>
      </a>
    </nav>

    <div class="p-4 border-t border-slate-700 text-xs text-gray-400">
      © {{ date('Y') }} EcoMart Admin
    </div>
  </aside>

  {{-- Overlay untuk mobile --}}
  <div id="overlay" class="fixed inset-0 z-30 bg-black/40 hidden lg:hidden"></div>

  {{-- ========== AREA UTAMA ========== --}}
  <div class="flex-1 lg:pl-64 min-w-0">
    <header class="sticky top-0 z-20 h-16 bg-white/90 backdrop-blur border-b border-slate-200">
      <div class="h-full px-4 sm:px-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button id="openSidebar" class="lg:hidden inline-flex items-center justify-center rounded-md p-2 text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-label="Buka sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
          <div>
            <div class="text-base font-semibold text-slate-800">@yield('header-title', 'Dasbor')</div>
            <div class="text-xs text-slate-500">@yield('header-subtitle', 'Selamat datang di dasbor Anda')</div>
          </div>
        </div>

        <div class="flex items-center gap-4">
          @yield('header-button')

          {{-- Notifikasi (opsional) --}}
          <button class="relative p-2 rounded-full hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-label="Notifikasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </button>

          {{-- Avatar = LINK ke /profile --}}
          <a href="{{ url('/profile') }}" class="flex items-center gap-2 rounded-full hover:bg-slate-100">
            <img class="w-9 h-9 rounded-full border border-slate-200"
                 src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=10b981&color=fff"
                 alt="Profil">
            <span class="hidden sm:block text-sm font-medium text-slate-800">{{ auth()->user()->name ?? 'Admin' }}</span>
          </a>
        </div>
      </div>
    </header>

    <main class="p-4 sm:p-6">
      <div class="max-w-7xl mx-auto">
        @yield('content')
      </div>
    </main>
  </div>
</div>

<script>
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const openBtn  = document.getElementById('openSidebar');

  function openSidebarFn() {
    sidebar.classList.remove('hidden');
    overlay.classList.remove('hidden');
    sidebar.classList.add('animate-slide-in');
  }
  function closeSidebarFn() {
    sidebar.classList.add('hidden');
    overlay.classList.add('hidden');
  }
  openBtn?.addEventListener('click', openSidebarFn);
  overlay?.addEventListener('click', closeSidebarFn);
  window.addEventListener('keydown', (e)=>{ if(e.key==='Escape'){ closeSidebarFn(); }});
</script>

<style>
  @keyframes slideIn { from { transform: translateX(-100%); } to { transform: translateX(0); } }
  .animate-slide-in { animation: slideIn .18s ease-out; }
</style>
</body>
</html>