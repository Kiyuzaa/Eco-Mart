<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        // Filter harga (contoh angka USD seperti di Blade)
        // Sesuaikan dengan satuan harga di DB-mu jika pakai IDR.
        if ($priceRange === 'under-25') {
            $query->where('price', '<', 25);
        } elseif ($priceRange === '25-50') {
            $query->whereBetween('price', [25, 50]);
        } elseif ($priceRange === '50-100') {
            $query->whereBetween('price', [50, 100]);
        } elseif ($priceRange === 'over-100') {
            $query->where('price', '>', 100);
        }

        // Filter material (jika kolom 'material' ada)
        if (!empty($selectedMaterials)) {
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

        // Materials unik (kalau kolom 'material' ada). Jika tidak ada, kirim array kosong.
        $materials = Product::whereNotNull('material')
            ->distinct()->pluck('material')->filter()->values()->all();

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
        // Sesuaikan dengan view detail milikmu (di pesanmu ada product-detail.blade.php)
        return view('product-detail', compact('product'));
    }
}
