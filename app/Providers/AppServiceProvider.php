<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Force HTTPS in production (Render proxies SSL, app sees HTTP internally)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Custom registration response to fix admin redirect issue
        $this->app->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            fn () => new class implements \Laravel\Fortify\Contracts\RegisterResponse {
                public function toResponse($request) {
                    return redirect('/my-account');
                }
            }
        );
    }
}
