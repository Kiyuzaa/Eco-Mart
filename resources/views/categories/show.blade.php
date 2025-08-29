@extends('layouts.app')
@section('title', $category->name)
@section('content')
  <h1 class="text-2xl font-bold mb-4">{{ $category->name }}</h1>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($products as $product)
      <x-product-card :product="$product" />
    @endforeach
  </div>
  <div class="mt-4">{{ $products->links() }}</div>
@endsection