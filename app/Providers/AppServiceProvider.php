<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class  AppServiceProvider extends ServiceProvider
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
        Passport::tokensExpireIn(Carbon::now()->addMinutes(5));
        Passport::refreshTokensExpireIn(Carbon::now()->addMinutes(7));
        Passport::personalAccessTokensExpireIn(Carbon::now()->addHours(1));
        Passport::enablePasswordGrant();
    }
}
