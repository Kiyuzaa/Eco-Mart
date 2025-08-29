<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">

    <x-navbar />

    <div class="container mx-auto my-10 px-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Keranjang Belanja Anda</h1>

        {{-- ... (kode untuk menampilkan item keranjang) ... --}}

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            @if(isset($products) && count($products) > 0)
                {{-- ... (kode perulangan produk) ... --}}

                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex justify-end items-center">
                        <span class="text-lg font-medium text-gray-900 dark:text-white">Total Belanja:</span>
                        <span class="text-2xl font-bold text-green-600 ml-4">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-end mt-4">
                        {{-- PERBAIKAN: Mengarahkan ke route checkout --}}
                        <a href="{{ route('checkout') }}" class="w-full sm:w-auto text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Lanjutkan ke Checkout
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Keranjang Anda Kosong</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Sepertinya Anda belum menambahkan produk apapun.</p>
                    <a href="{{ route('product.index') }}" class="mt-6 inline-block text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5">
                        Mulai Belanja
                    </a>
                </div>
            @endif
        </div>
    </div>

</body>
</html>
