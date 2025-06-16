<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Providers\CustomUserProvider;

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
        Auth::provider('eloquent.custom', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });

        Gate::define('is-admin', function ($user) {
            return $user->use_role === 'admin';
        });
    }
}
