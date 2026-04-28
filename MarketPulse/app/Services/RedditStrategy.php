<?php

namespace App\Services;

use App\Services\Contracts\DataSourceInterface;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class RedditStrategy implements DataSourceInterface
{
    public function __construct(private Client $client) {}

    public function fetch(): array
    {
        $token = $this->getAccessToken();

        $response = $this->client->get("/r/wallstreetbets/hot.json", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'User-Agent' => env('REDDIT_USER_AGENT'),
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    private function getAccessToken(): string
    {
        return Cache::remember('reddit_token', 3500, function () {
            $response = $this->client->post('https://www.reddit.com/api/v1/access_token', [
               'auth'        => [env('REDDIT_CLIENT_ID'), env('REDDIT_CLIENT_SECRET')],
               'form_params' => ['grant_type' => 'client_credentials'],
               'headers'     => ['User-Agent' => env('REDDIT_USER_AGENT')],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'];
        });
    }

}
