<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Profil - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    <x-navbar />

    <div class="container mx-auto my-8 px-4 sm:px-6 lg:px-8">
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- User Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col sm:flex-row items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-4">
                <img class="w-20 h-20 rounded-full object-cover border-4 border-green-200" src="{{ asset('storage/' . $user->avatar) . '?v=' . $user->updated_at->timestamp }}" alt="User Avatar" onerror="this.onerror=null;this.src='https://i.pravatar.cc/150?u={{ $user->email }}';">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->phone ?? 'No phone number' }}</p>
                    <div class="mt-2 flex items-center text-sm text-yellow-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span>Premium Member</span>
                        <span class="mx-2 text-gray-300">|</span>
                        <span class="text-gray-500 dark:text-gray-400">Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="w-full sm:w-auto bg-gray-800 dark:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-600 text-center">
                Edit Profile
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center gap-4">
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01"></path></svg></div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Reward Points</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">2,450</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center gap-4">
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full"><svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg></div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $orders->count() }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center gap-4">
                <div class="bg-red-100 dark:bg-red-900 p-3 rounded-full"><svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Waste Managed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">125 <span class="text-lg">kg</span></p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center gap-4">
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full"><svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg></div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">CO2 Saved</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">89 <span class="text-lg">kg</span></p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Full Name</span><span>{{ $user->name }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Email</span><span>{{ $user->email }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Phone</span><span>{{ $user->phone ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Address</span><span>{{ $user->address ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Join Date</span><span>{{ $user->created_at->format('M d, Y') }}</span></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Reward Points</h3>
                    <div class="text-center mb-4">
                        <p class="text-4xl font-bold text-blue-500">2,450</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Available Points</p>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">From purchases</span><span>1,890 pts</span></div>
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">From referrals</span><span>560 pts</span></div>
                    </div>
                    <button class="mt-6 w-full bg-blue-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-600">Redeem Points</button>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Purchase History</h3>
                        <a href="#" class="text-sm text-green-600 hover:underline">View All</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($orders as $order)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">IMG</div>
                            <div class="flex-grow">
                                <p class="font-medium">Pesanan #{{ $order->id }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                <p class="text-sm text-green-500">+{{-- Placeholder --}}50 points</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No purchase history yet.</p>
                        @endforelse
                    </div>
                     <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Waste Management Statistics</h3>
                    {{-- Placeholder untuk statistik --}}
                    <p class="text-sm text-gray-500 dark:text-gray-400">Statistik pengelolaan limbah Anda akan muncul di sini.</p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
