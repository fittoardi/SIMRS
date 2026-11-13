<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Rate limiter untuk pendaftaran antrean & booking online (cegah spam bot)
        RateLimiter::for('booking', function (Request $request) {
            return Limit::perMinute(15)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter untuk Kiosk check-in dan pencarian No. RM (cegah brute-force NIK)
        RateLimiter::for('checkin', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
