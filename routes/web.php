<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Landing page (publik)
Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/menu', [ShopController::class, 'menu'])->name('menu');

// File unggahan (foto produk, QRIS, bukti bayar) dari disk 'public'.
// Tidak butuh symlink public/storage, sehingga jalan juga di Windows tanpa hak admin.
// Bukti bayar (proofs/) hanya untuk admin dan pemilik pesanan.
Route::get('/media/{path}', function (Request $request, string $path) {
    // Normalisasi dulu: './proofs/x.jpg', 'proofs//x.jpg' atau 'Proofs/x.jpg' (Windows tidak peka huruf besar)
    // tidak boleh lolos dari pemeriksaan folder privat di bawah.
    $path = trim(preg_replace('#/+#', '/', $path), '/');
    abort_if(
        $path === ''
        || str_contains($path, "\0")
        || str_contains($path, '\\')
        || array_intersect(explode('/', $path), ['.', '..']),
        404
    );

    $private = str_starts_with(strtolower($path), 'proofs/');
    if ($private) {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || Order::where('payment_proof', $path)->where('user_id', $user->id)->exists()), 404);
    }
    $disk = Storage::disk('public');
    abort_unless($disk->exists($path), 404);

    return response()->file($disk->path($path), [
        'Cache-Control' => $private ? 'private, max-age=3600' : 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('media');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Customer: wajib login untuk beli, bayar, dan chat
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{product}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/bukti', [OrderController::class, 'uploadProof'])->name('orders.proof');

    Route::post('/ulasan', [ReviewController::class, 'store'])->middleware('throttle:6,1')->name('reviews.store');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/chat', [ChatController::class, 'customerIndex'])->name('chat');
    Route::get('/chat/messages', [ChatController::class, 'customerMessages'])->name('chat.messages');
    Route::post('/chat', [ChatController::class, 'customerSend'])->name('chat.send');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('notifikasi', [DashboardController::class, 'notifications'])->name('notifications');

    Route::resource('produk', ProductController::class)
        ->except('show')
        ->parameters(['produk' => 'product'])
        ->names('products');

    Route::get('pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('pesanan/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    Route::get('ulasan', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('ulasan/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('pengaturan', [SettingController::class, 'update'])->name('settings.update');
    Route::get('profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('inventori', [InventoryController::class, 'index'])->name('inventory.index');
    Route::patch('inventori/{product}', [InventoryController::class, 'update'])->name('inventory.update');

    Route::get('laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('laporan/ekspor', [ReportController::class, 'export'])->name('reports.export');

    Route::get('chat/threads', [ChatController::class, 'threads'])->name('chat.threads');
    Route::get('chat/{customer?}', [ChatController::class, 'adminIndex'])->name('chat');
    Route::get('chat/{customer}/messages', [ChatController::class, 'adminMessages'])->name('chat.messages');
    Route::post('chat/{customer}', [ChatController::class, 'adminSend'])->name('chat.send');
});
