<?php

use Illuminate\Support\Facades\Route;

// Väliaikainen testireitti — vahvistaa että moduuli on kytketty oikein.
// Poistetaan/korvataan kun oikeat hallintapaneelin reitit rakennetaan.
Route::get('/ajanvaraus/testi', function () {
    return 'Ajanvaraus-moduuli on asennettu ja toimii.';
})->name('ajanvaraus.testi');