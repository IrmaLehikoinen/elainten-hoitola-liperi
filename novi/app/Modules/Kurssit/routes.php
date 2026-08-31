<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {

    Route::get('/kurssit/hallinta', function () {
            return view('kurssit::dashboard');
    })->middleware(['auth', 'verified'])->name('kurssit.dashboard');

});