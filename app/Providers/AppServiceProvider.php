<?php

namespace App\Providers;

use App\Services\GeminiUsage;
use Illuminate\Support\Facades\View;
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
        // Make Gemini usage available to the navbar in the app layout.
        View::composer('layouts.app', function ($view) {
            $view->with('geminiUsage', app(GeminiUsage::class)->snapshot());
        });
    }
}
