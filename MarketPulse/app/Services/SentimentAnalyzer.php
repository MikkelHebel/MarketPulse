<?php

namespace App\Services;

class SentimentAnalyzer
{
    private array $positive = ['bullish', 'bull', 'moon', 'value', 'mooning', 'brrr', 'buy', 'stonks', 'call', 'calls', 'long', 'rocket', 'yolo'];
    private array $negative = ['bearish', 'bear', 'guh', 'puts', 'short', 'crash', 'down', 'dump', 'sell', 'rekt', 'bankrupt'];
    private array $tickers = ['AAPL', 'MSFT', 'GOOGL', 'NVDA', 'AMZN', 'META', 'TSLA'];

    public function analyze(array $posts): array
    {
        $children = $posts['data']['children'] ?? [];
        $hits = [];

        foreach ($children as $child)
        {
            $title = strtolower($child['data']['title'] ?? '');
            // check for negative and positive words in titles
            foreach ($this->tickers as $ticker) {
                if (str_contains($title, strtolower($ticker))) {
                    if (!isset($hits[$ticker])) {
                        $hits[$ticker] = ['pos' => 0, 'neg' => 0];
                    }
                    foreach ($this->positive as $word) {
                        if (str_contains($title, $word)) {
                            $hits[$ticker]['pos']++;
                        }
                    }
                    foreach ($this->negative as $word) {
                        if (str_contains($title, $word)) {
                            $hits[$ticker]['neg']++;
                        }
                    }
                }
            }
        }

        return collect($hits)->map(function ($counts) {
            $total = $counts['pos'] + $counts['neg'];
            return $total > 0 ? (int) round(($counts['pos'] / $total) * 100) : 50;
        })->all();
    }
}
