<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\CustomreBrevoData;
use App\Observers\CustomerObserver;
use App\Observers\CustomerBrevoDataObserver;
use App\Services\CalendarManager;
use App\Services\GoogleProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GoogleProvider::class, function ($app) {
            return new GoogleProvider(
                request(),  // Request object
                config('services.google.client_id'),  // clientId
                config('services.google.client_secret'),  // clientSecret
                config('services.google.redirect_uri'),
                config('services.google.scopes'),  // redirectUrl
                  // scopes (if any)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Customer::observe(CustomerObserver::class);
        CustomreBrevoData::observe(CustomerBrevoDataObserver::class);
    }
}
