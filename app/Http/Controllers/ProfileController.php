<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman dasbor profil.
     */
    public function index()
    {
        $user = Auth::user();

        // Relasi utama
        $wishlistItems = method_exists($user, 'wishlists')
            ? $user->wishlists()->with('product')->get()
            : collect();

        $orders = method_exists($user, 'orders')
            ? $user->orders()->latest()->paginate(5)
            : collect();

        // ====== Hitung Poin (prioritas dari tabel transaksi poin jika ada) ======
        $rate = (float) config('ecomart.points_per_rupiah', 1 / 10000);
        $pointableStatuses = (array) config('ecomart.pointable_status', ['paid','completed','delivered']);

        $pointsFromPurchases = 0;

        if (Schema::hasTable('reward_point_transactions')) {
            // Jika ada tabel transaksi poin, jumlahkan milik user (kolom yang umum: user_id, points, type)
            $pointsFromPurchases = (int) DB::table('reward_point_transactions')
                ->where('user_id', $user->id)
                ->sum('points');
        } else {
            // Fallback: hitung dari pesanan dengan status yang berhak
            if (method_exists($user, 'orders')) {
                $ordersForPoints = $user->orders()
                    ->whereIn('status', $pointableStatuses)
                    ->get();

                $pointsFromPurchases = $ordersForPoints->sum(function ($o) use ($rate) {
                    $total = (int) ($o->total_price ?? 0);
                    return (int) floor($total * $rate);
                });
            }
        }

        // Poin referral jika ada kolomnya
        $pointsFromReferrals = (int) ($user->referral_points ?? 0);

        $totalPoints = max(0, (int) $pointsFromPurchases + $pointsFromReferrals);
        // =======================================================================

        // Testimoni jika ada relasinya
        $testimonials = method_exists($user, 'testimonials')
            ? $user->testimonials()->latest()->get()
            : collect();

        return view('profile.profile', compact(
            'user',
            'orders',
            'wishlistItems',
            'testimonials',
            'pointsFromPurchases',
            'pointsFromReferrals',
            'totalPoints',
            'pointableStatuses',
            'rate'
        ));
    }

    /**
     * Form edit profil.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update profil.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => [
                'required','string','email','max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
