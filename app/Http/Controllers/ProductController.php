<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $products = Product::query()
            ->when($q, fn($qb) => $qb->where('name','like',"%{$q}%")->orWhere('slug','like',"%{$q}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id','name']);

        return view('admin.dashboard', [
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:150'],
            'slug'        => ['nullable','string','max:160','unique:products,slug'],
            'category_id' => ['required','exists:categories,id'],
            'price'       => ['required','numeric','min:0'],
            'image'       => ['nullable','image','mimes:png,jpg,jpeg','max:10240'], // 10MB
        ]);

        // generate slug kalau kosong
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        if ($request->hasFile('image')) {
            // simpan ke storage/app/public/products
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path; // kolom `image` string di tabel products
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success','Product created');
    }
}
