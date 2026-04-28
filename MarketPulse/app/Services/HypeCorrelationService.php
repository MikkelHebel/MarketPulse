<?php

namespace App\Services;

use App\Models\Snapshot;
use App\Models\SentimentScore;

class HypeCorrelationService
{
    public function calculate(string $ticker): int
    {
        $sentimentScore = SentimentScore::WhereHas('ticker', fn($q) => $q->where('ticker', $ticker))->latest('timestamp')->value('score') ?? 50;
        $snapshots = Snapshot::WhereHas('ticker', fn($q) => $q->where('ticker', $ticker))->latest('timestamp')->limit(2)->get();

        if ($snapshots->count() < 2) {
            return $sentimentScore;
        }

        $currentPrice = $snapshots->first()->price;
        $previousPrice = $snapshots->last()->price;

        $priceChange = ($currentPrice - $previousPrice) / $previousPrice * 100;
        $priceMomentum = min(100, max(0, 50 + ($priceChange * 5)));

        return (int) round(($sentimentScore + $priceMomentum) / 2);
    }
}
