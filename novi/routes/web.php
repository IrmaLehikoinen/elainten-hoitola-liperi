<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminBookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get(
    '/admin/bookings/customer-search',
    [AdminBookingController::class, 'searchCustomer']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.bookings.customer-search');

Route::post(
    '/admin/bookings',
    [AdminBookingController::class, 'store']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.bookings.store');

    require __DIR__.'/auth.php';


use App\Http\Controllers\PaymentController;

Route::get('/varaukset/{booking}/maksa', [PaymentController::class, 'checkout']);

Route::get('/maksu/onnistui', function () {
    return 'Maksu onnistui! Kiitos varauksestasi.';
});

Route::get('/maksu/peruttu', function () {
    return 'Maksu peruttiin.';
});

Route::get('/calendar', function () {
    return view('calendar.index');
})->middleware(['auth', 'verified'])->name('calendar.index');

use App\Http\Controllers\StripeWebhookController;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);