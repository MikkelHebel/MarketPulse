<?php

namespace App\Services;

use App\Services\Contracts\DataSourceInterface;
use GuzzleHttp\Client;
class StockStrategy implements DataSourceInterface
{
    private array $tickers = [
        '^GSPC', // S&P 500
        '^IXIC', // Nasdaq
        'AAPL', 'MSFT', 'GOOGL', 'NVDA', 'AMZN', 'META', 'TSLA', // MAG-7
    ];

    public function __construct(private Client $client) {}

    public function fetch(): array
    {
        $results = [];
        foreach($this->tickers as $ticker) {
            $response = $this->client->get("/v8/finance/chart/{$ticker}");
            $results[$ticker] = json_decode($response->getBody()->getContents(), true);
        }
        return $results;
    }
}
