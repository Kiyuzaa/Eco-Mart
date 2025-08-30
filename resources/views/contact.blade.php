{{-- resources/views/contact.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Kontak — EcoMart</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="icon" href="{{ asset('images/logoEcomart.png') }}" type="image/png">
  <style>.contact-hero{background:linear-gradient(180deg,#ffffff 0%,#f7faf9 60%,#f4faf6 100%)}</style>
</head>
<body class="antialiased bg-white text-slate-900">

  <x-navbar />

  {{-- HERO --}}
  <section class="contact-hero border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
      <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight">Hubungi Kami</h1>
      <p class="mt-2 text-slate-600">Ada pertanyaan tentang produk atau pesanan? Tim kami siap membantu.</p>
    </div>
  </section>

  {{-- ALERTS --}}
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    @if (session('status'))
      <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3">
        <div class="font-semibold mb-1">Periksa kembali formulir:</div>
        <ul class="list-disc ms-5 text-sm">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>

  {{-- KONTEN --}}
  <section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-5 gap-8">

      {{-- Info kontak --}}
      <div class="lg:col-span-2">
        <div class="rounded-2xl border bg-white p-6">
          <h2 class="text-xl font-semibold">Informasi Kontak</h2>
          <ul class="mt-4 space-y-4 text-slate-700">
            <li class="flex items-center gap-3">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16a2 2 0 012 2v.4l-10 6.25L2 8.4V8a2 2 0 012-2Zm18 4.35V16a2 2 0 01-2 2H4a2 2 0 01-2-2v-5.65l9.24 5.78a2 2 0 002.04 0L22 10.35Z"/></svg>
              </span>
              support@ecomart.local
            </li>
            <li class="flex items-center gap-3">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24 11.36 11.36 0 003.56.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h2.49a1 1 0 011 1 11.36 11.36 0 00.57 3.56 1 1 0 01-.24 1.05l-2.2 2.2Z"/></svg>
              </span>
              0800-ECO-MART
            </li>
            <li class="flex items-center gap-3">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7Zm0 9.5A2.5 2.5 0 119.5 9 2.5 2.5 0 0112 11.5Z"/></svg>
              </span>
              Jakarta, Indonesia
            </li>
          </ul>

          <div class="mt-6">
            <p class="text-sm text-slate-600 mb-2">Lokasi kantor:</p>
            <div class="aspect-[16/9] rounded-xl overflow-hidden border">
              {{-- Map placeholder (ganti src bila mau embed asli) --}}
              <iframe
                class="w-full h-full"
                src="https://maps.google.com/maps?q=Jakarta%20Indonesia&t=&z=11&ie=UTF8&iwloc=&output=embed"
                loading="lazy"></iframe>
            </div>
          </div>
        </div>
      </div>

      {{-- Form --}}
      <div class="lg:col-span-3">
        <form
          method="POST"
          action="{{ Route::has('contact.send') ? route('contact.send') : url('/contact') }}"
          class="rounded-2xl border bg-white p-6 space-y-4">
          @csrf

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="text-sm text-slate-600">Nama</label>
              <input name="name" value="{{ old('name') }}" required
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
            </div>
            <div>
              <label class="text-sm text-slate-600">Email</label>
              <input type="email" name="email" value="{{ old('email') }}" required
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
            </div>
          </div>

          <div>
            <label class="text-sm text-slate-600">Subjek</label>
            <input name="subject" value="{{ old('subject') }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
          </div>

          <div>
            <label class="text-sm text-slate-600">Pesan</label>
            <textarea name="message" rows="6" required
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">{{ old('message') }}</textarea>
          </div>

          {{-- Honeypot anti-bot --}}
          <input type="text" name="hp" class="hidden" tabindex="-1" autocomplete="off">

          <div class="flex justify-end">
            <button class="inline-flex items-center rounded-xl bg-emerald-700 px-5 py-3 text-white hover:bg-emerald-800">
              Kirim Pesan
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <x-footer />
  @stack('scripts')
</body>
</html>
