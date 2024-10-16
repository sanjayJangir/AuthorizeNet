<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('authorize/payment', [HomeController::class, 'paymentPost'])->name('authorize.payment');
Route::post('dopay/online', [HomeController::class, 'handleonlinePay'])->name('dopay.online');

Route::get('new', [PaymentController::class, 'index']);
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('processPayment');
