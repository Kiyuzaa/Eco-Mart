<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input filter
        $selectedCategories = (array) $request->get('category', []);
        $priceRange         = $request->get('price', '');
        $selectedMaterials  = (array) $request->get('material', []);
        $sort               = $request->get('sort', 'featured');

        $query = Product::query()->with('category');

        // Filter kategori (pakai slug kategori)
        if (!empty($selectedCategories)) {
            $query->whereHas('category', function ($q) use ($selectedCategories) {
                $q->whereIn('slug', $selectedCategories);
            });
        }

        // Filter harga (contoh USD; sesuaikan jika pakai IDR)
        if ($priceRange === 'under-25') {
            $query->where('price', '<', 25);
        } elseif ($priceRange === '25-50') {
            $query->whereBetween('price', [25, 50]);
        } elseif ($priceRange === '50-100') {
            $query->whereBetween('price', [50, 100]);
        } elseif ($priceRange === 'over-100') {
            $query->where('price', '>', 100);
        }

        // Filter material HANYA jika kolomnya ada
        if (!empty($selectedMaterials) && Schema::hasColumn('products', 'material')) {
            $query->whereIn('material', $selectedMaterials);
        }

        // Sorting
        if ($sort === 'price-asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price-desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else { // featured (default)
            $query->orderBy('id', 'desc');
        }

        // Pagination (9/grid)
        $products = $query->paginate(9)->withQueryString();

        // Data sidebar
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        // Materials unik jika kolom 'material' ada, kalau tidak -> []
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

    public function show(Product $product)
    {
        // Kumpulan gambar: storage('image') -> image_url -> placeholder
        $images = collect([
            data_get($product, 'image') ? asset('storage/' . $product->image) : null,
            data_get($product, 'image_url') ?: null,
        ])
        ->filter()
        ->whenEmpty(fn($c) => $c->push('https://images.unsplash.com/photo-1519744792095-2f2205e87b6f?q=80&w=1200&auto=format&fit=crop'))
        ->unique()
        ->values();

        // Produk terkait: kategori sama (jika ada), bukan dirinya, max 8
        $related = Product::query()
            ->when($product->category_id, fn($q) => $q->where('category_id', $product->category_id))
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        if ($related->isEmpty()) {
            $related = Product::where('id', '!=', $product->id)
                ->latest()->take(8)->get();
        }

        // Placeholder reviews (jika belum ada fitur review)
        $reviews = collect(); // bisa diisi dari tabel reviews jika ada

        // Pilihan size & color default (opsional; bisa ambil dari DB jika kamu punya kolomnya)
        $sizes  = collect(['S','M','L','XL']);
        $colors = collect(['#111827','#4B5563','#9CA3AF']); // hitam, abu tua, abu muda

        // Harga pembanding (compare_at / old_price) jika ada
        $compare_at = data_get($product, 'compare_at_price') ?? data_get($product, 'old_price');

        // Status wishlist user (opsional; pastikan method hasInWishlist ada di model User jika dipakai)
        $inWishlist = auth()->check() && method_exists(auth()->user(), 'hasInWishlist')
            ? (bool) auth()->user()->hasInWishlist($product->id)
            : false;

        return view('product-detail', compact(
            'product', 'images', 'related', 'reviews', 'sizes', 'colors', 'compare_at', 'inWishlist'
        ));
    }
}
