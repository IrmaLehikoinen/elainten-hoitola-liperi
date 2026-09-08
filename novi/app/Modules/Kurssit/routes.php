<?php

use App\Modules\Kurssit\Http\Controllers\CompanySettingsController;
use App\Modules\Kurssit\Http\Controllers\CourseCardController;
use App\Modules\Kurssit\Http\Controllers\CoursePaymentController;
use App\Modules\Kurssit\Http\Controllers\CourseController;
use App\Modules\Kurssit\Http\Controllers\CustomerDataController;
use App\Modules\Kurssit\Http\Controllers\CourseReminderController;
use App\Modules\Kurssit\Http\Controllers\GiftCardController;
use App\Modules\Kurssit\Http\Controllers\GiftCardPurchaseController;
use App\Modules\Kurssit\Http\Controllers\CourseRegistrationController;
use App\Modules\Kurssit\Http\Controllers\InvoiceController;
use App\Modules\Kurssit\Http\Controllers\RegistrationCardController;
use App\Modules\Kurssit\Http\Controllers\ReportController;
use App\Modules\Kurssit\Http\Controllers\SydanpolkuController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {

    // Julkiset reitit — ei kirjautumista, kuka tahansa voi katsoa
    // kursseja ja ilmoittautua.
        Route::get('/sydanpolku', [SydanpolkuController::class, 'index'])->name('sydanpolku.index');
    Route::get('/sydanpolku/tyohyvinvointipaivat', [SydanpolkuController::class, 'tyohyvinvointi'])->name('sydanpolku.tyohyvinvointi');
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
                Route::get('/kurssit/maksu/{registration:payment_token}', [CoursePaymentController::class, 'checkout'])->name('kurssit.payment.checkout');

    Route::get('/kurssit/lahjakortti/osta', [GiftCardPurchaseController::class, 'show'])->name('kurssit.public.gift-card.show');
        Route::post('/kurssit/lahjakortti/osta', [GiftCardPurchaseController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('kurssit.public.gift-card.store');
    Route::get('/kurssit/lahjakortti/onnistui', function () {
        return view('kurssit::public.gift-card-success');
    })->name('kurssit.public.gift-card.success');
    Route::get('/kurssit/lahjakortti/peruttu', function () {
        return view('kurssit::public.gift-card-cancelled');
    })->name('kurssit.public.gift-card.cancelled');
    Route::get('/kurssit/lahjakortti/kortti/{giftCard:share_token}', [GiftCardPurchaseController::class, 'card'])->name('kurssit.public.gift-card.card');
    Route::get('/kurssit/lahjakortti/kortti/{giftCard:share_token}/pdf', [GiftCardPurchaseController::class, 'cardPdf'])->name('kurssit.public.gift-card.card-pdf');

    // Hallintapaneeli — vaatii kirjautumisen.
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/kurssit/hallinta', [CourseController::class, 'dashboard'])->name('kurssit.dashboard');

        Route::get('/kurssit/hallinta/kurssit', [CourseController::class, 'index'])->name('kurssit.courses.index');
        Route::get('/kurssit/hallinta/kurssit/uusi', [CourseController::class, 'create'])->name('kurssit.courses.create');
        Route::post('/kurssit/hallinta/kurssit', [CourseController::class, 'store'])->name('kurssit.courses.store');
        Route::get('/kurssit/hallinta/kurssit/{course}/muokkaa', [CourseController::class, 'edit'])->name('kurssit.courses.edit');
        Route::patch('/kurssit/hallinta/kurssit/{course}', [CourseController::class, 'update'])->name('kurssit.courses.update');
        Route::delete('/kurssit/hallinta/kurssit/{course}', [CourseController::class, 'destroy'])->name('kurssit.courses.destroy');
        Route::post('/kurssit/hallinta/kurssit/{course}/kopioi', [CourseController::class, 'duplicate'])->name('kurssit.courses.duplicate');
        Route::post('/kurssit/hallinta/kurssit/{course}/sulje-ilmoittautuminen', [CourseController::class, 'toggleRegistrationClosed'])->name('kurssit.courses.toggle-registration');
        Route::post('/kurssit/hallinta/kurssit/{course}/peruuta', [CourseController::class, 'toggleCancelled'])->name('kurssit.courses.toggle-cancelled');

        Route::get('/kurssit/hallinta/raportti', [ReportController::class, 'index'])->name('kurssit.reports.index');

        Route::get('/kurssit/hallinta/laskutus', [InvoiceController::class, 'index'])->name('kurssit.invoices.index');
                Route::post('/kurssit/hallinta/laskutus/{registration}/merkitse-maksetuksi', [InvoiceController::class, 'markPaid'])->name('kurssit.invoices.mark-paid');
        Route::post('/kurssit/hallinta/laskutus/{registration}/merkitse-palautetuksi', [InvoiceController::class, 'markRefunded'])->name('kurssit.invoices.mark-refunded');
        Route::get('/kurssit/hallinta/laskutus/{registration}/pdf', [InvoiceController::class, 'downloadPdf'])->name('kurssit.invoices.pdf');
        Route::get('/kurssit/hallinta/laskutus/{registration}/tulosta', [InvoiceController::class, 'printPdf'])->name('kurssit.invoices.print');

                 Route::get('/kurssit/hallinta/asetukset', [CompanySettingsController::class, 'index'])->name('kurssit.settings.index');
        Route::post('/kurssit/hallinta/asetukset/yritystiedot', [CompanySettingsController::class, 'updateCompanyInfo'])->name('kurssit.settings.company-info.update');

                Route::post('/kurssit/hallinta/muistutukset', [CourseReminderController::class, 'store'])->name('kurssit.reminders.store');
        Route::post('/kurssit/hallinta/muistutukset/{reminder}/vaihda', [CourseReminderController::class, 'toggle'])->name('kurssit.reminders.toggle');
        Route::delete('/kurssit/hallinta/muistutukset/{reminder}', [CourseReminderController::class, 'destroy'])->name('kurssit.reminders.destroy');

        Route::get('/kurssit/hallinta/kurssikortit', [CourseCardController::class, 'index'])->name('kurssit.cards.index');
        Route::get('/kurssit/hallinta/kurssikortit/{course}', [CourseCardController::class, 'show'])->name('kurssit.cards.show');
        Route::post('/kurssit/hallinta/kurssikortit/{course}/osallistuja', [CourseCardController::class, 'storeRegistration'])->name('kurssit.cards.store-registration');

                Route::get('/kurssit/hallinta/osallistuja/{registration}', [RegistrationCardController::class, 'show'])->name('kurssit.registrations.show');
        Route::post('/kurssit/hallinta/osallistuja/{registration}/peru', [RegistrationCardController::class, 'cancel'])->name('kurssit.registrations.cancel');

                Route::get('/kurssit/hallinta/lahjakortit', [GiftCardController::class, 'index'])->name('kurssit.gift-cards.index');
        Route::post('/kurssit/hallinta/lahjakortit', [GiftCardController::class, 'store'])->name('kurssit.gift-cards.store');
                Route::post('/kurssit/hallinta/lahjakortit/asetukset', [GiftCardController::class, 'updateSettings'])->name('kurssit.gift-cards.update-settings');
                        Route::get('/kurssit/hallinta/asiakastiedot', [CustomerDataController::class, 'index'])->name('kurssit.customer-data.index');

        });

});