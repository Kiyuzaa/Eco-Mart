<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Ambil relasi yang dibutuhkan
        $wishlistItems = $user->wishlists()->with('product')->get();
        $orders        = $user->orders()->latest()->paginate(5); // riwayat pesanan (tampilan)

        // ====== Sinkronisasi Reward Points dari Riwayat Pembelian ======
        // Aturan konversi & status yang berhak mendapat poin
        $rate = (float) config('ecomart.points_per_rupiah', 1 / 10000); // default: 1 poin / Rp10.000
        $pointableStatuses = config('ecomart.pointable_status', ['paid', 'completed', 'delivered']);

        // Hitung poin dari seluruh order yang valid (tanpa paginasi agar akurat)
        $ordersForPoints = $user->orders()
            ->whereIn('status', $pointableStatuses)
            ->get();

        $pointsFromPurchases = $ordersForPoints->sum(function ($o) use ($rate) {
            return (int) floor($o->total_price * $rate);
        });

        // Jika punya skema poin referral, ambil dari kolom user; jika tidak, akan 0
        $pointsFromReferrals = (int) ($user->referral_points ?? 0);

        $totalPoints = $pointsFromPurchases + $pointsFromReferrals;
        // ===============================================================

        $testimonials = $user->testimonials()->latest()->get();

        return view('profile.profile', compact(
            'user',
            'orders',
            'wishlistItems',
            'testimonials',
            'pointsFromPurchases',
            'pointsFromReferrals',
            'totalPoints'
        ));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $path;
        }

        $user->update($validatedData);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
