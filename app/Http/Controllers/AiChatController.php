<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Product;

class AiChatController extends Controller
{
    public function index(Request $request)
    {
        // optional: reset chat via query ?reset=1
        if ($request->boolean('reset')) {
            $request->session()->forget('chat_messages');
        }

        $messages = $request->session()->get('chat_messages', []);
        return view('ai-chat', compact('messages'));
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:5000']);
        $msg = trim($request->input('message'));

        // ambil riwayat dari session
        $messages   = $request->session()->get('chat_messages', []);
        $messages[] = ['role' => 'user', 'text' => $msg];

        // === Intent: rekomendasi produk kategori food ===
        // longgar: "rekomendasi", "rekomendasikan", "merekomendasikan", dll
        $askProduct = preg_match('/\b(produk|barang|rekomendasi\w*)\b/i', $msg);
        $hasFood    = preg_match('/\b(food|makanan)\b/i', $msg)
                   || preg_match('/kategori\s*:\s*food/i', $msg)
                   || Str::contains(Str::lower($msg), 'kategori food');

        if ($askProduct && $hasFood) {
            $products = Product::with('category')
                ->whereHas('category', function($q){
                    $q->where('slug', 'food')
                      ->orWhere('name', 'like', '%Food%')
                      ->orWhere('name', 'like', '%Makan%');
                })
                // ->orderByDesc('eco_score') // HAPUS jika kolom tidak ada
                ->take(6)
                ->get(['id','name','slug','price','image','category_id']);

            if ($products->isEmpty()) {
                $messages[] = ['role'=>'model', 'text'=>'Maaf, produk kategori food belum tersedia di EcoMart.'];
            } else {
                $list = $products->map(fn($p)=> "- {$p->name} (Rp ".number_format((float)$p->price,0,',','.').")")->implode("\n");
                $messages[] = [
                    'role'     => 'model',
                    'text'     => "Berikut rekomendasi produk kategori *food* di EcoMart:\n".$list,
                    'products' => $products->map(fn($p)=>[
                        'name'  => $p->name,
                        'slug'  => $p->slug,
                        'price' => (float) $p->price,
                        'image' => $p->image,
                        'url'   => route('products.show', ['product'=>$p->slug]),
                    ])->toArray(),
                ];
            }

            $request->session()->put('chat_messages', $messages);
            return back();
        }

        // === Selain itu, lempar ke Gemini ===
        $apiKey = config('services.gemini.key');
        $model  = env('GEMINI_MODEL', 'gemini-2.0-flash');

        if (!$apiKey) {
            $messages[] = ['role'=>'model', 'text'=>'(Konfigurasi error: GEMINI_API_KEY belum di .env)'];
            $request->session()->put('chat_messages', $messages);
            return back();
        }

        $systemInstruction =
            "You are EcoBot for EcoMart. Jawab singkat, jelas, ramah (Indonesia). ".
            "Fokus lingkungan (reduce/reuse/recycle, jejak karbon, bahan ramah lingkungan). ".
            "Jika diminta rekomendasi belanja, beri KRITERIA hijau tanpa mengarang stok/harga.";

        $contents = [
            ['role'=>'user','parts'=>[['text'=>$systemInstruction]]],
            ['role'=>'user','parts'=>[['text'=>$msg]]],
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $resp = Http::withHeaders([
                'x-goog-api-key'=>$apiKey,
                'Content-Type'=>'application/json',
            ])->timeout(25)->post($url, ['contents'=>$contents]);

            if ($resp->failed()) {
                $hint = data_get($resp->json(),'error.message') ?: 'Permintaan ke AI gagal';
                $messages[] = ['role'=>'model','text'=>"(Maaf, AI gagal merespons: {$hint})"];
            } else {
                $json = $resp->json();
                $text = data_get($json,'candidates.0.content.parts.0.text');

                if (!$text) {
                    $parts = data_get($json,'candidates.0.content.parts',[]);
                    if (is_array($parts)) {
                        $text = collect($parts)->pluck('text')->filter()->implode("\n");
                    }
                }
                if (!$text) {
                    $reason = data_get($json,'promptFeedback.blockReason');
                    $text = $reason ? "(Jawaban diblokir oleh safety: {$reason})" : '(AI tidak mengirim teks)';
                }

                $messages[] = ['role'=>'model','text'=>$text];
            }
        } catch (\Throwable $e) {
            $messages[] = ['role'=>'model','text'=>'(Terjadi kesalahan jaringan/server. Coba lagi)'];
        }

        $request->session()->put('chat_messages', $messages);
        return back();
    }
}
