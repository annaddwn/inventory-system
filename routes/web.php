<?php
// routes/web.php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// User Routes (No Login Required)
Route::get('/', [UserController::class, 'showInfo'])->name('user.info');
Route::post('/user/info', [UserController::class, 'storeInfo'])->name('user.info.store');
Route::get('/items', [UserController::class, 'index'])->name('user.index');
Route::get('/cart', [UserController::class, 'cart'])->name('user.cart');
Route::post('/borrow', [UserController::class, 'borrowItems'])->name('user.borrow');
Route::get('/reset', [UserController::class, 'resetSession'])->name('user.reset');

// Admin Authentication Routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Routes (Login Required)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/items', [AdminController::class, 'items'])->name('items');
    Route::post('/items', [AdminController::class, 'storeItem'])->name('items.store');
    Route::post('/items/{id}/stock', [AdminController::class, 'updateStock'])->name('items.stock');
    Route::get('/borrowings', [AdminController::class, 'borrowings'])->name('borrowings');
    Route::post('/borrowings/{id}/return', [AdminController::class, 'returnItem'])->name('borrowings.return');
});