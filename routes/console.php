<?php

use App\Jobs\FetchNewOffersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

foreach (['06:00', '09:00', '12:00', '14:00', '18:00'] as $time) {
    Schedule::job(new FetchNewOffersJob)
        ->dailyAt($time)
        ->name('fetch-new-offers')
        ->withoutOverlapping(180);
}
