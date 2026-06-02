<?php

namespace App\Providers;

use App\AI\OpenAiApi;
use App\Contracts\AiApiInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AiApiInterface::class, OpenAiApi::class);
    }

    public function boot(): void {}
}
