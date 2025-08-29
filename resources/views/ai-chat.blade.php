{{-- resources/views/ai-chat.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>AI Chat — EcoBot | EcoMart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

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
    .bubble{white-space:pre-wrap}
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  {{-- NAVBAR (pakai komponenmu) --}}
  @include('components.navbar')

  {{-- HEADER STRIP (mirip ai-bot) --}}
  <section class="border-b bg-white">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-7 flex flex-wrap items-center gap-4 justify-between">
      <div>
        <p class="text-xs uppercase tracking-wider text-green-700 font-semibold">EcoBot</p>
        <h1 class="text-2xl md:text-3xl font-bold mt-1">Mulai Percakapan dengan Asisten AI EcoMart</h1>
        <p class="text-gray-600 mt-2">Tanya apa saja tentang produk ramah lingkungan & hidup berkelanjutan.</p>
      </div>
      <div class="shrink-0">
        <a href="{{ route('ai.bot') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-100">
          ← Kembali ke Halaman Bot
        </a>
      </div>
    </div>
  </section>

  {{-- KONTEN UTAMA --}}
  <main class="py-8">
    <div class="max-w-6xl mx-auto px-4 md:px-6 grid lg:grid-cols-3 gap-6">

      {{-- Kolom KIRI: Chat box --}}
      <section class="lg:col-span-2">
        <div class="bg-white border rounded-2xl shadow-sm">

          {{-- Header kecil chat --}}
          <div class="px-4 py-3 border-b flex items-center gap-3">
            <div class="h-9 w-9 grid place-content-center rounded-full bg-green-100 text-green-700 font-bold">E</div>
            <div>
              <div class="font-semibold">EcoBot</div>
              <div class="text-xs text-gray-500">Online</div>
            </div>
          </div>

          {{-- AREA CHAT --}}
          <div class="p-4">
            <div class="h-[58vh] md:h-[62vh] overflow-y-auto space-y-3 text-sm" id="chat-area">
              @forelse($messages as $m)
                @php
                  $isUser = ($m['role'] ?? '') === 'user';
                  $text   = $m['text'] ?? '';
                  // Bersihkan markdown umum agar visual konsisten
                  $text   = str_replace(['**','`'],'', $text);
                  $text   = preg_replace('/^\* /m','• ', $text);
                @endphp

                <div class="{{ $isUser ? 'text-right' : 'text-left' }}">
                  <div class="inline-block max-w-[85%] px-3 py-2 rounded-2xl bubble {{ $isUser ? 'bg-green-600 text-white' : 'bg-gray-100' }}">
                    {{ $text }}
                  </div>
                </div>

                {{-- Kartu produk jika ada --}}
                @if(!empty($m['products']) && is_array($m['products']))
                  <div class="text-left">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      @foreach($m['products'] as $p)
                        <a href="{{ $p['url'] ?? '#' }}" class="border rounded-lg overflow-hidden hover:shadow bg-white">
                          <div class="aspect-[4/3] bg-gray-100">
                            @if(!empty($p['image']))
                              <img src="{{ $p['image'] }}" alt="{{ $p['name'] }}" class="w-full h-full object-cover">
                            @endif
                          </div>
                          <div class="p-3">
                            <div class="font-medium line-clamp-2">{{ $p['name'] }}</div>
                            <div class="text-green-700 font-semibold mt-1">
                              Rp {{ number_format((float)($p['price'] ?? 0), 0, ',', '.') }}
                            </div>
                          </div>
                        </a>
                      @endforeach
                    </div>
                  </div>
                @endif
              @empty
                <div class="text-gray-500">Belum ada percakapan. Mulai dengan menanyakan sesuatu kepada EcoBot 🙂</div>
              @endforelse
            </div>

            {{-- FORM INPUT --}}
            <form method="POST" action="{{ route('chat.send') }}" class="mt-4 flex gap-2">
              @csrf
              <input name="message"
                     value="{{ old('message') }}"
                     class="flex-1 border rounded-xl px-3 py-2"
                     placeholder="Tulis pesan untuk EcoBot..."
                     autofocus
                     required>
              <button class="rounded-xl px-4 py-2 bg-green-700 text-white hover:bg-green-800">
                Kirim
              </button>
            </form>

            {{-- Reset chat (opsional) --}}
            <form method="GET" action="{{ route('chat.index') }}" class="mt-2"
                  onsubmit="return confirm('Hapus riwayat chat?')">
              <input type="hidden" name="reset" value="1">
              <button class="text-xs text-red-600 hover:underline">Reset chat</button>
            </form>
          </div>
        </div>
      </section>

      {{-- Kolom KANAN: Bantuan ringkas (menyamai gaya ai-bot) --}}
      <aside class="space-y-4">
        <div class="bg-white border rounded-2xl p-5">
          <div class="font-semibold mb-2">Tips Cepat</div>
          <ul class="text-sm text-gray-600 list-disc pl-5 space-y-1">
            <li>Ketik: <span class="font-medium">rekomendasikan produk kategori food</span></li>
            <li>Atau: <span class="font-medium">produk pembersih ramah lingkungan</span></li>
            <li>Contoh lain: <span class="font-medium">cara kurangi plastik di rumah</span></li>
          </ul>
        </div>

        <div class="bg-white border rounded-2xl p-5">
          <div class="font-semibold mb-2">Yang Bisa Saya Bantu</div>
          <div class="grid gap-3">
            <div class="bg-gray-50 border rounded-xl p-4">
              <div class="text-green-700 font-semibold">Rekomendasi Produk</div>
              <div class="text-sm text-gray-600">Dari database EcoMart, bukan karangan.</div>
            </div>
            <div class="bg-gray-50 border rounded-xl p-4">
              <div class="text-green-700 font-semibold">Tips Berkelanjutan</div>
              <div class="text-sm text-gray-600">Reduce–reuse–recycle & hemat energi.</div>
            </div>
            <div class="bg-gray-50 border rounded-xl p-4">
              <div class="text-green-700 font-semibold">Edukasi Lingkungan</div>
              <div class="text-sm text-gray-600">Jejak karbon, bahan dan sertifikasi.</div>
            </div>
          </div>
        </div>

        <div class="bg-gray-900 text-gray-100 rounded-2xl p-5">
          <div class="font-semibold">Butuh inspirasi cepat?</div>
          <p class="text-sm text-gray-300 mt-1">Coba minta “produk household yang eco-friendly”.</p>
          <a href="{{ route('ai.bot') }}" class="inline-block mt-3 px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-200">
            Lihat Panduan EcoBot
          </a>
        </div>
      </aside>
    </div>
  </main>

  {{-- FOOTER (komponenmu) --}}
  @include('components.footer')

</body>
</html>
