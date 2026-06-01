<?php

use App\Jobs\FetchNewOffersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new FetchNewOffersJob)->dailyAt('06:00');
Schedule::job(new FetchNewOffersJob)->dailyAt('09:00');
Schedule::job(new FetchNewOffersJob)->dailyAt('12:00');
Schedule::job(new FetchNewOffersJob)->dailyAt('18:00');
