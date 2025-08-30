@extends('admin.layout')

@section('title','Manajemen Pelanggan')
@section('header-title','Pelanggan')
@section('header-subtitle','Kelola data pelanggan Anda')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

  <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
    <h3 class="text-lg font-semibold text-slate-800">Daftar Pelanggan</h3>

    <form method="GET" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau email…"
             class="h-10 w-56 rounded-md border border-slate-300 px-3 text-sm
                    focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
      <button class="h-10 px-4 rounded-md border border-emerald-500 text-emerald-600 text-sm font-medium
                     hover:bg-emerald-50">Cari</button>
    </form>
  </div>

  @if($customers->count())
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="px-5 py-3 text-left font-semibold">Nama</th>
            <th class="px-5 py-3 text-left font-semibold">Email</th>
            <th class="px-5 py-3 text-left font-semibold">Bergabung</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($customers as $c)
            <tr class="hover:bg-slate-50/60">
              {{-- Nama bisa diklik --}}
              <td class="px-5 py-3 font-medium text-slate-800">
                <a href="{{ route('admin.customers.show', $c) }}" class="text-emerald-600 hover:underline">
                  {{ $c->name }}
                </a>
              </td>
              <td class="px-5 py-3 text-slate-700">{{ $c->email }}</td>
              <td class="px-5 py-3 text-slate-600">{{ $c->created_at?->format('d M Y') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="px-5 py-3 border-t border-slate-200 bg-white">
      {{ $customers->links() }}
    </div>
  @else
    <div class="px-6 py-10 text-center text-slate-500">Belum ada pelanggan.</div>
  @endif
</div>
@endsection
