<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth routes (login, register, password, dll)
Auth::routes();

// Halaman Utama & Statis
Route::get('/', fn() => app(HomeController::class)->index())->name('home');
Route::view('/about', 'about')->name('about');

// Publik: Produk & Kategori
Route::get('/product', [ProductController::class, 'index'])->name('product.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Publik: AI Bot & Kontak
Route::view('/ai-bot', 'ai-bot')->name('ai.bot');
Route::get('/chat', [AiChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [AiChatController::class, 'send'])->name('chat.send');

Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Butuh login
Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Wishlist (hapus duplikasi toggle)
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

    // Checkout — SESUAI controller kamu: show() & place()
    // Checkout — pakai nama 'checkout' (bukan 'checkout.show')
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout/place', [CheckoutController::class, 'place'])->name('checkout.place');

Route::get('/orders/{order}/waiting', [CheckoutController::class, 'waiting'])->name('order.waiting');
Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');   


    // (Opsional) halaman sukses sederhana jika kamu memang punya view-nya
    Route::view('/order/success', 'orders.success')->name('order.success');
});

// Admin
Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('dashboard');

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{user}', [CustomerController::class, 'show'])->name('customers.show');

        // Products
        Route::get('/products',                [AdminProductController::class, 'index'])->name('products.index');
        Route::post('/products',               [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}',      [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}',   [AdminProductController::class, 'destroy'])->name('products.destroy');
    });
