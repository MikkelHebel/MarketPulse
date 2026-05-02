<?php

namespace App\Services;

use App\Services\Contracts\DataSourceInterface;
use GuzzleHttp\Client;

class RedditScraperStrategy implements DataSourceInterface
{
    public function __construct(private Client $client) {}

    public function fetch(): array
    {
        $response = $this->client->get('/r/wallstreetbets/hot.json', [
            'headers' => [
                'User-Agent' => env('REDDIT_USER_AGENT', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36'),
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
