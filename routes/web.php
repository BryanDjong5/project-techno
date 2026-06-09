<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;

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

    Route::get('/rating/{orderId}', [RatingController::class, 'showRating']);
    Route::post('/rating', [RatingController::class, 'kirimRating']);
});

Route::get('/signup', function () {
    return response()->file(public_path('signup.html'));
});

Route::middleware('auth')->group(function () {
    Route::get('/cart/data',         [CartController::class, 'getCart']);
    Route::post('/cart/add',         [CartController::class, 'addToCart']);
    Route::post('/cart/update-qty',  [CartController::class, 'updateQty']);
    Route::post('/cart/remove',      [CartController::class, 'removeItem']);
    Route::post('/cart/checkout',    [CartController::class, 'checkout']);
    Route::post('/cart/clear',       [CartController::class, 'clearCart']);

    Route::get('/cart', function () {
    return response()->file(public_path('fiturKeranjang.html'));
});

Route::get('/chat', [ChatController::class, 'showChat']);
Route::get('/user-info', [ChatController::class, 'userInfo']);
Route::post('/chat/send',[ChatController::class, 'send']);
});

