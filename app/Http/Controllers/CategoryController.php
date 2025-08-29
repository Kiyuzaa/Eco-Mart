<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    // GET /categories/{category:slug}
    public function show(Category $category, Request $request)
    {
        // Ambil produk dalam kategori ini (sesuaikan nama kolomnya)
        $products = Product::where('category_id', $category->id)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('categories.show', compact('category', 'products'));
    }
}