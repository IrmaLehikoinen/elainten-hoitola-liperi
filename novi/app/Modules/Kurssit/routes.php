<?php

use App\Modules\Kurssit\Http\Controllers\CourseController;
use App\Modules\Kurssit\Http\Controllers\CourseRegistrationController;
use App\Modules\Kurssit\Http\Controllers\InvoiceController;
use App\Modules\Kurssit\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {

    // Julkiset reitit — ei kirjautumista, kuka tahansa voi katsoa
    // kursseja ja ilmoittautua.
    Route::get('/kurssit', [CourseRegistrationController::class, 'index'])->name('kurssit.public.index');
    Route::get('/kurssit/{course}/ilmoittaudu', [CourseRegistrationController::class, 'show'])->name('kurssit.public.register');
    Route::post('/kurssit/{course}/ilmoittaudu', [CourseRegistrationController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('kurssit.public.store');
    Route::get('/kurssit/ilmoittautuminen/onnistui', function () {
        return view('kurssit::public.success');
    })->name('kurssit.public.success');
    Route::get('/kurssit/ilmoittautuminen/peruttu', function () {
        return view('kurssit::public.cancelled');
    })->name('kurssit.public.cancelled');

    // Hallintapaneeli — vaatii kirjautumisen.
    Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('/kurssit/hallinta', [CourseController::class, 'dashboard'])->name('kurssit.dashboard');

        Route::get('/kurssit/hallinta/kurssit', [CourseController::class, 'index'])->name('kurssit.courses.index');
        Route::get('/kurssit/hallinta/kurssit/uusi', [CourseController::class, 'create'])->name('kurssit.courses.create');
        Route::post('/kurssit/hallinta/kurssit', [CourseController::class, 'store'])->name('kurssit.courses.store');
        Route::get('/kurssit/hallinta/kurssit/{course}/muokkaa', [CourseController::class, 'edit'])->name('kurssit.courses.edit');
        Route::patch('/kurssit/hallinta/kurssit/{course}', [CourseController::class, 'update'])->name('kurssit.courses.update');
        Route::delete('/kurssit/hallinta/kurssit/{course}', [CourseController::class, 'destroy'])->name('kurssit.courses.destroy');

        Route::get('/kurssit/hallinta/raportti', [ReportController::class, 'index'])->name('kurssit.reports.index');

        Route::get('/kurssit/hallinta/laskutus', [InvoiceController::class, 'index'])->name('kurssit.invoices.index');
        Route::post('/kurssit/hallinta/laskutus/{registration}/merkitse-maksetuksi', [InvoiceController::class, 'markPaid'])->name('kurssit.invoices.mark-paid');
        Route::get('/kurssit/hallinta/laskutus/{registration}/pdf', [InvoiceController::class, 'downloadPdf'])->name('kurssit.invoices.pdf');
        Route::get('/kurssit/hallinta/laskutus/{registration}/tulosta', [InvoiceController::class, 'printPdf'])->name('kurssit.invoices.print');
    });

});