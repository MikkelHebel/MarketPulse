<?php

use App\Services\SentimentAnalyzer;

test('returns empty array when posts list is empty', function () {
    $analyzer = new SentimentAnalyzer();

    expect($analyzer->analyze(['data' => ['children' => []]]))->toBeEmpty();
});

test('returns empty array when the data key is missing', function () {
    $analyzer = new SentimentAnalyzer();

    expect($analyzer->analyze([]))->toBeEmpty();
});

test('returns 50 when a known ticker is mentioned but no sentiment words appear', function () {
    $analyzer = new SentimentAnalyzer();
    $posts = makePosts(['AAPL is doing interesting things today']);

    $result = $analyzer->analyze($posts);

    expect($result)->toHaveKey('AAPL')->and($result['AAPL'])->toBe(50);
});

test('scores 100 when only bullish words are present', function () {
    $analyzer = new SentimentAnalyzer();
    $posts = makePosts(['AAPL is bullish moon rocket buy calls yolo']);

    expect($analyzer->analyze($posts)['AAPL'])->toBe(100);
});

test('scores 0 when only bearish words are present', function () {
    $analyzer = new SentimentAnalyzer();
    $posts = makePosts(['TSLA bearish puts crash sell dump rekt']);

    expect($analyzer->analyze($posts)['TSLA'])->toBe(0);
});

test('scores 50 for perfectly balanced sentiment', function () {
    $analyzer = new SentimentAnalyzer();
    $posts = makePosts(['NVDA bull bear']);

    expect($analyzer->analyze($posts)['NVDA'])->toBe(50);
});

test('ignores posts that mention no known ticker', function () {
    $analyzer = new SentimentAnalyzer();
    $posts = makePosts(['SPY puts crash sell']);

    expect($analyzer->analyze($posts))->toBeEmpty();
});

test('handles multiple different tickers across posts independently', function () {
    $analyzer = new SentimentAnalyzer();
    $posts = makePosts([
        'AAPL moon rocket buy',
        'MSFT crash dump sell',
    ]);

    $result = $analyzer->analyze($posts);

    expect($result['AAPL'])->toBe(100)->and($result['MSFT'])->toBe(0);
});

test('ticker matching is case-insensitive', function () {
    $analyzer = new SentimentAnalyzer();
    $posts = makePosts(['aapl is going to the moon']);

    expect($analyzer->analyze($posts))->toHaveKey('AAPL');
});

// Helper to build the nested post structure the analyzer expects
function makePosts(array $titles): array
{
    return [
        'data' => [
            'children' => array_map(
                fn($title) => ['data' => ['title' => $title]],
                $titles
            ),
        ],
    ];
}
