<?php

namespace App\Providers;

use App\Repositories\PackagesRepository;
use App\Repositories\RetailersRepository;
use App\Repositories\ShipmentsRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ShipmentsRepository::class, function ($app) {
            return new ShipmentsRepository();
        });

        $this->app->singleton(RetailersRepository::class, function ($app) {
            return new RetailersRepository();
        });

        $this->app->singleton(PackagesRepository::class, function ($app) {
            return new PackagesRepository();
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
