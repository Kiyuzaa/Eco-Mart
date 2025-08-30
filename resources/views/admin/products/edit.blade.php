@extends('admin.layout')

@section('title','Ubah Produk')
@section('header-title','Ubah Produk')
@section('header-subtitle','Perbarui detail produk')

@section('content')
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
    <div class="px-5 py-3 border-b border-slate-200">
      <h3 class="font-semibold text-slate-800">Ubah: {{ $product->name }}</h3>
    </div>

    <div class="p-5">
      <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf @method('PUT')

        <div>
          <label class="block text-sm text-slate-600 mb-1">Nama Produk</label>
          <input type="text" name="name" required
                 class="w-full rounded-md border border-slate-300 px-3 h-10 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                 value="{{ old('name',$product->name) }}">
        </div>

        <div>
          <label class="block text-sm text-slate-600 mb-1">Slug</label>
          <input type="text" name="slug"
                 class="w-full rounded-md border border-slate-300 px-3 h-10 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                 value="{{ old('slug',$product->slug) }}">
        </div>

        <div>
          <label class="block text-sm text-slate-600 mb-1">Kategori</label>
          <select name="category_id" required
                  class="w-full rounded-md border border-slate-300 px-3 h-10 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected(old('category_id',$product->category_id)==$cat->id)>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm text-slate-600 mb-1">Harga</label>
          <input type="number" step="0.01" min="0" name="price" required
                 class="w-full rounded-md border border-slate-300 px-3 h-10 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                 value="{{ old('price',$product->price) }}">
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm text-slate-600 mb-2">Gambar Produk</label>
          <input type="file" name="image" class="block w-full text-sm text-slate-700">
          @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" class="mt-3 w-24 h-24 object-cover rounded-lg border border-slate-200" alt="">
          @endif
        </div>

        <div class="md:col-span-2 flex gap-2 pt-2">
          <button class="px-4 h-10 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-black">Perbarui</button>
          <a href="{{ route('admin.products.index') }}"
             class="px-4 h-10 rounded-md border border-slate-300 text-slate-700 text-sm hover:bg-slate-50">Kembali</a>
        </div>
      </form>
    </div>
  </div>
@endsection