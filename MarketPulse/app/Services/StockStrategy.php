<?php

namespace App\Services;

use App\Services\Contracts\DataSourceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class StockStrategy implements DataSourceInterface
{
    private array $tickers = [
        '^GSPC', // S&P 500
        '^IXIC', // Nasdaq
        'AAPL', 'MSFT', 'GOOGL', 'NVDA', 'AMZN', 'META', 'TSLA', // MAG-7
    ];
    private string $interval = '1d';
    private string $range = '1d';

    public function __construct(private Client $client) {}

    public function fetch(): array
    {
        $results = [];
        foreach($this->tickers as $ticker) {
            try {
                $response = $this->client->get("/v8/finance/chart/{$ticker}", [
                    'query' => [
                        'interval' => $this->interval,
                        'range' => $this->range,
                    ]
                ]);
                $results[$ticker] = json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                $results[$ticker] = null;
            }
        }
        return $results;
    }
}
