<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\StockStrategy;
use App\Services\RedditStrategy;
use GuzzleHttp\Client;

class MarketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(StockStrategy::class, function () {
            return new StockStrategy(new Client([
                'base_uri' => 'https://query1.finance.yahoo.com',
                'timeout' => 5.0,
            ]));
        });

        $this->app->bind(RedditStrategy::class, function () {
            return new RedditStrategy(new Client([
                'base_uri' => 'https://reddit.com',
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
