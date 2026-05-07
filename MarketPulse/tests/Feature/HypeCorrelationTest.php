<?php

use App\Models\Ticker;
use App\Models\Snapshot;
use App\Models\SentimentScore;
use App\Services\HypeCorrelationService;

test('returns the sentiment score when fewer than 2 snapshots exist', function () {
    $ticker = Ticker::create(['ticker' => 'AAPL']);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 70, 'timestamp' => now()]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 150.00, 'timestamp' => now()]);

    $hci = app(HypeCorrelationService::class)->calculate('AAPL');

    expect($hci)->toBe(70);
});

test('defaults to 50 when neither snapshots nor a sentiment score exist', function () {
    Ticker::create(['ticker' => 'MSFT']);

    $hci = app(HypeCorrelationService::class)->calculate('MSFT');

    expect($hci)->toBe(50);
});

test('calculates HCI correctly for a +2% price increase', function () {
    // priceMomentum = 50 + (2 * 5) = 60, sentiment = 60, HCI = (60 + 60) / 2 = 60
    $ticker = Ticker::create(['ticker' => 'NVDA']);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 60, 'timestamp' => now()->subMinutes(5)]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 100.00, 'timestamp' => now()->subMinutes(2)]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 102.00, 'timestamp' => now()]);

    expect(app(HypeCorrelationService::class)->calculate('NVDA'))->toBe(60);
});

test('calculates HCI correctly for a -2% price decrease', function () {
    // priceMomentum = 50 + (-2 * 5) = 40, sentiment = 40, HCI = (40 + 40) / 2 = 40
    $ticker = Ticker::create(['ticker' => 'TSLA']);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 40, 'timestamp' => now()->subMinutes(5)]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 100.00, 'timestamp' => now()->subMinutes(2)]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 98.00, 'timestamp' => now()]);

    expect(app(HypeCorrelationService::class)->calculate('TSLA'))->toBe(40);
});

test('clamps price momentum at 100 for a price spike beyond +10%', function () {
    // +20% move: priceMomentum capped at 100, sentiment = 100, HCI = 100
    $ticker = Ticker::create(['ticker' => 'GME']);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 100, 'timestamp' => now()->subMinutes(5)]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 100.00, 'timestamp' => now()->subMinutes(2)]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 120.00, 'timestamp' => now()]);

    expect(app(HypeCorrelationService::class)->calculate('GME'))->toBe(100);
});

test('clamps price momentum at 0 for a crash beyond -10%', function () {
    // -20% move: priceMomentum capped to 0, sentiment = 0, HCI = 0
    $ticker = Ticker::create(['ticker' => 'BBBY']);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 0, 'timestamp' => now()->subMinutes(5)]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 100.00, 'timestamp' => now()->subMinutes(2)]);
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 80.00, 'timestamp' => now()]);

    expect(app(HypeCorrelationService::class)->calculate('BBBY'))->toBe(0);
});
