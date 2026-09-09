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

Route::get('/tietosuoja/{company?}', function (?\App\Models\Company $company) {
    return view('legal.tietosuoja', [
        'companyRecord' => $company ?? \App\Models\Company::first(),
    ]);
})->name('legal.privacy');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

Route::post('/vaihda-yritys/{company}', function (\App\Models\Company $company, \Illuminate\Http\Request $request) {
    if (! app(\App\Services\ActiveCompanyResolver::class)->switchTo($company)) {
        abort(403);
    }

    $redirect = $request->input('redirect', $request->query('redirect'));

    if ($redirect && str_starts_with($redirect, '/')) {
        return redirect($redirect);
    }

    $routeName = config("industries.{$company->industry}.home_route", 'dashboard');

    return redirect()->route($routeName);
})->middleware('auth')->name('company.switch');

require __DIR__.'/auth.php';