<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Product;

class AiChatController extends Controller
{
    /**
     * Tampilkan halaman chat (opsional: reset via ?reset=1)
     */
    public function index(Request $request)
    {
        if ($request->boolean('reset')) {
            $request->session()->forget('chat_messages');
        }

        $messages = $request->session()->get('chat_messages', []);
        return view('ai-chat', compact('messages'));
    }

    /**
     * Endpoint AJAX untuk mengirim pesan dan menerima balasan.
     * Selalu mengembalikan JSON.
     */
    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:5000']);
        $msg = trim($request->input('message'));

        // simpan riwayat ringkas di session (opsional untuk tampilan ulang server-side)
        $messages = $request->session()->get('chat_messages', []);
        $messages[] = ['role' => 'user', 'text' => $msg];

        // 1) Coba pahami intent "minta rekomendasi produk"
        $askProduct = (bool) preg_match('/\b(produk|barang|rekomendasi\w*|saran\w*)\b/i', $msg);

        // 2) Peta kategori: slug => daftar kata kunci (ID & EN)
        $categoryMap = [
            'food'             => ['food','makan','makanan','beverage','minum','minuman','snack','cemilan'],
            'health-beauty'    => ['health','beauty','kesehatan','kecantikan','skincare','perawatan','sabun','sampo','shampoo','lotion','deodoran','deodorant','sabun muka','kosmetik'],
            'household'        => ['household','rumah','rumah tangga','pembersih','detergen','deterjen','alat bersih','kamar mandi','dapur'],
            'electronics'      => ['elektronik','electronics','gadget','lampu','bohlam','charger','powerbank'],
            'fashion'          => ['fashion','pakaian','baju','kaos','celana','piyama','kain','katun'],
            'sports'           => ['olahraga','sports','yoga','mat','sepeda','fitness','fitnes','outdoor'],
            'toys'             => ['toys','mainan','toy','boneka','puzzle','lego','balok'],
            'books'            => ['buku','books','novel','komik','literatur'],
            'office-supplies'  => ['alat tulis','ATK','stationery','kertas','bolpoin','pensil','office'],
            'baby'             => ['bayi','baby','diaper','popok','susu formula','bedak bayi'],
            'pets'             => ['hewan','peliharaan','pets','makanan kucing','makanan anjing','cat food','dog food'],
        ];

        // 3) Jika user minta produk → coba tebak kategori
        if ($askProduct) {
            // a) ekstra: deteksi pola "kategori: xxx"
            $explicitCategory = $this->extractExplicitCategory($msg);
            if ($explicitCategory) {
                $slug = $this->matchCategorySlug($explicitCategory, $categoryMap);
                if ($slug) {
                    return $this->respondWithProductsForSlug($request, $messages, $slug, $explicitCategory);
                }
            }

            // b) cocokkan berdasarkan keyword bebas
            $lower = Str::lower($msg);
            foreach ($categoryMap as $slug => $keywords) {
                foreach ($keywords as $kw) {
                    if (Str::contains($lower, Str::lower($kw))) {
                        return $this->respondWithProductsForSlug($request, $messages, $slug, $kw);
                    }
                }
            }
        }

