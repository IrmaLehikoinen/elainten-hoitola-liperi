<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\BookingWizardController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\BookingHoldController;

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
    '/admin/bookings/hold',
    [BookingHoldController::class, 'store']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.bookings.hold.store');

Route::delete(
    '/admin/bookings/hold',
    [BookingHoldController::class, 'destroy']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.bookings.hold.destroy');

Route::post(
    '/admin/reminders/{reminder}/toggle',
    [ReminderController::class, 'toggle']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.reminders.toggle');

Route::delete(
    '/admin/reminders/{reminder}',
    [ReminderController::class, 'destroy']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.reminders.destroy');

Route::get(
    '/admin/customers',
    [CustomerController::class, 'index']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.customers.index');

Route::get(
    '/admin/customers/{customer}',
    [CustomerController::class, 'show']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.customers.show');

Route::patch(
    '/admin/customers/{customer}',
    [CustomerController::class, 'update']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.customers.update');

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

Route::delete(
    '/admin/pets/{pet}',
    [PetController::class, 'destroy']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.pets.destroy');

Route::post(
    '/admin/reminders',
    [ReminderController::class, 'store']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.reminders.store');

Route::post(
    '/admin/customers',
    [CustomerController::class, 'store']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.customers.store');

Route::post(
    '/admin/pets',
    [PetController::class, 'store']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.pets.store');

Route::get(
    '/admin/bookings/uusi',
    [BookingWizardController::class, 'create']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.bookings.create');

Route::post(
    '/admin/bookings/availability',
    [BookingWizardController::class, 'availability']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.bookings.availability');

Route::post(
    '/admin/bookings/wizard',
    [BookingWizardController::class, 'store']
)
    ->middleware(['auth', 'verified'])
    ->name('admin.bookings.wizard-store');

Route::middleware(['auth', 'verified'])->prefix('admin/settings')->name('admin.settings.')->group(function () {
Route::get('/', [CompanySettingsController::class, 'index'])->name('index');

Route::post('/deposit', [CompanySettingsController::class, 'updateDepositSettings'])->name('deposit.update');

    Route::post('/base-rate', [CompanySettingsController::class, 'updateBaseRate'])->name('base-rate.update');

    Route::post('/resources', [CompanySettingsController::class, 'storeResource'])->name('resources.store');
    Route::patch('/resources/{resource}', [CompanySettingsController::class, 'updateResource'])->name('resources.update');
    Route::delete('/resources/{resource}', [CompanySettingsController::class, 'destroyResource'])->name('resources.destroy');

    Route::post('/services', [CompanySettingsController::class, 'storeService'])->name('services.store');
    Route::patch('/services/{service}', [CompanySettingsController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [CompanySettingsController::class, 'destroyService'])->name('services.destroy');

    Route::post('/reminder-types', [CompanySettingsController::class, 'storeReminderType'])->name('reminder-types.store');
    Route::patch('/reminder-types/{reminderType}', [CompanySettingsController::class, 'updateReminderType'])->name('reminder-types.update');
    Route::delete('/reminder-types/{reminderType}', [CompanySettingsController::class, 'destroyReminderType'])->name('reminder-types.destroy');

    Route::post('/care-types', [CompanySettingsController::class, 'storeCareType'])->name('care-types.store');
    Route::patch('/care-types/{careType}', [CompanySettingsController::class, 'updateCareType'])->name('care-types.update');
    Route::delete('/care-types/{careType}', [CompanySettingsController::class, 'destroyCareType'])->name('care-types.destroy');
});

    require __DIR__.'/auth.php';


use App\Http\Controllers\PaymentController;

Route::get('/varaukset/{booking}/maksa', [PaymentController::class, 'checkout'])->name('payment.checkout');

Route::get('/maksu/onnistui', function () {
    return 'Maksu onnistui! Kiitos varauksestasi.';
});

Route::get('/maksu/peruttu', function () {
    return 'Maksu peruttiin.';
});

Route::get('/calendar', [CalendarController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('calendar.index');

Route::get('/admin/calendar/{date}', [CalendarController::class, 'day'])
    ->middleware(['auth', 'verified'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('admin.calendar.day');

Route::get('/admin/varaukset', [AdminBookingController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('admin.bookings.index');

use App\Http\Controllers\StripeWebhookController;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);