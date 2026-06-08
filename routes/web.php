<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/index.html', [HomeController::class, 'index']);

Route::get('/buy', function () {
    return response()->file(public_path('buy.html'));
});

Route::get('/search-game', [SearchController::class, 'searchGame']);
Route::post('/buy', [OrderController::class, 'buyNow']);

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::get('/login', [LoginController::class, 'showLogin']);
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegister']);
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/menungguPembayaran/{orderId}', [PaymentController::class, 'waitPayment']);
    Route::post('/pembayaran/konfirmasi', [PaymentController::class, 'confirmPayment']);
    Route::post('/pembayaran/batal/{orderId}', [PaymentController::class, 'cancelPayment']);
});

