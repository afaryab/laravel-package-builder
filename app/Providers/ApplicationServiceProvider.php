<?php

namespace LaravelApp\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelApp\Services\ApplicationConfigService;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ApplicationConfigService::class, function ($app) {
            return new ApplicationConfigService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
