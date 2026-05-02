<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Services\StockStrategy;
use App\Services\RedditStrategy;
use App\Services\SentimentAnalyzer;
use App\Models\Ticker;
use App\Models\Snapshot;
use App\Models\SentimentScore;

#[Signature('app:fetch-data')]
#[Description('Fetch stock and Reddit data')]
class FetchData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(StockStrategy $stock, RedditStrategy $reddit, SentimentAnalyzer $analyzer): void
    {
        $results = $stock->fetch();

        foreach($results as $symbol => $data) {
            if ($data === null) continue;

            $price = $data['chart']['result'][0]['meta']['regularMarketPrice'] ?? null;

            if ($price === null) continue;

            $ticker = Ticker::firstOrCreate(['ticker' => $symbol]);

            Snapshot::create([
                'ticker_id' => $ticker->id,
                'price' => $price,
                'timestamp' => now(),
            ]);
        }

        try {
            $posts = $reddit->fetch();
            $scores = $analyzer->analyze($posts);

            foreach($scores as $symbol => $score) {
                $ticker = Ticker::firstOrCreate(['ticker' => $symbol]);

                SentimentScore::create([
                    'ticker_id' => $ticker->id,
                    'score'     => $score,
                    'timestamp' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::warning('Reddit fetch failed: ' . $e->getMessage());
        }
    }
}
