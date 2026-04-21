<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Services\StockStrategy;
use App\Services\RedditStrategy;
use App\Models\Ticker;
use App\Models\Snapshot;

#[Signature('app:fetch-data')]
#[Description('Fetch stock and Reddit data')]
class FetchData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(StockStrategy $stock, RedditStrategy $reddit): void
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

        $reddit->fetch();
    }
}
