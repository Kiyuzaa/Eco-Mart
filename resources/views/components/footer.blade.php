{{-- resources/views/components/footer.blade.php --}}
@php
  use Illuminate\Support\Facades\Route as R;

  // Safe URLs + fallback
  $home       = R::has('home') ? route('home') : url('/');
  $about      = R::has('about') ? route('about') : $home;
  $products   = R::has('product.index') ? route('product.index') : $home;
  $privacy    = R::has('privacy') ? route('privacy') : $about;
  $terms      = R::has('terms') ? route('terms') : $about;
  $faq        = R::has('faq') ? route('faq') : $about;
  $returns    = R::has('returns') ? route('returns') : $about;
  $shipping   = R::has('shipping') ? route('shipping') : $about;
  $newsletter = R::has('newsletter.subscribe') ? route('newsletter.subscribe') : null;

  $adminPanel = (auth()->check() && method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin() && R::has('admin.dashboard'))
                ? route('admin.dashboard') : null;

  // "Old navigation" gaya Shop (kategori)
  $categories = [
    'all'        => 'All Products',
    'fashion'    => 'Fashion',
    'home'       => 'Household',
    'beauty'     => 'Beauty',
    'kids'       => 'Kids',
    'zero-waste' => 'Zero Waste',
  ];

  // Buat URL kategori: pakai route('category.show', $slug) jika ada, kalau tidak -> product.index?category=slug
  $catUrl = function (string $slug) use ($products) {
    return R::has('category.show')
      ? route('category.show', $slug)
      : $products . (str_contains($products, '?') ? '&' : '?') . 'category=' . urlencode($slug);
  };

  $tahun = date('Y');
@endphp

