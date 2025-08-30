{{-- resources/views/profile/profile.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Profil — {{ $user->name }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @else
        <link rel="preconnect" href="https://fonts.bunny.net"/>
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        html,body{font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial}
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    <x-navbar />

    <div class="max-w-7xl mx-auto my-8 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Header Pengguna --}}
        <section class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 flex flex-col sm:flex-row items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                @php
                    $avatar = $user->avatar ? asset('storage/'.$user->avatar).'?v='.$user->updated_at->timestamp : 'https://i.pravatar.cc/150?u='.urlencode($user->email);
                @endphp
                <img
                    class="w-20 h-20 rounded-full object-cover border-4 border-emerald-100"
                    src="{{ $avatar }}"
                    alt="Avatar Pengguna"
                    onerror="this.onerror=null;this.src='https://i.pravatar.cc/150?u={{ urlencode($user->email) }}';"
                >
                <div class="min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $user->phone ?? 'Tidak ada nomor telepon' }}</p>
                    <div class="mt-2 flex items-center flex-wrap gap-x-2 text-sm">
                        <span class="inline-flex items-center text-yellow-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            Anggota Premium
                        </span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500 dark:text-gray-400">Sejak {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex w-full sm:w-auto gap-2">
                <a href="{{ route('profile.edit') }}"
                   class="flex-1 sm:flex-none bg-gray-900 dark:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 dark:hover:bg-gray-600 text-center">
                    Edit Profil
                </a>
                <form action="{{ route('logout') }}" method="POST" class="flex-1 sm:flex-none">
                    @csrf
                    <button type="submit"
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
                        Keluar
                    </button>
                </form>
            </div>
        </section>

        {{-- Kartu Statistik --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <article class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm flex items-center gap-4">
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Poin Hadiah</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totalPoints ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </article>

            <article class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm flex items-center gap-4">
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pesanan</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ is_object($orders) ? $orders->total() : (is_countable($orders) ? count($orders) : 0) }}
                    </p>
                </div>
            </article>

            <article class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm flex items-center gap-4">
                <div class="bg-red-100 dark:bg-red-900 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Limbah Terkelola</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">125 <span class="text-lg">kg</span></p>
                </div>
            </article>

            <article class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm flex items-center gap-4">
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">CO₂ Terselamatkan</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">89 <span class="text-lg">kg</span></p>
                </div>
            </article>
        </section>

        {{-- Grid Utama --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Kolom Kiri --}}
            <aside class="lg:col-span-1 space-y-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Informasi Pribadi</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Nama Lengkap</dt>
                            <dd class="text-right">{{ $user->name }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-right truncate">{{ $user->email }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">No. Telepon</dt>
                            <dd class="text-right">{{ $user->phone ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Alamat</dt>
                            <dd class="text-right">{{ $user->address ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Tanggal Bergabung</dt>
                            <dd class="text-right">{{ $user->created_at->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Poin Hadiah</h3>
                    <div class="text-center mb-4">
                        <p class="text-4xl font-bold text-blue-600">
                            {{ number_format($totalPoints ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Poin Tersedia</p>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Dari pembelian</span><span>{{ number_format($pointsFromPurchases ?? 0, 0, ',', '.') }} pts</span></div>
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Dari referral</span><span>{{ number_format($pointsFromReferrals ?? 0, 0, ',', '.') }} pts</span></div>
                    </div>
                    <button class="mt-6 w-full bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">Tukarkan Poin</button>
                </div>
            </aside>

            {{-- Kolom Kanan --}}
            <main class="lg:col-span-2 space-y-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Riwayat Pembelian</h3>
                        <a href="{{ route('orders.index', [], false) ?? '#' }}" class="text-sm text-emerald-600 hover:underline">Lihat Semua</a>
                    </div>

                    <div class="space-y-4">
                        @forelse($orders as $order)
                            @php
                                // Earned points per order (hanya jika status berhak)
                                $eligible = in_array($order->status, $pointableStatuses ?? []);
                                $earned = $eligible ? (int) floor(((int)$order->total_price) * ($rate ?? (1/10000))) : 0;
                            @endphp
                            <article class="rounded-lg bg-slate-800/60 border border-slate-700 p-4 flex items-center justify-between">
                                <div class="min-w-0">
                                    <div class="font-semibold text-white truncate">Pesanan #{{ $order->id }}</div>
                                    <div class="text-slate-400 text-sm">
                                        {{ $order->created_at->format('d M Y') }} • Status: {{ ucfirst($order->status) }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-white font-semibold">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </div>
                                    <div class="text-emerald-400 text-sm">
                                        +{{ number_format($earned, 0, ',', '.') }} poin
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat pembelian.</p>
                        @endforelse
                    </div>

                    @if(is_object($orders) && method_exists($orders, 'links'))
                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold mb-2">Statistik Pengelolaan Limbah</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Statistik pengelolaan limbah Anda akan muncul di sini.</p>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
