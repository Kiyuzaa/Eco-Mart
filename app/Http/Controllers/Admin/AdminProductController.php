<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;

class AdminProductController extends Controller
{
    /**
     * Dashboard admin:
     * - Form tambah produk
     * - Tabel list produk (dengan search & pagination)
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $products = Product::with('category')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        // View yang kamu sebut adalah dashboard.blade.php
        return view('admin.dashboard', compact('products', 'categories'));
    }

    /**
     * Simpan produk baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:10240'], // 10MB
        ]);

        // Auto-slug kalau kosong
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // Pastikan unik kalau slug auto-duplicate
        if (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] .= '-' . Str::random(4);
        }

        // Upload gambar (opsional)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path; // disimpan relatif storage/app/public
        }

        Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Halaman edit (opsional jika punya view terpisah)
     * Jika belum ada view edit, bisa dilewati/diarahkan ke dashboard dengan parameter.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update data produk
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $product->id],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:10240'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // Handle gambar baru
        if ($request->hasFile('image')) {
            // Hapus file lama (jika ada)
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Hapus produk
     */
    public function destroy(Product $product)
    {
        // Hapus file gambar
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }
}
