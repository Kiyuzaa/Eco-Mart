<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $customers = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->where('role', 'customer')  // pastikan kolom 'role' ada
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'q'));
    }
    public function show(\App\Models\User $user)
{
    // ringkas: filter hanya role customer biar aman
    abort_unless($user->role === 'customer', 404);

    // statistik singkat
    $stats = [
        'orders_count' => $user->orders()->count(),
        'spent_total'  => $user->orders()->sum('total_price'),
        'last_order_at'=> optional($user->orders()->latest()->first())->created_at,
    ];

    // riwayat pembelian + item + produk (paginate)
    $orders = $user->orders()
        ->with(['items.product:id,name'])   // minimal field produk
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('admin.customers.show', compact('user','orders','stats'));
}
}
