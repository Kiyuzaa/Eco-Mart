{{-- resources/views/ai-chat.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>AI Chat — EcoBot | EcoMart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css','resources/js/app.js'])
  @else
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
  @endif

  <style>
    body{font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial}
    .bubble{white-space:pre-wrap}
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  @includeIf('components.navbar')

  {{-- Header --}}
  <section class="border-b bg-white">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-7 flex flex-wrap items-center gap-4 justify-between">
      <div>
        <p class="text-xs uppercase tracking-wider text-green-700 font-semibold">EcoBot</p>
        <h1 class="text-2xl md:text-3xl font-bold text-emerald-900">Chat AI Ramah Lingkungan</h1>
        <p class="text-gray-600">Tanyakan tips 3R, bahan produk, hingga rekomendasi belanja yang lebih hijau.</p>
      </div>
      <a href="{{ route('ai.bot') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">Lihat Panduan</a>
    </div>
  </section>

  {{-- Layout --}}
  <main class="max-w-6xl mx-auto px-4 md:px-6 py-6 grid md:grid-cols-[1fr,320px] gap-6">
    {{-- Chat panel --}}
    <section class="bg-white border rounded-2xl overflow-hidden flex flex-col">
      <div id="chatbox" class="flex-1 p-4 md:p-6 space-y-4 overflow-y-auto" style="min-height:420px">
        <div class="flex gap-3 items-start">
          <div class="shrink-0 w-9 h-9 rounded-full bg-emerald-600/10 grid place-items-center text-emerald-700 font-semibold">E</div>
          <div class="bubble bg-emerald-50 border border-emerald-100 rounded-2xl px-4 py-3 text-sm">
            Hai! Aku EcoBot 🌱. Ceritakan kebutuhanmu—aku bisa beri tips hemat energi, 3R, dan rekomendasi produk EcoMart.
          </div>
        </div>
      </div>

      <form id="chatForm" class="border-t bg-gray-50 p-3 md:p-4 flex items-end gap-3">
        <textarea
          id="message"
          class="flex-1 rounded-xl border px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600"
          rows="2" placeholder="Tulis pertanyaanmu… (misal: rekomendasi sabun cuci piring yang ramah lingkungan)"></textarea>
        <button
          id="sendBtn"
          type="submit"
          class="rounded-xl px-4 py-3 bg-emerald-700 text-white hover:bg-emerald-800 disabled:opacity-50">
          Kirim
        </button>
      </form>
    </section>

    {{-- Sidebar --}}
    <aside class="space-y-4">
      <div class="bg-white border rounded-2xl p-5">
        <div class="font-semibold text-emerald-900">Contoh Cepat</div>
        <div class="mt-3 grid grid-cols-1 gap-2">
          <button class="quick text-left text-sm px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
            Rekomendasi pembersih lantai non-toxic?
          </button>
          <button class="quick text-left text-sm px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
            Cara mengurangi plastik saat belanja bulanan?
          </button>
          <button class="quick text-left text-sm px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
            Produk household eco-friendly untuk kamar mandi?
          </button>
        </div>
      </div>

      <div class="bg-white border rounded-2xl p-5">
        <div class="font-semibold text-emerald-900">Ruang Lingkup</div>
        <ul class="text-sm text-gray-600 mt-2 list-disc pl-5 space-y-1">
          <li>Tips 3R: reduce, reuse, recycle</li>
          <li>Energi & air: hemat & efisien</li>
          <li>Material: non-toxic, BPA-free, daur ulang</li>
          <li>Rekomendasi produk EcoMart</li>
        </ul>
      </div>

      <div class="bg-gray-900 text-gray-100 rounded-2xl p-5">
        <div class="font-semibold">Butuh inspirasi cepat?</div>
        <p class="text-sm text-gray-300 mt-1">Coba “produk household yang eco-friendly”.</p>
        <a href="{{ route('ai.bot') }}" class="inline-block mt-3 px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-200">Lihat Panduan EcoBot</a>
      </div>
    </aside>
  </main>

  @includeIf('components.footer')

  <script>
    const chatbox = document.getElementById('chatbox');
    const form = document.getElementById('chatForm');
    const textarea = document.getElementById('message');
    const sendBtn = document.getElementById('sendBtn');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const quickBtns = document.querySelectorAll('.quick');

    const history = []; // riwayat lokal di halaman

    function addBubble(role, content) {
      const wrap = document.createElement('div');
      wrap.className = 'flex gap-3 items-start';
      if (role === 'user') wrap.classList.add('justify-end');

      const avatar =
        role === 'user'
          ? '<div class="shrink-0 w-9 h-9 rounded-full bg-gray-200 grid place-items-center text-gray-700 font-semibold">U</div>'
          : '<div class="shrink-0 w-9 h-9 rounded-full bg-emerald-600/10 grid place-items-center text-emerald-700 font-semibold">E</div>';

      const bubbleClass = role === 'user' ? 'bg-white border' : 'bg-emerald-50 border border-emerald-100';

      wrap.innerHTML = `
        ${role === 'user' ? '' : avatar}
        <div class="bubble rounded-2xl px-4 py-3 text-sm ${bubbleClass} max-w-[95%] md:max-w-[80%]">${escapeHtml(content)}</div>
        ${role === 'user' ? avatar : ''}
      `;

      chatbox.appendChild(wrap);
      chatbox.scrollTop = chatbox.scrollHeight;
    }

    function escapeHtml(unsafe) {
      return unsafe.replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');
    }

    quickBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        textarea.value = btn.textContent.trim();
        textarea.focus();
      });
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const text = textarea.value.trim();
      if (!text) return;

      addBubble('user', text);
      history.push({ role: 'user', content: text });
      textarea.value = '';
      textarea.style.height = 'auto';
      sendBtn.disabled = true;

      const typing = document.createElement('div');
      typing.className = 'flex gap-3 items-start';
      typing.innerHTML = `
        <div class="shrink-0 w-9 h-9 rounded-full bg-emerald-600/10 grid place-items-center text-emerald-700 font-semibold">E</div>
        <div class="rounded-2xl px-4 py-3 text-sm bg-emerald-50 border border-emerald-100">Mengetik…</div>
      `;
      chatbox.appendChild(typing);
      chatbox.scrollTop = chatbox.scrollHeight;

      try {
        const res = await fetch('{{ route('chat.send') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify({ message: text, context: history })
        });

        // kalau non-2xx → tampilkan cuplikan
        if (!res.ok) {
          const raw = await res.text();
          typing.remove();
          addBubble('assistant', `Gagal (${res.status}).\n${raw.substring(0, 400)}…`);
          return;
        }

        // parse JSON aman
        let json;
        try {
          json = await res.json();
        } catch (e) {
          const raw = await res.text();
          typing.remove();
          addBubble('assistant', `Respons tidak valid (bukan JSON).\n${raw.substring(0, 400)}…`);
          return;
        }

        typing.remove();

        if (!json.ok) {
          addBubble('assistant', json.reply || 'Maaf, terjadi kendala saat memproses. Coba lagi ya.');
          return;
        }

        addBubble('assistant', json.reply);
        history.push({ role: 'assistant', content: json.reply });

        // render list produk kalau ada
        if (Array.isArray(json.products) && json.products.length) {
          const ul = document.createElement('ul');
          ul.className = 'mt-2 space-y-2';
          json.products.forEach(p => {
            const li = document.createElement('li');
            li.className = 'text-sm';
            const price = Number(p.price || 0).toLocaleString('id-ID');
            li.innerHTML = `<a href="${p.url}" class="underline hover:no-underline">${escapeHtml(p.name)}</a> — Rp ${price}`;
            ul.appendChild(li);
          });
          const last = chatbox.lastElementChild;
          if (last) last.appendChild(ul);
          chatbox.scrollTop = chatbox.scrollHeight;
        }

      } catch (err) {
        typing.remove();
        addBubble('assistant', `Koneksi gagal: ${err?.message || 'Periksa server Laravel / jaringanmu.'}`);
      } finally {
        sendBtn.disabled = false;
      }
    });

    // auto-resize textarea
    textarea.addEventListener('input', () => {
      textarea.style.height = 'auto';
      textarea.style.height = Math.min(textarea.scrollHeight, 180) + 'px';
    });
  </script>
</body>
</html>
