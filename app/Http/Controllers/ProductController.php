<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Halaman daftar produk (filter Rupiah, bahasa Indonesia).
     */
    public function index(Request $request)
    {
        $selectedCategories = (array) $request->get('category', []);
        $priceRange         = (string) $request->get('price', '');
        $selectedMaterials  = (array) $request->get('material', []);
        $sort               = (string) $request->get('sort', 'featured');
        $q                  = trim((string) $request->get('q', ''));

        $query = Product::query()->with('category');

        // Cari (opsional)
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('slug', 'like', "%{$q}%");
                if (Schema::hasColumn('products', 'description')) {
                    $qq->orWhere('description', 'like', "%{$q}%");
                }
            });
        }

        // Filter kategori (slug)
        if (!empty($selectedCategories)) {
            $query->whereHas('category', function ($q2) use ($selectedCategories) {
                $q2->whereIn('slug', $selectedCategories);
            });
        }

        // Filter harga (IDR)
        switch ($priceRange) {
            case 'under-100k':   $query->where('price', '<', 100_000); break;
            case '100k-250k':    $query->whereBetween('price', [100_000, 250_000]); break;
            case '250k-500k':    $query->whereBetween('price', [250_000, 500_000]); break;
            case 'over-500k':    $query->where('price', '>', 500_000); break;
        }

        // Material (opsional)
        if (!empty($selectedMaterials) && Schema::hasColumn('products', 'material')) {
            $query->whereIn('material', $selectedMaterials);
        }

        // Urut
        if ($sort === 'price-asc')       $query->orderBy('price','asc');
        elseif ($sort === 'price-desc')  $query->orderBy('price','desc');
        elseif ($sort === 'newest')      $query->orderBy('created_at','desc');
        elseif ($sort === 'name-asc')    $query->orderBy('name','asc');
        elseif ($sort === 'name-desc')   $query->orderBy('name','desc');
        elseif ($sort === 'eco-desc' && Schema::hasColumn('products','eco_score')) $query->orderBy('eco_score','desc');
        else                              $query->orderBy('id','desc'); // featured

        $products   = $query->paginate(9)->withQueryString();
        $categories = Category::orderBy('name')->get(['id','name','slug']);

        $materials = [];
        if (Schema::hasColumn('products', 'material')) {
            $materials = Product::whereNotNull('material')
                ->distinct()->pluck('material')->filter()->values()->all();
        }

        return view('product', [
            'products'           => $products,
            'categories'         => $categories,
            'materials'          => $materials,
            'selectedCategories' => $selectedCategories,
            'selectedMaterials'  => $selectedMaterials,
            'priceRange'         => $priceRange,
            'sort'               => $sort,
        ]);
    }

    /**
     * Detail produk — normalisasi URL gambar + fallback aman.
     */
    public function show(Product $product)
    {
        // KUMPULKAN URL GAMBAR SECARA AMAN
        $images = collect();

        // 1) kolom 'image' (biasanya path storage)
        if ($img = data_get($product, 'image')) {
            if (Str::startsWith($img, ['http://','https://'])) {
                $images->push($img); // sudah URL penuh
            } else {
                $img = ltrim($img, '/'); // hindari double slash
                // jika sudah mengandung 'storage/', jangan ditambah lagi
                $images->push(Str::startsWith($img, 'storage/')
                    ? asset($img)
                    : asset('storage/'.$img)
                );
            }
        }

        // 2) kolom 'image_url' (link eksternal)
        if ($url = data_get($product, 'image_url')) {
            $images->push($url);
        }

        // 3) fallback placeholder
        if ($images->filter()->isEmpty()) {
            $images->push('https://images.unsplash.com/photo-1519744792095-2f2205e87b6f?q=80&w=1200&auto=format&fit=crop');
        }

        $images = $images->filter()->unique()->values();

        // Produk terkait
        $related = Product::query()
            ->when($product->category_id, fn($q) => $q->where('category_id', $product->category_id))
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        if ($related->isEmpty()) {
            $related = Product::where('id', '!=', $product->id)->latest()->take(8)->get();
        }

        // Placeholder
        $reviews    = collect();
        $sizes      = collect(['S','M','L','XL']);
        $colors     = collect(['#111827','#4B5563','#9CA3AF']);
        $compare_at = data_get($product,'compare_at_price') ?? data_get($product,'old_price');

        // Status wishlist opsional
        $inWishlist = auth()->check() && method_exists(auth()->user(), 'hasInWishlist')
            ? (bool) auth()->user()->hasInWishlist($product->id)
            : false;

        return view('product-detail', compact(
            'product','images','related','reviews','sizes','colors','compare_at','inWishlist'
        ));
    }

    /**
     * Simpan produk (admin).
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required'        => 'Nama produk wajib diisi.',
            'slug.required'        => 'Slug produk wajib diisi.',
            'slug.unique'          => 'Slug sudah dipakai.',
            'price.required'       => 'Harga wajib diisi.',
            'price.numeric'        => 'Harga harus angka.',
            'price.min'            => 'Harga minimal 0.',
            'stock.required'       => 'Stok wajib diisi.',
            'stock.integer'        => 'Stok harus bilangan bulat.',
            'stock.min'            => 'Stok minimal 0.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak ditemukan.',
            'image.image'          => 'File gambar tidak valid.',
            'image.mimes'          => 'Gambar harus jpeg, png, atau jpg.',
            'image.max'            => 'Ukuran gambar maksimal 10MB.',
        ];

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:products,slug',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ], $messages);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public'); // simpan ke storage/app/public/products
        }

        Product::create([
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'price'       => $data['price'],
            'stock'       => $data['stock'],
            'category_id' => $data['category_id'],
            'image'       => $path, // simpan TANPA prefix 'storage/'
        ]);

        return redirect()->route('admin.products.index')->with('success','Produk berhasil ditambahkan!');
    }
}
