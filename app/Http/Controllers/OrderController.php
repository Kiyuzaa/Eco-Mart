<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Auth::user()
            ->orders()
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}
