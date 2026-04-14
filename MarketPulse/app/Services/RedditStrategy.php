<?php

namespace App\Services;

use App\Services\Contracts\DataSourceInterface;
use GuzzleHttp\Client;

class RedditStrategy implements DataSourceInterface
{
    public function __construct(private Client $client) {}

    public function fetch(): array
    {
        $response = $this->client->get("/r/wallstreetbets/hot.json");
        return json_decode($response->getBody()->getContents(), true);
    }
}
