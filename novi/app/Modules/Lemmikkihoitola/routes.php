<?php

// MALLIESIMERKKI: moduulin omat reitit, ei pohjan routes/web.php:ssä.
// Ladataan automaattisesti LemmikkihoitolaServiceProvider::boot():ssa
// (loadRoutesFrom). Tuleva toimialamoduuli tekee oman vastaavan
// routes.php-tiedoston ja lataa sen samalla tavalla omasta
// ServiceProvideristaan.

use App\Modules\Lemmikkihoitola\Http\Controllers\AdminBookingController;


use App\Modules\Lemmikkihoitola\Http\Controllers\BookingHoldController;
use App\Modules\Lemmikkihoitola\Http\Controllers\CalendarCapacityController;
use App\Modules\Lemmikkihoitola\Http\Controllers\CalendarController;
use App\Modules\Lemmikkihoitola\Http\Controllers\CompanySettingsController;
use App\Modules\Lemmikkihoitola\Http\Controllers\CustomerController;
use App\Modules\Lemmikkihoitola\Http\Controllers\DashboardController;
use App\Modules\Lemmikkihoitola\Http\Controllers\InvoiceController;
use App\Modules\Lemmikkihoitola\Http\Controllers\PaymentController;
use App\Modules\Lemmikkihoitola\Http\Controllers\PetController;
use App\Modules\Lemmikkihoitola\Http\Controllers\PublicBookingController;
use App\Modules\Lemmikkihoitola\Http\Controllers\ReminderController;
use App\Modules\Lemmikkihoitola\Http\Controllers\ReportController;
use App\Modules\Lemmikkihoitola\Http\Controllers\ServiceSelectionController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {

    Route::prefix('varaa')->name('public.booking.')->middleware('throttle:60,1')->group(function () {
        Route::get('/', [PublicBookingController::class, 'start'])->name('start');
        Route::get('/vapaat-ajat', fn () => redirect()->route('public.booking.start'));
        Route::post('/vapaat-ajat', [PublicBookingController::class, 'availability'])->name('availability');
        Route::post('/hold', [PublicBookingController::class, 'hold'])->name('hold');
        Route::post('/tunnista', [PublicBookingController::class, 'identify'])->name('identify')->middleware('throttle:5,1');
        Route::get('/vahvista/{customer}', [PublicBookingController::class, 'verify'])->name('verify')->middleware('signed');
        Route::post('/tallenna', [PublicBookingController::class, 'store'])->name('store');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::get('/admin/bookings/{booking}/avaa', [AdminBookingController::class, 'acknowledge'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.acknowledge');

    Route::get('/admin/bookings/customer-search', [AdminBookingController::class, 'searchCustomer'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.customer-search');

    Route::post('/admin/bookings', [AdminBookingController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.store');

    Route::post('/admin/bookings/availability', [AdminBookingController::class, 'availability'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.availability');

    Route::post('/admin/bookings/hold', [BookingHoldController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.hold.store');

    Route::delete('/admin/bookings/hold', [BookingHoldController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.hold.destroy');

    Route::post('/admin/bookings/hold/beacon', [BookingHoldController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.hold.beacon');

    Route::post('/admin/reminders/{reminder}/toggle', [ReminderController::class, 'toggle'])
        ->middleware(['auth', 'verified'])
        ->name('admin.reminders.toggle');

    Route::delete('/admin/reminders/{reminder}', [ReminderController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('admin.reminders.destroy');

    Route::get('/admin/customers', [CustomerController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('admin.customers.index');

    Route::get('/admin/customers/{customer}', [CustomerController::class, 'show'])
        ->middleware(['auth', 'verified'])
        ->name('admin.customers.show');

    Route::patch('/admin/customers/{customer}', [CustomerController::class, 'update'])
        ->middleware(['auth', 'verified'])
        ->name('admin.customers.update');

    Route::delete('/admin/customers/{customer}', [CustomerController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('admin.customers.destroy');

    Route::get('/admin/customers/{customer}/tietopyynto', [CustomerController::class, 'dataExport'])
        ->middleware(['auth', 'verified'])
        ->name('admin.customers.data-export');

    Route::get('/admin/customers/{customer}/tietopyynto/pdf', [CustomerController::class, 'dataExportPdf'])
        ->middleware(['auth', 'verified'])
        ->name('admin.customers.data-export.pdf');

    Route::get('/admin/pets/{pet}', [PetController::class, 'show'])
        ->middleware(['auth', 'verified'])
        ->name('admin.pets.show');

    Route::patch('/admin/pets/{pet}', [PetController::class, 'update'])
        ->middleware(['auth', 'verified'])
        ->name('admin.pets.update');

    Route::delete('/admin/pets/{pet}', [PetController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('admin.pets.destroy');

    Route::post('/admin/reminders', [ReminderController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('admin.reminders.store');

    Route::post('/admin/customers', [CustomerController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('admin.customers.store');

    Route::post('/admin/pets', [PetController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('admin.pets.store');

    Route::middleware(['auth', 'verified'])->prefix('admin/settings')->name('admin.settings.')->group(function () {
        Route::get('/', [CompanySettingsController::class, 'index'])->name('index');

        Route::post('/deposit', [CompanySettingsController::class, 'updateDepositSettings'])->name('deposit.update');

        Route::post('/base-rate', [CompanySettingsController::class, 'updateBaseRate'])->name('base-rate.update');

        Route::post('/company-info', [CompanySettingsController::class, 'updateCompanyInfo'])->name('company-info.update');

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

        Route::post('/booking-fields', [CompanySettingsController::class, 'updatePublicBookingFields'])->name('booking-fields.update');
    });

    Route::get('/varaukset/{booking}/maksa', [PaymentController::class, 'checkout'])
        ->middleware('throttle:30,1')
        ->name('payment.checkout');

    Route::get('/kuitti/{invoice}', [InvoiceController::class, 'show'])
        ->middleware(['auth', 'verified'])
        ->name('invoices.show');

    Route::get('/kuitti/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
        ->middleware(['auth', 'verified'])
        ->name('invoices.pdf');

    Route::get('/kuitti/{invoice}/tulosta', [InvoiceController::class, 'printPdf'])
        ->middleware(['auth', 'verified'])
        ->name('invoices.print');

    Route::get('/maksu/onnistui', function (\Illuminate\Http\Request $request) {
        $booking = \App\Modules\Lemmikkihoitola\Models\Booking::find($request->query('booking'));

        return view('public.booking.success', ['booking' => $booking]);
    });

    Route::get('/maksu/peruttu', function () {
        return view('public.booking.cancelled');
    });

    Route::get('/calendar', [CalendarController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('calendar.index');

    Route::get('/admin/calendar/{date}', [CalendarController::class, 'day'])
        ->middleware(['auth', 'verified'])
        ->where('date', '\d{4}-\d{2}-\d{2}')
        ->name('admin.calendar.day');

    Route::post('/admin/calendar/kapasiteetti', [CalendarCapacityController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('admin.calendar.capacity.store');

    Route::delete('/admin/calendar/kapasiteetti/{override}', [CalendarCapacityController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('admin.calendar.capacity.destroy');

    Route::get('/admin/varaukset', [AdminBookingController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.index');

    Route::post('/admin/varaukset/{booking}/peruuta', [AdminBookingController::class, 'cancel'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.cancel');

    Route::patch('/admin/varaukset/lemmikit/{participant}/hoitojakso', [AdminBookingController::class, 'updateParticipantPeriod'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.participants.update-period');

    Route::post('/admin/varaukset/{booking}/merkitse-maksetuksi', [AdminBookingController::class, 'markDepositPaid'])
        ->middleware(['auth', 'verified'])
        ->name('admin.bookings.mark-deposit-paid');

    Route::get('/admin/palvelut', [ServiceSelectionController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('admin.services.index');

    Route::post('/admin/palvelut/{booking}/tallenna', [ServiceSelectionController::class, 'update'])
        ->middleware(['auth', 'verified'])
        ->name('admin.services.update');

    Route::get('/admin/raportit', [ReportController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('admin.reports.index');

    Route::get('/admin/laskutus', [InvoiceController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('admin.invoices.index');

    Route::post('/admin/laskutus/{booking}/tee-kuitti', [InvoiceController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('admin.invoices.store');

    Route::post('/admin/laskutus/{booking}/ohita', [InvoiceController::class, 'skip'])
        ->middleware(['auth', 'verified'])
        ->name('admin.invoices.skip');

});