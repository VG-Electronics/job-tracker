<?php

namespace App\Providers;

use App\AI\OpenAiApi;
use App\Contracts\AiApiInterface;
use App\Contracts\ScrapperInterface;
use App\Scrappers\HttpScrapper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AiApiInterface::class, OpenAiApi::class);
        $this->app->bind(ScrapperInterface::class, HttpScrapper::class);
    }

    public function boot(): void {}
}
