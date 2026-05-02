<?php

namespace App\Services;

use App\Services\Contracts\DataSourceInterface;
use GuzzleHttp\Client;

class RedditScraperStrategy implements DataSourceInterface
{
    public function __construct(private Client $client) {}

    public function fetch(): array
    {
        $response = $this->client->get('/r/wallstreetbets/hot.json?limit=25&raw_json=1', [
            'headers' => [
                'User-Agent'      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'Accept'          => 'application/json, text/plain, */*',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Accept-Encoding' => 'identity',
                'Referer'         => 'https://www.reddit.com/',
            ],
            'decode_content' => false,
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
