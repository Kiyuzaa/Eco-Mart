<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth routes (login, register, password reset, dll)
Auth::routes();

// Halaman utama & statis
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');

// Produk & kategori (publik)
Route::get('/product', [ProductController::class, 'index'])->name('product.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// AI Bot & Kontak (publik)
Route::view('/ai-bot', 'ai-bot')->name('ai.bot');
Route::get('/chat', [AiChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [AiChatController::class, 'send'])->name('chat.send');

Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// -----------------------------
// Grup route butuh login (auth)
// -----------------------------
Route::middleware('auth')->group(function () {

    // Profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::delete('/wishlist/bulk-remove', [WishlistController::class, 'bulkRemove'])->name('wishlist.bulk-remove');
    Route::post('/wishlist/bulk-add-to-cart', [WishlistController::class, 'bulkAddToCart'])->name('wishlist.bulkAddToCart');

    // Keranjang
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('cart.remove');
    Route::post('/cart/apply-code', [CartController::class, 'applyCode'])->name('cart.apply-code');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/place', [CheckoutController::class, 'place'])->name('checkout.place');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');             // daftar pesanan
    Route::get('/orders/{order}/waiting', [CheckoutController::class, 'waiting'])->name('orders.waiting'); // status menunggu
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');      // detail pesanan

    // Halaman sukses (opsional, jika punya view-nya)
    Route::view('/orders/success', 'orders.success')->name('orders.success');
});

// -----------------------------
// Admin area (auth + role:admin)
// -----------------------------
Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('dashboard');

        // Customers
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{user}', [CustomerController::class, 'show'])->name('customers.show');

        // Products
        Route::get('/products',                [AdminProductController::class, 'index'])->name('products.index');
        Route::post('/products',               [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}',      [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}',   [AdminProductController::class, 'destroy'])->name('products.destroy');
    });
