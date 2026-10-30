<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Di production aplikasi berada di balik proxy HTTPS milik hosting,
        // sehingga semua URL (link, form action, redirect) harus memakai https.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}