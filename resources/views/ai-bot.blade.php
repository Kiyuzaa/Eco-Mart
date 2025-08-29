{{-- resources/views/ai-bot.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AI Chat Bot — EcoMart</title>

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

  {{-- NAVBAR dari komponen --}}
  @include('components.navbar')

  {{-- HERO --}}
  <section class="py-10 md:py-14">
    <div class="max-w-6xl mx-auto px-4 md:px-6 grid md:grid-cols-2 gap-8 items-center">
      <div>
        <h1 class="text-3xl md:text-4xl font-bold leading-tight">
          Halo! Saya <span class="text-green-700">EcoBot</span>, Asisten AI EcoMart
        </h1>
        <p class="mt-3 text-gray-600">
          Saya membantu Anda menemukan produk ramah lingkungan dan memberi tips hidup berkelanjutan.
          Mari bersama menjaga planet ini 🌎
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
          <a href="{{ route('chat.index') }}"
             class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-green-700 text-white hover:bg-green-800">
            Mulai Chat
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>

          <a href="#learn-more"
             class="inline-flex items-center px-5 py-3 rounded-xl border border-gray-300 bg-white hover:bg-gray-100">
            Pelajari Lebih Lanjut
          </a>
        </div>
      </div>

      {{-- Ilustrasi / Placeholder kanan --}}
      <div class="bg-white border rounded-2xl p-6 h-64 md:h-72 grid place-content-center text-center">
        <div class="text-5xl mb-2">🤖</div>
        <div class="text-sm text-gray-500">EcoBot Interface</div>
      </div>
    </div>
  </section>

  {{-- PREVIEW CHAT MINI --}}
  <section class="py-6">
    <div class="max-w-6xl mx-auto px-4 md:px-6">
      <div class="bg-white border rounded-2xl p-4 md:p-6">
        <div class="text-sm text-gray-500 mb-3">EcoBot</div>
        <div class="space-y-3 text-sm">
          <div class="text-left">
            <div class="inline-block bg-gray-100 px-3 py-2 rounded-2xl">
              Selamat datang di EcoBot! Saya bantu…
            </div>
          </div>
          <div class="text-right">
            <div class="inline-block bg-green-600 text-white px-3 py-2 rounded-2xl">
              Rekomendasikan produk kategori food
            </div>
          </div>
          <div class="text-left">
            <div class="inline-block bg-gray-100 px-3 py-2 rounded-2xl">
              Pilih produk lokal/organik & kemasan minim plastik. Klik “Mulai Chat” untuk rekomendasi dari database EcoMart.
            </div>
          </div>
        </div>
        <div class="mt-4 border-t pt-3 flex gap-2">
          <input type="text" disabled placeholder="Ketik pesan Anda..." class="flex-1 rounded-lg border px-3 py-2 bg-gray-50">
          <button disabled class="rounded-lg px-4 py-2 bg-green-600 text-white opacity-60">Kirim</button>
        </div>
      </div>
    </div>
  </section>

  {{-- YANG BISA SAYA BANTU --}}
  <section id="learn-more" class="py-12 bg-white border-t">
    <div class="max-w-6xl mx-auto px-4 md:px-6">
      <h2 class="text-2xl font-bold mb-6 text-center md:text-left">Yang Bisa Saya Bantu</h2>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="bg-gray-50 border rounded-xl p-5">
          <div class="font-semibold text-green-700 mb-1">Rekomendasi Produk</div>
          <p class="text-gray-600 text-sm">Analisis kategori & kebutuhan Anda untuk pilihan yang berkelanjutan.</p>
        </div>
        <div class="bg-gray-50 border rounded-xl p-5">
          <div class="font-semibold text-green-700 mb-1">Tips Berkelanjutan</div>
          <p class="text-gray-600 text-sm">Reduce–reuse–recycle, hemat energi, dan belanja cerdas.</p>
        </div>
        <div class="bg-gray-50 border rounded-xl p-5">
          <div class="font-semibold text-green-700 mb-1">Edukasi Lingkungan</div>
          <p class="text-gray-600 text-sm">Jejak karbon, bahan ramah lingkungan, dan sertifikasi.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- FAQ / POPULER --}}
  <section class="py-12">
    <div class="max-w-6xl mx-auto px-4 md:px-6">
      <h2 class="text-2xl font-bold mb-6 text-center md:text-left">Pertanyaan Populer</h2>
      <div class="grid md:grid-cols-2 gap-4">
        <a href="{{ route('chat.index') }}" class="block border rounded-xl p-4 bg-white hover:shadow">
          <div class="font-medium mb-1">Saya ingin belanja sayur organik</div>
          <div class="text-sm text-gray-600">Lihat rekomendasi & tips penyimpanan.</div>
        </a>
        <a href="{{ route('chat.index') }}" class="block border rounded-xl p-4 bg-white hover:shadow">
          <div class="font-medium mb-1">Produk pembersih ramah lingkungan</div>
          <div class="text-sm text-gray-600">Tanpa fosfat, biodegradable, minim plastik.</div>
        </a>
        <a href="{{ route('chat.index') }}" class="block border rounded-xl p-4 bg-white hover:shadow">
          <div class="font-medium mb-1">Fashion eco-friendly</div>
          <div class="text-sm text-gray-600">Bahan organik/daur ulang & etis.</div>
        </a>
        <a href="{{ route('chat.index') }}" class="block border rounded-xl p-4 bg-white hover:shadow">
          <div class="font-medium mb-1">Bagaimana kurangi sampah plastik?</div>
          <div class="text-sm text-gray-600">Refill, bawa tumbler, tas kain, dll.</div>
        </a>
      </div>

      <div class="mt-8 text-center md:text-left">
        <a href="{{ route('chat.index') }}"
           class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-green-700 text-white hover:bg-green-800">
          Mulai Chat Sekarang
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      </div>
    </div>
  </section>

  {{-- CTA DARK --}}
  <section class="bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-12 text-center">
      <h3 class="text-2xl font-bold">Mulai Hidup Berkelanjutan Hari Ini</h3>
      <p class="text-gray-300 mt-2">Bergabung dengan komunitas EcoMart dan mulai perjalanan menuju gaya hidup yang lebih ramah lingkungan.</p>
      <a href="{{ route('chat.index') }}" class="mt-5 inline-block px-5 py-3 rounded-xl bg-white text-gray-900 hover:bg-gray-200">
        Chat dengan EcoBot Sekarang
      </a>
    </div>
  </section>


</body>
</html>
