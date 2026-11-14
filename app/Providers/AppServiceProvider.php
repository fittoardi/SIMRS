<?php

namespace App\Providers;

use App\Integrations\WhatsApp\FonnteProvider;
use App\Integrations\WhatsApp\LogWhatsAppProvider;
use App\Integrations\WhatsApp\WhatsAppProvider;
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
        $this->app->singleton(WhatsAppProvider::class, function () {
            $driver = config('services.whatsapp.driver', 'log');
            $token = config('services.whatsapp.fonnte_token');

            if ($driver === 'fonnte' && ! empty($token)) {
                return new FonnteProvider($token, config('services.whatsapp.endpoint', 'https://api.fonnte.com/send'));
            }

            return new LogWhatsAppProvider;
        });
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
