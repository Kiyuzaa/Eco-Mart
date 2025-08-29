@extends('admin.layout')

@section('title','Edit Product')
@section('header-title','Edit Product')

@section('content')
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
    <div class="px-5 py-3 border-b border-gray-100">
      <h3 class="font-semibold text-gray-800">Edit: {{ $product->name }}</h3>
    </div>
    <div class="p-5">
      <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf @method('PUT')

        <div>
          <label class="block text-sm text-gray-600 mb-1">Product Name</label>
          <input type="text" name="name" class="w-full rounded border-gray-300" value="{{ old('name',$product->name) }}">
        </div>

        <div>
          <label class="block text-sm text-gray-600 mb-1">Slug</label>
          <input type="text" name="slug" class="w-full rounded border-gray-300" value="{{ old('slug',$product->slug) }}">
        </div>

        <div>
          <label class="block text-sm text-gray-600 mb-1">Category</label>
          <select name="category_id" class="w-full rounded border-gray-300">
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected(old('category_id',$product->category_id)==$cat->id)>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600 mb-1">Price</label>
          <input type="number" step="0.01" name="price" class="w-full rounded border-gray-300" value="{{ old('price',$product->price) }}">
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600 mb-1">Product Image</label>
          <input type="file" name="image" class="block w-full text-sm text-gray-700">
          @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" class="mt-2 w-20 h-20 object-cover rounded border">
          @endif
        </div>

        <div class="md:col-span-2 flex gap-2">
          <button class="px-4 py-2 rounded bg-gray-900 text-white">Update</button>
          <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded border border-gray-300 text-gray-700">Back</a>
        </div>
      </form>
    </div>
  </div>
@endsection
