{{-- resources/views/about.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Tentang Kami — EcoMart</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="icon" href="{{ asset('images/logoEcomart.png') }}" type="image/png">
  <style>.about-hero{background:linear-gradient(180deg,#ffffff 0%,#f7faf9 60%,#f4faf6 100%)}</style>
</head>
<body class="antialiased bg-white text-slate-900">
  <x-navbar />

  {{-- HERO --}}
  <section class="about-hero border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <p class="text-emerald-700 font-semibold">Tentang EcoMart</p>
        <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold leading-tight">
          Belanja lebih bijak, <span class="text-emerald-700">bumi lebih sehat</span>
        </h1>
        <p class="mt-4 text-lg text-slate-600">
          EcoMart adalah toko berkelanjutan yang mengkurasi produk ramah lingkungan—dari kebutuhan rumah tangga,
          gaya hidup, hingga pilihan zero-waste—agar setiap transaksi membawa dampak baik.
        </p>
        @php $shopUrl = \Illuminate\Support\Facades\Route::has('product.index') ? route('product.index') : url('/product'); @endphp
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="{{ $shopUrl }}" class="inline-flex items-center rounded-xl bg-emerald-700 px-5 py-3 text-white hover:bg-emerald-800">Belanja Produk</a>
          <a href="#komitmen" class="inline-flex items-center rounded-xl border border-emerald-700 px-5 py-3 text-emerald-800 hover:bg-emerald-50">Komitmen Kami</a>
        </div>
      </div>

      <div class="relative">
        <div class="rounded-2xl border bg-white p-3 shadow-sm max-w-lg ml-auto">
          <img src="https://images.unsplash.com/photo-1543164904-8f8e6e89c7ec?q=80&w=1400&auto=format&fit=crop"
               alt="Tim EcoMart memilah produk berkelanjutan" class="rounded-xl w-full object-cover aspect-[4/3]">
        </div>
        <div class="absolute -bottom-4 -left-4 hidden sm:block">
          <span class="inline-block rounded-xl bg-emerald-100 text-emerald-700 px-3 py-1 text-sm shadow">
            🌿 Kemasan Minim Plastik
          </span>
        </div>
      </div>
    </div>
  </section>

  {{-- ANGKA IMPAK --}}
  <section class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-5">
      <div class="rounded-2xl border bg-white p-5 text-center">
        <div class="text-2xl font-bold text-slate-900">50k+</div>
        <p class="text-sm text-slate-600 mt-1">Pelanggan Dipuaskan</p>
      </div>
      <div class="rounded-2xl border bg-white p-5 text-center">
        <div class="text-2xl font-bold text-slate-900">2.3 ton</div>
        <p class="text-sm text-slate-600 mt-1">Plastik Dihindari*</p>
      </div>
      <div class="rounded-2xl border bg-white p-5 text-center">
        <div class="text-2xl font-bold text-slate-900">95%</div>
        <p class="text-sm text-slate-600 mt-1">Tingkat Kepuasan</p>
      </div>
      <div class="rounded-2xl border bg-white p-5 text-center">
        <div class="text-2xl font-bold text-slate-900">1M+</div>
        <p class="text-sm text-slate-600 mt-1">Produk Terkirim</p>
      </div>
    </div>
    <p class="text-center text-xs text-slate-500 mt-2">*Estimasi berdasarkan penggunaan kemasan & bahan isi ulang.</p>
  </section>

  {{-- KOMITMEN --}}
  <section id="komitmen" class="py-10 border-t">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold">Komitmen Keberlanjutan</h2>
      <p class="text-slate-600">Tiga pilar utama yang memandu kami.</p>

      <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="rounded-2xl border bg-white p-5">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M5 12c0 5 4 9 9 9 3 0 5-2 5-5 0-5-4-9-9-9-3 0-5 2-5 5Zm4.5 2.5C9.5 12 12 9.5 15 9.5c0 2.5-2.5 5-5.5 5.5Z"/></svg>
            </span>
            <h3 class="font-semibold text-slate-900">Kurasi Produk</h3>
          </div>
          <p class="text-sm text-slate-600 mt-3">Setiap produk lolos kurasi bahan, proses, serta dampak lingkungan yang terukur.</p>
        </div>

        <div class="rounded-2xl border bg-white p-5">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M21 8l-9-5-9 5 9 5 9-5Zm-9 7l-9-5v9l9 5 9-5v-9l-9 5Z"/></svg>
            </span>
            <h3 class="font-semibold text-slate-900">Kemasan Minim</h3>
          </div>
          <p class="text-sm text-slate-600 mt-3">Kami mengutamakan kemasan dapat didaur ulang, isi ulang, atau tanpa plastik sekali pakai.</p>
        </div>

        <div class="rounded-2xl border bg-white p-5">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h11v7h2.5l2 2H21V9h-3l-2-3H3V6Zm2 8a2 2 0 100 4 2 2 0 000-4Zm11 0a2 2 0 100 4 2 2 0 000-4Z"/></svg>
            </span>
            <h3 class="font-semibold text-slate-900">Logistik Hijau</h3>
          </div>
          <p class="text-sm text-slate-600 mt-3">Bermitra dengan kurir yang menawarkan opsi pengiriman rendah emisi.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- TIM KAMI (2 orang) --}}
  <section class="py-10 border-t">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold">Tim Kami</h2>
      <p class="text-slate-600">Orang-orang di balik EcoMart.</p>

      @php
        $team = [
          [
            'name' => 'Eza Fadlan Maulana',
            'role' => 'CEO & Founder',
            'img'  => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?q=80&w=800&auto=format&fit=crop',
          ],
          [
            'name' => 'Zakiyuddi Muhammad Syafiq',
            'role' => 'CTO & Co-Founder',
            'img'  => 'https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?q=80&w=800&auto=format&fit=crop',
          ],
        ];
      @endphp

      <div class="mt-6 grid grid-cols-2 md:grid-cols-2 gap-5">
        @foreach($team as $m)
          <div class="rounded-2xl border bg-white overflow-hidden">
            <img src="{{ $m['img'] }}" alt="Foto {{ $m['name'] }}" class="w-full h-56 object-cover">
            <div class="p-4">
              <div class="font-semibold">{{ $m['name'] }}</div>
              <div class="text-sm text-slate-600">{{ $m['role'] }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- LINIMASA --}}
  <section class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold">Perjalanan Kami</h2>
      <div class="mt-6 grid md:grid-cols-4 gap-5">
        @php
          $steps = [
            ['year'=>'2019','text'=>'Riset pasar dan kurasi pemasok lokal berkelanjutan.'],
            ['year'=>'2020','text'=>'EcoMart versi awal diluncurkan, 100 produk pertama.'],
            ['year'=>'2022','text'=>'Skalasi logistik hijau & program isi-ulang.'],
            ['year'=>'2024','text'=>'1M+ produk terkirim dengan kemasan minim plastik.'],
          ];
        @endphp
        @foreach($steps as $s)
          <div class="rounded-2xl border bg-white p-5">
            <div class="text-emerald-700 font-semibold">{{ $s['year'] }}</div>
            <p class="mt-1 text-sm text-slate-700">{{ $s['text'] }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- FAQ ringkas --}}
  <section class="py-10 border-t">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold text-center">Pertanyaan Umum</h2>
      <div class="mt-6 space-y-3">
        <details class="rounded-2xl border bg-white p-4">
          <summary class="cursor-pointer font-medium text-slate-900">Apakah semua produk benar-benar ramah lingkungan?</summary>
          <p class="mt-2 text-slate-600 text-sm">Kami menerapkan kurasi ketat atas bahan, proses produksi, dan kemasan. Tidak semua sempurna, namun kami transparan pada deskripsi produk.</p>
        </details>
        <details class="rounded-2xl border bg-white p-4">
          <summary class="cursor-pointer font-medium text-slate-900">Bagaimana kebijakan pengembalian?</summary>
          <p class="mt-2 text-slate-600 text-sm">Pengembalian 7 hari untuk produk non-consumable dalam kondisi baru. Lihat halaman kebijakan di footer untuk detail.</p>
        </details>
        <details class="rounded-2xl border bg-white p-4">
          <summary class="cursor-pointer font-medium text-slate-900">Apakah ada opsi pengiriman rendah emisi?</summary>
          <p class="mt-2 text-slate-600 text-sm">Ya, kami bermitra dengan kurir yang memiliki inisiatif pengurangan emisi dan rute efisien.</p>
        </details>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="rounded-3xl bg-emerald-700 text-white px-6 py-10 md:px-10 md:py-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <h3 class="text-2xl font-semibold">Bergabung dalam gerakan belanja sadar</h3>
        </div>
        <a href="{{ $shopUrl }}" class="inline-flex items-center rounded-xl bg-white px-5 py-3 text-emerald-800 hover:bg-emerald-50">
          Jelajahi Katalog
        </a>
      </div>
    </div>
  </section>

  <x-footer />
  @stack('scripts')
</body>
</html>
