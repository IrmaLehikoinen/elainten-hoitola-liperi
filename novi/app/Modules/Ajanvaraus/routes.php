<?php

use App\Modules\Ajanvaraus\Http\Controllers\CalendarController;
use App\Modules\Ajanvaraus\Http\Controllers\PublicBookingController;
use App\Modules\Ajanvaraus\Http\Controllers\TreatmentAvailabilityRuleController;
use App\Modules\Ajanvaraus\Http\Controllers\TreatmentCategoryController;
use App\Modules\Ajanvaraus\Http\Controllers\TreatmentController;
use App\Modules\Ajanvaraus\Http\Controllers\TreatmentSpecialOpeningController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
        Route::get('/ajanvaraus/varaa', [PublicBookingController::class, 'index'])->name('ajanvaraus.public.book');
    Route::post('/ajanvaraus/varaa', [PublicBookingController::class, 'store'])->name('ajanvaraus.public.store');
    Route::get('/ajanvaraus/varaa/kiitos/{appointment}', [PublicBookingController::class, 'success'])->name('ajanvaraus.public.success');
});

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/ajanvaraus/hallinta', [CalendarController::class, 'index'])->name('ajanvaraus.dashboard');
    Route::get('/ajanvaraus/hallinta/paiva', [CalendarController::class, 'day'])->name('ajanvaraus.calendar.day');

    Route::get('/ajanvaraus/hallinta/hoidot', [TreatmentController::class, 'index'])->name('ajanvaraus.treatments.index');
    Route::get('/ajanvaraus/hallinta/hoidot/uusi', [TreatmentController::class, 'create'])->name('ajanvaraus.treatments.create');
    Route::post('/ajanvaraus/hallinta/hoidot', [TreatmentController::class, 'store'])->name('ajanvaraus.treatments.store');
    Route::get('/ajanvaraus/hallinta/hoidot/{treatment}/muokkaa', [TreatmentController::class, 'edit'])->name('ajanvaraus.treatments.edit');
    Route::patch('/ajanvaraus/hallinta/hoidot/{treatment}', [TreatmentController::class, 'update'])->name('ajanvaraus.treatments.update');
    Route::delete('/ajanvaraus/hallinta/hoidot/{treatment}', [TreatmentController::class, 'destroy'])->name('ajanvaraus.treatments.destroy');

    Route::post('/ajanvaraus/hallinta/hoidot/{treatment}/aikataulu', [TreatmentAvailabilityRuleController::class, 'store'])->name('ajanvaraus.rules.store');
    Route::delete('/ajanvaraus/hallinta/aikataulu/{rule}', [TreatmentAvailabilityRuleController::class, 'destroy'])->name('ajanvaraus.rules.destroy');

        Route::post('/ajanvaraus/hallinta/hoidot/{treatment}/avaus', [TreatmentSpecialOpeningController::class, 'store'])->name('ajanvaraus.openings.store');
    Route::post('/ajanvaraus/hallinta/kalenteri/avaus', [TreatmentSpecialOpeningController::class, 'storeForAny'])->name('ajanvaraus.openings.store-any');
    Route::delete('/ajanvaraus/hallinta/avaus/{opening}', [TreatmentSpecialOpeningController::class, 'destroy'])->name('ajanvaraus.openings.destroy');

    Route::post('/ajanvaraus/hallinta/otsikot', [TreatmentCategoryController::class, 'store'])->name('ajanvaraus.categories.store');
    Route::patch('/ajanvaraus/hallinta/otsikot/{category}', [TreatmentCategoryController::class, 'update'])->name('ajanvaraus.categories.update');
    Route::delete('/ajanvaraus/hallinta/otsikot/{category}', [TreatmentCategoryController::class, 'destroy'])->name('ajanvaraus.categories.destroy');
});