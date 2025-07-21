<?php

namespace LaravelApp\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use LaravelApp\Services\ApplicationConfigService;
use LaravelApp\Models\User;

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
        // Custom Blade directive for permission checking
        Blade::if('permission', function ($permission) {
            if (!Auth::check()) return false;
            /** @var User $user */
            $user = Auth::user();
            return $user->hasPermission($permission);
        });

        // Custom Blade directive for role checking
        Blade::if('role', function ($role) {
            if (!Auth::check()) return false;
            /** @var User $user */
            $user = Auth::user();
            return $user->hasRole($role);
        });

        // Custom Blade directive for multiple permission checking (any)
        Blade::if('anyPermission', function (...$permissions) {
            if (!Auth::check()) return false;
            /** @var User $user */
            $user = Auth::user();
            return $user->hasAnyPermission($permissions);
        });

        // Custom Blade directive for multiple permission checking (all)
        Blade::if('allPermissions', function (...$permissions) {
            if (!Auth::check()) return false;
            /** @var User $user */
            $user = Auth::user();
            return $user->hasAllPermissions($permissions);
        });
    }
}
