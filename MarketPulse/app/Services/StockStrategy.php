<?php

namespace App\Services;

use App\Services\Contracts\DataSourceInterface;
use GuzzleHttp\Client;

class StockStrategy implements DataSourceInterface
{
    public function __construct(private Client $client) {}

    public function fetch(string $symbol): array
    {
        $response = $this->client->get("/v8/finance/chart/{$symbol}");
        return json_decode($response->getBody()->getContents(), true);
    }
}
