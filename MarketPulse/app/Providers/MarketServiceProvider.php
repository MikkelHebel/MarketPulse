<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MarketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(StockStrategy::class, function () {
            return new StockStrategy(new client([
                'base_uri' => 'https://query1.finance.yahoo.com',
                'timeout' => 5.0,
            ]));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