<footer class="mt-12 border-t bg-white">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

    {{-- Pita info --}}
    <div class="py-4 text-center text-sm text-emerald-900/80 bg-emerald-50/60 border-x border-t border-emerald-100 rounded-b-none rounded-2xl">
      🌱 Pengiriman ramah lingkungan & kemasan minim plastik untuk setiap pesanan.
    </div>

    {{-- Kolom utama (4 kolom responsif) --}}
    <div class="py-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

      {{-- Brand --}}
      <div>
        <a href="{{ $home }}" class="flex items-center gap-2">
          <img src="{{ asset('images/logoEcomart.png') }}" alt="EcoMart" class="h-9 w-9">
          <span class="font-semibold text-emerald-900 text-xl">EcoMart</span>
        </a>
        <p class="mt-3 text-sm leading-6 text-gray-600">
          Toko berkelanjutan untuk kebutuhan harian: dari alat rumah tangga, mode berkelanjutan,
          hingga produk zero-waste. Beli bijak, rawat bumi.
        </p>

        {{-- Sosial --}}
        <div class="mt-4 flex items-center gap-3 text-gray-500">
          <a href="#" aria-label="Instagram" class="hover:text-emerald-700 transition">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2C4.243 2 2 4.243 2 7v10c0 2.757 2.243 5 5 5h10c2.757 0 5-2.243 5-5V7c0-2.757-2.243-5-5-5H7zm10 2c1.654 0 3 1.346 3 3v10c0 1.654-1.346 3-3 3H7c-1.654 0-3-1.346-3-3V7c0-1.654 1.346-3 3-3h10zM12 7a5 5 0 100 10 5 5 0 000-10zm5.5 1.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
          </a>
          <a href="#" aria-label="X / Twitter" class="hover:text-emerald-700 transition">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.8l-6.6 12.2H13l2.5-4.6-4.5-7.6h3l3 5.3 2.8-5.3H22zM9.7 6.7L2 18.2h3.1l2.1-3.5h3.4l1.1-1.9H8.3l3-5.1H9.7z"/></svg>
          </a>
          <a href="#" aria-label="YouTube" class="hover:text-emerald-700 transition">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3.1 3.1 0 00-2.2-2.2C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.3.5A3.1 3.1 0 00.5 6.2 32.8 32.8 0 000 12a32.8 32.8 0 00.5 5.8 3.1 3.1 0 002.2 2.2c1.8.5 9.3.5 9.3.5s7.5 0 9.3-.5a3.1 3.1 0 002.2-2.2c.4-1.8.5-3.8.5-5.8s0-4-.5-5.8zM9.8 15.3V8.7l6.2 3.3-6.2 3.3z"/></svg>
          </a>
        </div>
      </div>

      {{-- Navigasi (tetap) --}}
      <div>
        <h4 class="font-semibold text-gray-900 mb-3">Navigasi</h4>
        <ul class="space-y-2 text-gray-600">
          <li><a class="hover:text-emerald-700" href="{{ $home }}">Beranda</a></li>
          <li><a class="hover:text-emerald-700" href="{{ $products }}">Produk</a></li>
          <li><a class="hover:text-emerald-700" href="{{ $about }}">Tentang</a></li>
          <li><a class="hover:text-emerald-700" href="#contact">Kontak</a></li>
          @if($adminPanel)
            <li><a class="hover:text-emerald-700" href="{{ $adminPanel }}">Admin Panel</a></li>
          @endif
        </ul>
      </div>

      {{-- Shop (navigasi lama / kategori) --}}
      <div>
        <h4 class="font-semibold text-gray-900 mb-3">Shop</h4>
        <ul class="space-y-2 text-gray-600">
          @foreach($categories as $slug => $label)
            @php
              $url = $slug === 'all' ? $products : $catUrl($slug);
            @endphp
            <li><a class="hover:text-emerald-700" href="{{ $url }}">{{ $label }}</a></li>
          @endforeach
        </ul>
      </div>

      {{-- Bantuan + Newsletter (desain sekarang) --}}
      <div>
        <h4 class="font-semibold text-gray-900 mb-3">Bantuan</h4>
        <ul class="space-y-2 text-gray-600">
          <li><a class="hover:text-emerald-700" href="{{ $faq }}">FAQ</a></li>
          <li><a class="hover:text-emerald-700" href="{{ $terms }}">Syarat & Ketentuan</a></li>
          <li><a class="hover:text-emerald-700" href="{{ $privacy }}">Kebijakan Privasi</a></li>
          <li><a class="hover:text-emerald-700" href="{{ $returns }}">Pengembalian</a></li>
          <li><a class="hover:text-emerald-700" href="{{ $shipping }}">Pengiriman</a></li>
        </ul>

        {{-- Newsletter --}}
        <div class="mt-5">
          <h5 class="font-semibold text-gray-900 mb-2">Berlangganan</h5>
          @if($newsletter)
            <form action="{{ $newsletter }}" method="POST" class="flex items-stretch gap-2">
              @csrf
              <input type="email" name="email" placeholder="Email kamu"
                     class="w-full rounded-xl border-gray-300 focus:ring-emerald-600 focus:border-emerald-600" required>
              <button class="rounded-xl bg-emerald-700 px-4 py-2 text-white hover:bg-emerald-800">
                Langganan
              </button>
            </form>
          @else
            <form action="#" method="GET" class="flex items-stretch gap-2" onsubmit="return false;">
              <input type="email" placeholder="Email kamu"
                     class="w-full rounded-xl border-gray-300 focus:ring-emerald-600 focus:border-emerald-600">
              <button type="button" class="rounded-xl bg-emerald-700 px-4 py-2 text-white opacity-60 cursor-not-allowed">
                Langganan
              </button>
            </form>
          @endif
        </div>
      </div>

    </div>

    {{-- Strip bawah --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t py-5 text-sm text-gray-600">
      <p>© {{ $tahun }} <span class="font-medium text-emerald-900">EcoMart</span>. Semua hak dilindungi.</p>
      <div class="flex items-center gap-4">
        <a href="{{ $privacy }}" class="hover:text-emerald-700">Privasi</a>
        <a href="{{ $terms }}" class="hover:text-emerald-700">Ketentuan</a>
        <a href="#top" class="inline-flex items-center gap-1 hover:text-emerald-700">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5l7 7-1.4 1.4L13 9.8V20h-2V9.8l-4.6 3.6L5 12z"/></svg>
          Kembali ke atas
        </a>
      </div>
    </div>

  </div>
</footer>
