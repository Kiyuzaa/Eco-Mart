<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Admin Panel') — EcoMart</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* versi CSS biasa (pengganti @apply) */
        .nav-link{
            display:flex; align-items:center; gap:12px;
            width:100%; padding:10px 12px; font-size:14px;
            color:#9ca3af; border-radius:8px; text-decoration:none;
        }
        .nav-link:hover{ background:#374151; color:#fff; }
        .nav-active{ background:#374151; color:#fff; font-weight:600; }
        .nav-icon{ width:24px; height:24px; }

        /* Sidebar precision reset (tetap dari punyamu, tapi tanpa @apply) */
        .sidebar .nav-link{
            display:flex; align-items:center; gap:12px;
            height:40px; padding:0 12px; border-radius:6px;
            font-size:14px; color:rgba(255,255,255,.9);
        }
        .sidebar .nav-link:hover{ background:rgba(255,255,255,.1); }
        .sidebar .nav-active{ background:rgba(255,255,255,.1); color:#fff; }
        .sidebar .nav-icon{ width:16px; height:16px; flex:0 0 16px; color:rgba(255,255,255,.75); }
        .sidebar svg, .sidebar img{ width:16px; height:16px; flex:0 0 16px; }
        .sidebar .section{
            padding:0 12px; font-size:11px; text-transform:uppercase;
            letter-spacing:.06em; color:rgba(255,255,255,.7); margin:12px 0 6px;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 antialiased">
<div class="min-h-screen flex">

  {{-- ========== SIDEBAR ========== --}}
  <aside class="sidebar w-56 bg-[#0f1b2a] text-white flex flex-col border-r border-black/10">
    {{-- header brand --}}
    <div class="h-14 flex items-center px-4 border-b border-white/10">
      <span class="text-[14px] font-semibold">Admin Panel</span>
    </div>

    {{-- menu list (PASTIKAN CUMA ADA SATU) --}}
    <nav class="flex-1 py-3 px-2">
      <div class="section">Main</div>

      <a href="{{ route('admin.dashboard') }}"
         class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M3 7h18l-2 11H5L3 7zM8 7l1.2-2.4A2 2 0 0 1 11 3h2a2 2 0 0 1 1.8 1.1L16 7"/>
        </svg>
        <span>Products</span>
      </a>

      <a href="#" class="nav-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3 3h18v2H3zM7 7h2v14H7zM11 11h2v10h-2zM15 9h2v12h-2z"/>
        </svg>
        <span>Analytics</span>
      </a>

      <a href="#" class="nav-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
          <path d="M16 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4Zm8 0c-.34 0-.68.02-1 .05 1.94 1.01 3 2.21 3 3.95v2h6v-2c0-2.66-5.33-4-8-4Z"/>
        </svg>
        <span>Customers</span>
      </a>

      <div class="section">Settings</div>

      <a href="#" class="nav-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
          <path d="M19.14 12.94a7 7 0 0 0 0-1.88l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96c-.49-.39-1.04-.7-1.62-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.5.42l-.36 2.54c-.58.24-1.12.55-1.62.94l-2.39-.96a.5.5 0 0 0-.6.22L2.91 7.9a.5.5 0 0 0 .12.64l2.03 1.58a7.14 7.14 0 0 0 0 1.88L3.03 13.6a.5.5 0 0 0-.12.64l1.92 3.32c.14.24.43.33.67.22l2.39-.96c.5.41 1.04.72 1.62.94l.36 2.54c.04.24.25.42.5.42h3.8c.25 0 .46-.18.5-.42l.36-2.54c.58-.22 1.12-.53 1.62-.94l2.39.96c.24.11.53.02.67-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.58Z"/>
        </svg>
        <span>Settings</span>
      </a>
    </nav>
  </aside>

  {{-- ========== MAIN AREA ========== --}}
  <main class="flex-1 min-w-0">
    {{-- topbar --}}
    <div class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
      <div>
        <div class="text-lg font-semibold text-gray-800">@yield('header-title', 'Dashboard')</div>
        <div class="text-sm text-gray-500">@yield('header-subtitle', 'Welcome to your dashboard')</div>
      </div>
      <div class="flex items-center gap-4">
        @yield('header-button')
        <button class="text-gray-500 hover:text-gray-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.1-1.6-5.6-4.5-6.3V4c0-.8-.7-1.5-1.5-1.5s-1.5.7-1.5 1.5v.7C7.6 5.4 6 7.9 6 11v5l-2 2v1h16v-1l-2-2z"/>
          </svg>
        </button>
        <img class="w-8 h-8 rounded-full" src="https://i.pravatar.cc/150" alt="Admin">
      </div>
    </div>

    <div class="p-6">
      <div class="max-w-7xl mx-auto">
        @yield('content')
      </div>
    </div>
  </main>
</div>
</body>
</html>
