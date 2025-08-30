{{-- resources/views/ai-bot.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>EcoBot — Panduan AI | EcoMart</title>

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css','resources/js/app.js'])
  @else
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
  @endif

  <style>
    html{scroll-behavior:smooth}
    body{font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial}
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  {{-- NAVBAR --}}
  @includeIf('components.navbar')

  {{-- Hero --}}
  <section class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-12 grid md:grid-cols-2 gap-10 items-center">
      <div>
        <p class="text-xs uppercase tracking-wider text-green-700 font-semibold">EcoBot</p>
        <h1 class="text-3xl md:text-4xl font-bold text-emerald-900 mt-2">
          Asisten AI Ramah Lingkungan untuk Membantumu Belanja Lebih Bijak
        </h1>
        <p class="mt-4 text-gray-600">
          Tanyakan apa saja seputar gaya hidup berkelanjutan, tips 3R, hingga rekomendasi produk EcoMart yang cocok dengan kebutuhanmu.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="{{ route('chat.index') }}" class="px-5 py-3 rounded-xl bg-emerald-700 text-white hover:bg-emerald-800">Mulai Chat dengan EcoBot</a>
          <a href="#fitur" class="px-5 py-3 rounded-xl bg-gray-100 text-gray-800 hover:bg-gray-200">Lihat Fitur</a>
        </div>
      </div>
      <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6">
        <div class="text-sm text-emerald-900 font-medium">Contoh pertanyaan</div>
        <ul class="mt-3 space-y-2 text-gray-700">
          <li>• “Rekomendasi sabun cuci piring yang ramah lingkungan dong.”</li>
          <li>• “Cara mengurangi plastik saat belanja bulanan?”</li>
          <li>• “Produk pembersih rumah yang non-toxic ada apa saja?”</li>
        </ul>
        <a href="{{ route('chat.index') }}" class="inline-block mt-5 px-4 py-2 rounded-lg bg-emerald-700 text-white hover:bg-emerald-800">Coba Sekarang</a>
      </div>
    </div>
  </section>

  {{-- Fitur --}}
  <section id="fitur" class="max-w-6xl mx-auto px-4 md:px-6 py-12">
    <h2 class="text-2xl md:text-3xl font-bold text-emerald-900">Fitur Utama</h2>
    <p class="text-gray-600 mt-2">Didesain untuk membantumu membuat keputusan yang lebih hijau dan hemat.</p>

    <div class="grid md:grid-cols-3 gap-6 mt-8">
      <div class="bg-white border rounded-2xl p-6">
        <div class="text-emerald-700 font-semibold">Rekomendasi Produk</div>
        <p class="text-gray-600 mt-2">Saran produk EcoMart yang lebih ramah lingkungan sesuai kategori kebutuhanmu.</p>
      </div>
      <div class="bg-white border rounded-2xl p-6">
        <div class="text-emerald-700 font-semibold">Tips 3R Praktis</div>
        <p class="text-gray-600 mt-2">Reduce, Reuse, Recycle dengan contoh yang mudah diterapkan di rumah.</p>
      </div>
      <div class="bg-white border rounded-2xl p-6">
        <div class="text-emerald-700 font-semibold">Edukasi Bahan & Sertifikasi</div>
        <p class="text-gray-600 mt-2">Pahami label ramah lingkungan seperti BPA-free, non-toxic, dan lainnya.</p>
      </div>
    </div>

    <div class="mt-10 rounded-2xl border bg-gray-50 p-6">
      <div class="font-semibold text-emerald-900">Privasi & Akurasi</div>
      <p class="text-gray-600 mt-2">
        Chat disimpan sementara untuk meningkatkan kualitas rekomendasi. EcoBot berfokus pada saran berkelanjutan; verifikasi informasi spesifik bila diperlukan.
      </p>
      <a href="{{ route('chat.index') }}" class="inline-block mt-4 px-5 py-3 rounded-xl bg-emerald-700 text-white hover:bg-emerald-800">Mulai Chat</a>
    </div>
  </section>

  {{-- CTA --}}
  <section class="bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-12 text-center">
      <h3 class="text-2xl font-bold">Mulai Hidup Berkelanjutan Hari Ini</h3>
      <p class="text-gray-300 mt-2">Ayo mulai chat dengan EcoBot dan temukan pilihan produk yang lebih ramah lingkungan.</p>
      <a href="{{ route('chat.index') }}" class="mt-5 inline-block px-5 py-3 rounded-xl bg-white text-gray-900 hover:bg-gray-200">
        Chat dengan EcoBot Sekarang
      </a>
    </div>
  </section>

  @includeIf('components.footer')
</body>
</html>
