<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // HANYA paksa HTTPS jika URL mengandung 'ngrok' atau 'render'
        // Jangan paksa HTTPS jika sedang di localhost
        if (str_contains(request()->url(), 'ngrok') || str_contains(request()->url(), 'onrender.com')) {
            URL::forceScheme('https');
        }
    }
}
