<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:cancel-expired')->dailyAt('20:00');
Schedule::command('bookings:send-payment-reminders')->dailyAt('09:00');
Schedule::command('kurssit:send-reminders')->everyFifteenMinutes();
Schedule::command('kurssit:cancel-expired')->everyFiveMinutes();
Schedule::command('kurssit:anonymize-old-data')->daily();
Schedule::command('ajanvaraus:send-group-warnings')->daily();