{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>EcoMart — Toko Berkelanjutan</title>

  {{-- Vite --}}
  @vite(['resources/css/app.css','resources/js/app.js'])

  <link rel="icon" href="{{ asset('images/logoEcomart.png') }}" type="image/png">
  <style>.hero-wrap{background:linear-gradient(180deg,#fff,#f7faf9 60%,#f6f9f7,) }</style>
</head>
<body class="antialiased bg-white text-slate-900">

  {{-- NAVBAR --}}
  <x-navbar />

  {{-- HERO --}}
  <section class="hero-wrap">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16 grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
          Belanja Berkelanjutan, <span class="text-emerald-700">Hidup Lebih Baik</span>
        </h1>
        <p class="mt-4 text-slate-600 text-lg">
          Temukan produk ramah lingkungan yang berdampak positif bagi bumi. Dari kebutuhan zero-waste hingga fashion berkelanjutan.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          @php $productsUrl = Route::has('product.index') ? route('product.index') : url('/product'); @endphp
          <a href="{{ $productsUrl }}" class="inline-flex items-center rounded-xl bg-emerald-700 px-5 py-3 text-white hover:bg-emerald-800">
            Belanja Sekarang
          </a>
          <a href="{{ Route::has('about') ? route('about') : '#' }}"
             class="inline-flex items-center rounded-xl border border-emerald-700 px-5 py-3 text-emerald-800 hover:bg-emerald-50">
            Pelajari EcoMart
          </a>
        </div>

        {{-- trust badges --}}
        <div class="mt-8 grid grid-cols-3 gap-4 text-center">
          <div><div class="text-xl font-bold text-slate-900">50k+</div><div class="text-xs text-slate-500">Pelanggan Puas</div></div>
          <div><div class="text-xl font-bold text-slate-900">95%</div><div class="text-xs text-slate-500">Tingkat Kepuasan</div></div>
          <div><div class="text-xl font-bold text-slate-900">1M+</div><div class="text-xs text-slate-500">Produk Terkirim</div></div>
        </div>
      </div>

      {{-- Ilustrasi --}}
      <div class="relative">
        <div class="rounded-2xl border bg-white p-3 shadow-sm max-w-lg mx-auto">
          <img src="https://images.unsplash.com/photo-1511988617509-a57c8a288659?q=80&w=1200&auto=format&fit=crop"
               alt="Produk ramah lingkungan" class="rounded-xl w-full object-cover aspect-[4/3]">
        </div>
        <div class="absolute -bottom-4 -left-4 hidden sm:block">
          <span class="inline-block rounded-xl bg-emerald-100 text-emerald-800 px-3 py-1 text-sm shadow">
            🌿 Kemasan Minim Plastik
          </span>
        </div>
      </div>
    </div>
  </section>

  {{-- KEUNGGULAN --}}
  <section class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="rounded-2xl border p-5 bg-white">
        <div class="text-emerald-700 font-semibold">Ramah Lingkungan</div>
        <p class="text-sm text-slate-600 mt-1">Produk dipilih dengan jejak karbon lebih rendah.</p>
      </div>
      <div class="rounded-2xl border p-5 bg-white">
        <div class="text-emerald-700 font-semibold">Bahan Berkualitas</div>
        <p class="text-sm text-slate-600 mt-1">Aman digunakan, awet, dan bersertifikasi.</p>
      </div>
      <div class="rounded-2xl border p-5 bg-white">
        <div class="text-emerald-700 font-semibold">Pengiriman Hijau</div>
        <p class="text-sm text-slate-600 mt-1">Opsi pengiriman minim emisi & kemasan daur ulang.</p>
      </div>
      <div class="rounded-2xl border p-5 bg-white">
        <div class="text-emerald-700 font-semibold">Harga Transparan</div>
        <p class="text-sm text-slate-600 mt-1">Tanpa biaya tersembunyi, mudah dibandingkan.</p>
      </div>
    </div>
  </section>

  {{-- KATEGORI (fixed 4 dari database + link ke produk) --}}
  <section class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-end justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-slate-900">Belanja per Kategori</h2>
          <p class="text-slate-600">Empat kategori utama dari katalog kami.</p>
        </div>
        @php $productsUrl = $productsUrl ?? (Route::has('product.index') ? route('product.index') : url('/product')); @endphp
        <a href="{{ $productsUrl }}"
           class="hidden sm:inline-flex items-center rounded-xl border px-4 py-2 text-emerald-800 border-emerald-700 hover:bg-emerald-50">
          Lihat Semua
        </a>
      </div>

      @php
        use Illuminate\Support\Str;

        // Urutan kategori yang diinginkan (sesuai seeder)
        $desired = ['Toys', 'Fashion', 'Health & Beauty', 'Books'];

        // Ambil dari DB berdasarkan "name"
        $fromDb = \App\Models\Category::query()
                  ->whereIn('name', $desired)
                  ->get()
                  ->keyBy('name');

        // Bangun kartu sesuai urutan $desired
        $cards = collect($desired)->map(function ($name) use ($fromDb) {
            $row  = $fromDb->get($name);
            // Seeder membuat slug unik: Str::slug($name) . '-' . ($i+1)
            $slug = $row ? $row->slug : Str::slug($name);
            // Gambar default per kategori (ganti ke asset() jika punya gambar lokal)
            $img = match ($name) {
                'Toys'             => 'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?q=80&w=800&auto=format&fit=crop',
                'Fashion'          => 'https://images.unsplash.com/photo-1520975916090-3105956dac38?q=80&w=800&auto=format&fit=crop',
                'Health & Beauty'  => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?q=80&w=800&auto=format&fit=crop',
                'Books'            => 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?q=80&w=800&auto=format&fit=crop',
                default            => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=800&auto=format&fit=crop',
            };
            // URL ke halaman produk dengan filter category=slug
            $url = (Route::has('product.index'))
                ? route('product.index', ['category' => $slug])
                : url('/product?category=' . $slug);

            return compact('name', 'slug', 'img', 'url');
        });
      @endphp

      <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-5">
        @foreach($cards as $c)
          <a href="{{ $c['url'] }}" class="group rounded-2xl overflow-hidden border bg-white hover:shadow-md transition">
            <div class="aspect-[4/3] overflow-hidden">
              <img src="{{ $c['img'] }}" alt="{{ $c['name'] }}"
                   class="w-full h-full object-cover group-hover:scale-105 transition"
                   onerror="this.src='https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=800&auto=format&fit=crop'">
            </div>
            <div class="p-3 text-center font-medium text-slate-800">{{ $c['name'] }}</div>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  {{-- PRODUK UNGGULAN --}}
  <section class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-end justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-slate-900">Produk Unggulan</h2>
          <p class="text-slate-600">Pilihan terbaik untuk mulai hidup lebih berkelanjutan.</p>
        </div>
        <a href="{{ $productsUrl }}"
           class="hidden sm:inline-flex items-center rounded-xl border px-4 py-2 text-emerald-800 border-emerald-700 hover:bg-emerald-50">
          Lihat Semua
        </a>
      </div>

      @if(isset($products) && $products instanceof \Illuminate\Contracts\Pagination\Paginator && $products->count())
        <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
          @foreach($products as $product)
            <x-product-card :product="$product" />
          @endforeach
        </div>
        <div class="mt-8">{{ $products->withQueryString()->links() }}</div>
      @elseif(isset($products) && $products instanceof \Illuminate\Support\Collection && $products->count())
        <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
          @foreach($products as $product)
            <x-product-card :product="$product" />
          @endforeach
        </div>
      @else
        <div class="mt-8 rounded-2xl border p-6 bg-white text-center">
          <p class="text-slate-600">Produk unggulan belum tersedia saat ini.</p>
          <div class="mt-4">
            <a href="{{ $productsUrl }}" class="inline-flex items-center rounded-xl bg-emerald-700 px-4 py-2 text-white hover:bg-emerald-800">
              Jelajahi Katalog
            </a>
          </div>
        </div>
      @endif
    </div>
  </section>

  {{-- KONTAK (desain baru, lebih bersih) --}}
  @php $contactAction = Route::has('contact.send') ? route('contact.send') : '#'; @endphp
  <section id="contact" class="py-12 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="rounded-3xl border bg-white/80 backdrop-blur p-6 md:p-8">
        <div class="grid lg:grid-cols-5 gap-8">
          {{-- Info singkat --}}
          <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold text-slate-900">Hubungi Kami</h2>
            <p class="text-slate-600 mt-1">Ada pertanyaan tentang produk atau pesanan? Tim kami siap membantu.</p>

            <ul class="mt-6 space-y-3 text-slate-700">
              <li class="flex items-center gap-3">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                  {{-- mail icon --}}
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16a2 2 0 012 2v.4l-10 6.25L2 8.4V8a2 2 0 012-2Zm18 4.35V16a2 2 0 01-2 2H4a2 2 0 01-2-2v-5.65l9.24 5.78a 2 2 0 002.04 0L22 10.35Z"/></svg>
                </span>
                support@ecomart.local
              </li>
              <li class="flex items-center gap-3">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                  {{-- phone --}}
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24 11.36 11.36 0 003.56.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h2.49a1 1 0 011 1 11.36 11.36 0 00.57 3.56 1 1 0 01-.24 1.05l-2.2 2.2Z"/></svg>
                </span>
                0800-ECO-MART
              </li>
              <li class="flex items-center gap-3">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                  {{-- pin --}}
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7Zm0 9.5A2.5 2.5 0 119.5 9 2.5 2.5 0 0112 11.5Z"/></svg>
                </span>
                Jakarta, Indonesia
              </li>
            </ul>

            <div class="mt-6 flex gap-3 text-gray-500">
              <a href="#" class="hover:text-emerald-700" aria-label="Instagram">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5Zm5 4.5A5.5 5.5 0 1017.5 12 5.5 5.5 0 0012 6.5ZM18 7.25a.75.75 0 10.75.75.75.75 0 00-.75-.75Z"/></svg>
              </a>
              <a href="#" class="hover:text-emerald-700" aria-label="Twitter/X">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h3.7l4.13 6.18L16.9 4H21l-7.56 9.77L21 20h-3.68l-4.6-6.68L7.1 20H3l7.85-10.19L4 4Z"/></svg>
              </a>
              <a href="#" class="hover:text-emerald-700" aria-label="YouTube">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3.1 3.1 0 00-2.2-2.2C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.3.5A3.1 3.1 0 00.5 6.2 32.8 32.8 0 000 12a32.8 32.8 0 00.5 5.8 3.1 3.1 0 002.2 2.2c1.8.5 9.3.5 9.3.5s7.5 0 9.3-.5a3.1 3.1 0 002.2-2.2c.4-1.8.5-3.8.5-5.8s0-4-.5-5.8zM9.8 15.3V8.7l6.2 3.3-6.2 3.3z"/></svg>
              </a>
            </div>
          </div>

          {{-- Form compact --}}
          <form class="lg:col-span-3 grid gap-4 rounded-2xl border bg-white p-5"
                method="POST"
                action="{{ $contactAction }}"
                @if($contactAction === '#') onsubmit="event.preventDefault(); alert('Terima kasih! Pesanmu terkirim.');" @endif>
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="text-sm text-slate-600">Nama</label>
                <input name="name" required
                       class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
              </div>
              <div>
                <label class="text-sm text-slate-600">Email</label>
                <input name="email" type="email" required
                       class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
              </div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Subjek</label>
              <input name="subject"
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
            </div>
            <div>
              <label class="text-sm text-slate-600">Pesan</label>
              <textarea name="message" rows="5" required
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"></textarea>
            </div>
            <div class="flex justify-end">
              <button class="inline-flex items-center rounded-xl bg-emerald-700 px-5 py-3 text-white hover:bg-emerald-800">
                Kirim Pesan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  {{-- FOOTER --}}
  <x-footer />

  @stack('scripts')
</body>
</html>
