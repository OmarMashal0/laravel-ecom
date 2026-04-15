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
