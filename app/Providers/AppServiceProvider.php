<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($appUrl = env('APP_URL')) {
            URL::forceRootUrl($appUrl);
            if (str_starts_with($appUrl, 'https')) {
                URL::forceScheme('https');
            }
        }
    }
}