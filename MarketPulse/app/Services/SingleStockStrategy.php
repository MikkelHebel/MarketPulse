<?php

namespace App\Services;

use App\Services\Contracts\DataSourceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SingleStockStrategy implements DataSourceInterface
{
    private string $interval = '5m';
    private string $range = '5d';

    public function __construct(private Client $client, private string $ticker) {}

    public function fetch(): array
    {
        $results = [];
        try {
            $response = $this->client->get("/v8/finance/chart/{$this->ticker}", [
                'query' => [
                    'interval' => $this->interval,
                    'range' => $this->range,
                ]
            ]);
            $results[$this->ticker] = json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $results[$this->ticker] = null;
        }
        return $results;
    }
}
