@extends('admin.layout')

@section('title', 'Product Management')

@section('header-title', 'Product Management')
@section('header-subtitle', 'Manage your product inventory')

@section('header-button')
<a href="#" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-900 font-medium">+ Add Product</a>
@endsection

@section('content')
  {{-- ===== Add New Product ===== --}}
  <div class="bg-white border border-gray-200 rounded-lg mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="text-lg font-semibold text-gray-800">Add New Product</div>
    </div>

    <form action="#" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
          <input class="w-full h-10 border border-gray-300 rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400" type="text" name="name" placeholder="Enter product name">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
          <input class="w-full h-10 border border-gray-300 rounded-md px-3 text-sm bg-gray-50 text-gray-500" type="text" name="slug" value="product-slug" readonly>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
          <select class="w-full h-10 border border-gray-300 rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400" name="category">
            <option value="">Select category</option>
            <option>Electronics</option>
            <option>Sports</option>
            <option>Fashion</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
          <input class="w-full h-10 border border-gray-300 rounded-md px-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400" type="number" step="0.01" name="price" placeholder="0.00">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
        <div class="h-32 border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center text-center">
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
            <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
            <p class="text-xs text-gray-400 mt-0.5">PNG, JPG up to 10MB</p>
          </div>
        </div>
        <input type="file" class="hidden" name="image">
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="px-4 h-9 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-900 font-medium">Save Product</button>
        <button type="reset" class="px-4 h-9 bg-white border border-gray-300 text-sm rounded-md text-gray-700 hover:bg-gray-50 font-medium">Cancel</button>
      </div>
    </form>
  </div>

  {{-- ===== Products List ===== --}}
  <div class="bg-white border border-gray-200 rounded-lg">
    <div class="px-6 py-4 flex items-center justify-between border-b">
      <h3 class="text-lg font-semibold text-gray-800">Products List</h3>
      <div class="flex items-center gap-2">
        <div class="relative">
          <input type="text" placeholder="Search products..." class="w-64 h-9 border border-gray-300 rounded-md pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-2 text-gray-400" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
        </div>
        <button class="w-9 h-9 border border-gray-300 rounded-md flex items-center justify-center hover:bg-gray-50">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
          <tr>
            <th class="px-6 py-3 text-left font-medium">Product</th>
            <th class="px-6 py-3 text-left font-medium">Category</th>
            <th class="px-6 py-3 text-left font-medium">Price</th>
            <th class="px-6 py-3 text-left font-medium">Status</th>
            <th class="px-6 py-3 text-left font-medium">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white text-sm divide-y divide-gray-200">
          <tr>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-md bg-gray-100 flex items-center justify-center text-xs text-gray-500">IMG</div>
                <div>
                  <div class="text-gray-900 font-medium">Wireless Headphones</div>
                  <div class="text-xs text-gray-500">wireless-headphones</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600">Electronics</td>
            <td class="px-6 py-4 text-gray-600">$99.99</td>
            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span></td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-4 text-gray-500">
                <a href="#" class="hover:text-gray-900"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                <a href="#" class="hover:text-red-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></a>
              </div>
            </td>
          </tr>
          <tr>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-md bg-gray-100 flex items-center justify-center text-xs text-gray-500">IMG</div>
                <div>
                  <div class="text-gray-900 font-medium">Smart Watch</div>
                  <div class="text-xs text-gray-500">smart-watch</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600">Electronics</td>
            <td class="px-6 py-4 text-gray-600">$298.99</td>
            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span></td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-4 text-gray-500">
                <a href="#" class="hover:text-gray-900"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                <a href="#" class="hover:text-red-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></a>
              </div>
            </td>
          </tr>
          <tr>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-md bg-gray-100 flex items-center justify-center text-xs text-gray-500">IMG</div>
                <div>
                  <div class="text-gray-900 font-medium">Running Shoes</div>
                  <div class="text-xs text-gray-500">running-shoes</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600">Sports</td>
            <td class="px-6 py-4 text-gray-600">$129.99</td>
            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Draft</span></td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-4 text-gray-500">
                <a href="#" class="hover:text-gray-900"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                <a href="#" class="hover:text-red-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      {{-- Pagination --}}
      <div class="flex items-center justify-between px-6 py-3 bg-white border-t text-sm text-gray-600">
        <div>Showing 1 to 3 of 3 results</div>
        <div class="flex items-center gap-2">
          <button class="px-3 h-8 bg-white border border-gray-300 rounded-md text-gray-500 cursor-not-allowed text-xs">Previous</button>
          <span class="w-8 h-8 flex items-center justify-center text-white bg-gray-800 text-xs rounded-md">1</span>
          <button class="px-3 h-8 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-xs">Next</button>
        </div>
      </div>
    </div>
  </div>
@endsection
