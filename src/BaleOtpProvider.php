<?php

namespace ErfanKatebSaber\BaleOtp;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class BaleOtpProvider extends ServiceProvider
{
    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bale-otp.php', 'bale-otp');


        $this->app->singleton(BaleOtp::class, function (Application $app) {
            $config = $app->make('config')->get('bale-otp', []);
            return new BaleOtp($config);
        });
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        // Config
        $this->publishes([
            __DIR__.'/../config/bale-otp.php' => config_path('bale-otp.php'),
        ], 'bale-otp');
    }
}