        // 4) Jika tidak terdeteksi kategori → fallback ke Gemini
        return $this->replyWithGemini($request, $messages, $msg);
    }

    /**
     * Mencoba mengambil produk berdasarkan slug kategori.
     * Mengembalikan JSON: { ok, reply, products? }
     */
    protected function respondWithProductsForSlug(Request $request, array $messages, string $slug, string $matchedKeyword)
    {
        // Query produk yang kategori-nya match slug (atau nama mengandung keyword sebagai fallback)
        $products = Product::with('category')
            ->whereHas('category', function($q) use ($slug, $matchedKeyword) {
                $q->where('slug', $slug)
                  ->orWhere('name', 'like', '%'.$matchedKeyword.'%');
            })
            ->take(6)
            ->get(['id','name','slug','price','image','category_id']);

        if ($products->isEmpty()) {
            $reply = "Maaf, produk kategori {$slug} belum tersedia di EcoMart.";
            $messages[] = ['role' => 'model', 'text' => $reply];
            $request->session()->put('chat_messages', $messages);

            return response()->json([
                'ok'       => true,
                'reply'    => $reply,
                'products' => [],
            ]);
        }

        $listLines = $products->map(function ($p) {
            $rp = 'Rp '.number_format((float)$p->price, 0, ',', '.');
            return "- {$p->name} ({$rp})";
        })->implode("\n");

        $reply = "Berikut rekomendasi produk kategori *{$slug}* di EcoMart:\n".$listLines;

        $payloadProducts = $products->map(function ($p) {
            return [
                'name'  => $p->name,
                'slug'  => $p->slug,
                'price' => (float) $p->price,
                'image' => $p->image,
                'url'   => route('products.show', ['product' => $p->slug]),
            ];
        })->values()->all();

        $messages[] = ['role' => 'model', 'text' => $reply, 'products' => $payloadProducts];
        $request->session()->put('chat_messages', $messages);

        return response()->json([
            'ok'       => true,
            'reply'    => $reply,
            'products' => $payloadProducts,
        ]);
    }

    /**
     * Fallback ke Gemini untuk jawaban umum (tips/kriteria, dsb)
     */
    protected function replyWithGemini(Request $request, array $messages, string $msg)
    {
        $apiKey = config('services.gemini.key');
        $model  = env('GEMINI_MODEL', 'gemini-2.0-flash');

        if (!$apiKey) {
            $reply = '(Konfigurasi error: GEMINI_API_KEY belum di .env)';
            $messages[] = ['role' => 'model', 'text' => $reply];
            $request->session()->put('chat_messages', $messages);

            return response()->json(['ok' => false, 'reply' => $reply], 500);
        }

        $systemInstruction =
            "You are EcoBot for EcoMart. Jawab singkat, jelas, ramah (Indonesia). ".
            "Fokus lingkungan (reduce/reuse/recycle, jejak karbon, bahan ramah lingkungan). ".
            "Jika diminta rekomendasi belanja, beri KRITERIA hijau tanpa mengarang stok/harga.";

        $contents = [
            ['role'=>'user','parts'=>[['text'=>$systemInstruction]]],
            ['role'=>'user','parts'=>[['text'=>$msg]]],
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $resp = Http::asJson()->timeout(25)->post($url, ['contents' => $contents]);

            if ($resp->failed()) {
                $hint  = data_get($resp->json(), 'error.message') ?: 'Permintaan ke AI gagal';
                $reply = "(Maaf, AI gagal merespons: {$hint})";

                $messages[] = ['role' => 'model', 'text' => $reply];
                $request->session()->put('chat_messages', $messages);

                return response()->json(['ok' => false, 'reply' => $reply], 502);
            }

            $json = $resp->json();
            $text = data_get($json, 'candidates.0.content.parts.0.text');

            if (!$text) {
                $parts = data_get($json, 'candidates.0.content.parts', []);
                if (is_array($parts)) {
                    $text = collect($parts)->pluck('text')->filter()->implode("\n");
                }
            }
            if (!$text) {
                $reason = data_get($json, 'promptFeedback.blockReason');
                $text   = $reason ? "(Jawaban diblokir oleh safety: {$reason})" : '(AI tidak mengirim teks)';
            }

            $messages[] = ['role' => 'model', 'text' => $text];
            $request->session()->put('chat_messages', $messages);

            return response()->json(['ok' => true, 'reply' => $text]);

        } catch (\Throwable $e) {
            $reply = '(Terjadi kesalahan jaringan/server. Coba lagi)';
            $messages[] = ['role' => 'model', 'text' => $reply];
            $request->session()->put('chat_messages', $messages);

            return response()->json(['ok' => false, 'reply' => $reply], 500);
        }
    }

    /**
     * Ekstrak teks setelah "kategori:" jika ada.
     * contoh: "rekomendasikan produk kategori: health & beauty"
     */
    protected function extractExplicitCategory(string $text): ?string
    {
        if (preg_match('/kategori\s*:\s*([A-Za-z0-9 &\-\_]+)/i', $text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /**
     * Cocokkan kata bebas dengan slug di peta kategori.
     */
    protected function matchCategorySlug(string $freeText, array $categoryMap): ?string
    {
        $freeText = Str::lower($freeText);

        // normalisasi beberapa variasi umum
        $freeText = str_replace([' & ', ' and '], ['-', '-'], $freeText);
        $freeText = preg_replace('/\s+/', '-', $freeText); // spasi → dash

        // 1) jika freeText persis slug yang ada
        if (array_key_exists($freeText, $categoryMap)) {
            return $freeText;
        }

        // 2) fuzzy: cek apakah freeText mengandung salah satu keyword
        foreach ($categoryMap as $slug => $keywords) {
            if ($slug === $freeText) return $slug;
            foreach ($keywords as $kw) {
                if (Str::contains($freeText, Str::lower($kw))) {
                    return $slug;
                }
            }
        }

        // 3) normalisasi khusus: health & beauty → health-beauty
        if (Str::contains($freeText, 'health') && Str::contains($freeText, 'beauty')) {
            return 'health-beauty';
        }

        return null;
    }
}
