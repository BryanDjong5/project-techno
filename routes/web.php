<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLogin']);
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegister']);
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/menungguPembayaran/{orderId}', [PaymentController::class, 'waitPayment']);
    Route::post('/pembayaran/konfirmasi', [PaymentController::class, 'confirmPayment']);
    Route::post('/pembayaran/batal/(orderId}', [PaymentController::class, 'cancelPayment']);
});