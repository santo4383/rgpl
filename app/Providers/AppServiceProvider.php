<?php

namespace App\Providers;

use App\Services\SMS\SmsManager;
use App\Services\SMS\SmsProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('sms', function ($app) {
            return new SmsManager($app);
        });

        $this->app->bind(SmsProviderInterface::class, function ($app) {
            return $app->make('sms')->driver();
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
