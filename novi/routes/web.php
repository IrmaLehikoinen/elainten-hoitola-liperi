<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\CalendarController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

Route::post(
    '/admin/reminders/{reminder}/toggle',
    [ReminderController::class, 'toggle']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.reminders.toggle');

Route::get(
    '/admin/customers/{customer}',
    [CustomerController::class, 'show']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.customers.show');

Route::get(
    '/admin/pets/{pet}',
    [PetController::class, 'show']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.pets.show');

Route::patch(
    '/admin/pets/{pet}',
    [PetController::class, 'update']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.pets.update');

Route::post(
    '/admin/reminders',
    [ReminderController::class, 'store']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.reminders.store');

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

Route::get('/admin/calendar/{date}', [CalendarController::class, 'day'])
    ->middleware(['auth', 'verified'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('admin.calendar.day');

use App\Http\Controllers\StripeWebhookController;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);