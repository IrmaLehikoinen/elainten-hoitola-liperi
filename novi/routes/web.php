<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/tietosuoja', function () {
    return view('legal.tietosuoja', [
        'companyRecord' => \App\Models\Company::first(),
    ]);
})->name('legal.privacy');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

require __DIR__.'/auth.php';