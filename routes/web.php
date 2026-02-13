<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/catalog', [StoreController::class, 'catalog'])->name('catalog');
Route::get('/produk/{produk}', [StoreController::class, 'show'])->name('produk.show');

// ============================================================
// MIDTRANS WEBHOOK (no auth - called by Midtrans server)
// ============================================================
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

// ============================================================
// AUTH ROUTES (Guest only)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])
        ->middleware('throttle:5,1') // 5 attempts per minute
        ->name('login.proses');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])
        ->middleware('throttle:3,1') // 3 attempts per minute
        ->name('register.proses');

    // Forgot Password (Self-Service Password Reset)
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->middleware('throttle:3,1') // 3 attempts per minute
        ->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'processChangePassword'])->name('password.change.proses');
});

// ============================================================
// CART ROUTES (Auth required)
// ============================================================
Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{produk}/update', [CartController::class, 'update'])->name('update');
    Route::delete('/{produk}/remove', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/data', [CartController::class, 'getCartData'])->name('data');
});

// ============================================================
// CUSTOMER ROUTES (Auth required)
// ============================================================
Route::middleware('auth')->group(function () {
    Route::post('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::get('/order/{order}/success', [StoreController::class, 'orderSuccess'])->name('order.success');
    Route::post('/order/{order}/payment', [StoreController::class, 'uploadPaymentProof'])
        ->middleware('throttle:10,1') // 10 uploads per minute (prevent spam)
        ->name('order.payment');
    Route::post('/order/{order}/cancel', [StoreController::class, 'cancelOrder'])->name('order.cancel');
    Route::get('/my-orders', [StoreController::class, 'myOrders'])->name('my.orders');
    Route::get('/order/{order}/track', [StoreController::class, 'trackOrder'])->name('order.track');

    // Reviews
    Route::post('/produk/{produk}/review', [ReviewController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('review.store');
    Route::delete('/review/{review}', [ReviewController::class, 'destroy'])->name('review.destroy');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{produk}', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // Customer Chat
    Route::get('/chat', [ChatController::class, 'customerChat'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'customerSend'])
        ->middleware('throttle:30,1') // 30 messages per minute
        ->name('chat.send');
    Route::get('/chat/poll', [ChatController::class, 'customerPoll'])->name('chat.poll');
    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread');

    // Midtrans Payment
    Route::post('/payment/{order}/snap-token', [PaymentController::class, 'createSnapToken'])->name('payment.snap');
});

// ============================================================
// ADMIN ROUTES (Auth + Admin Middleware)
// ============================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'chartData'])->name('dashboard.chart');

    // Product CRUD
    Route::resource('produk', ProdukController::class);

    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/verify-payment', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify');
    Route::post('/orders/{order}/reject-payment', [AdminOrderController::class, 'rejectPayment'])->name('orders.reject');
    Route::post('/orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('orders.confirm');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Shipping Zones
    Route::resource('shipping-zones', \App\Http\Controllers\ShippingZoneController::class);

    // Admin Chat
    Route::get('/chat', [ChatController::class, 'adminIndex'])->name('chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'adminChat'])->name('chat.show');
    Route::post('/chat/{user}/send', [ChatController::class, 'adminSend'])
        ->middleware('throttle:30,1')
        ->name('chat.send');
    Route::get('/chat/{user}/poll', [ChatController::class, 'adminPoll'])->name('chat.poll');

    // Notifications
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [AdminNotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/clear-read', [AdminNotificationController::class, 'clearRead'])->name('notifications.clearRead');

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
});