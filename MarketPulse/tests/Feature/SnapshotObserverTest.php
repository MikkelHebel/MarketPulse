<?php

use App\Models\Ticker;
use App\Models\Snapshot;
use App\Models\SentimentScore;
use App\Models\User;
use App\Models\UserThreshold;
use App\Notifications\HciAlertNotification;
use Illuminate\Support\Facades\Notification;

test('fires a hype notification when HCI meets or exceeds the high threshold', function () {
    Notification::fake();
    // Sentiment 80 single snapshot HCI = 80 (hci_high set to 75)
    $user = User::factory()->create();
    $ticker = Ticker::create(['ticker' => 'AAPL']);
    UserThreshold::create(['user_id' => $user->id, 'ticker_id' => $ticker->id, 'hci_high' => 75, 'hci_low' => null]);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 80, 'timestamp' => now()]);

    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 150.00, 'timestamp' => now()]);

    Notification::assertSentTo($user, HciAlertNotification::class, function ($notification) use ($user) {
        return $notification->toArray($user)['type'] === 'hype';
    });
});

test('fires a crash notification when HCI falls at or below the low threshold', function () {
    Notification::fake();
    // Sentiment 20 single snapshot HCI = 20 (hci_low set to 25)
    $user = User::factory()->create();
    $ticker = Ticker::create(['ticker' => 'TSLA']);
    UserThreshold::create(['user_id' => $user->id, 'ticker_id' => $ticker->id, 'hci_high' => null, 'hci_low' => 25]);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 20, 'timestamp' => now()]);

    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 100.00, 'timestamp' => now()]);

    Notification::assertSentTo($user, HciAlertNotification::class, function ($notification) use ($user) {
        return $notification->toArray($user)['type'] === 'crash';
    });
});

test('does not fire a notification when no sentiment score exists for the ticker', function () {
    Notification::fake();
    $user = User::factory()->create();
    $ticker = Ticker::create(['ticker' => 'MSFT']);
    UserThreshold::create(['user_id' => $user->id, 'ticker_id' => $ticker->id, 'hci_high' => 75, 'hci_low' => 25]);

    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 300.00, 'timestamp' => now()]);

    Notification::assertNothingSent();
});

test('does not re-fire a hype notification when HCI is already in the hype zone', function () {
    $user = User::factory()->create();
    $ticker = Ticker::create(['ticker' => 'NVDA']);
    UserThreshold::create(['user_id' => $user->id, 'ticker_id' => $ticker->id, 'hci_high' => 75, 'hci_low' => null]);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 80, 'timestamp' => now()]);

    // First snapshot crosses into hype zone — notification persisted to DB
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 150.00, 'timestamp' => now()]);
    expect($user->fresh()->notifications()->count())->toBe(1);

    // Second snapshot while still in hype zone — should not fire again
    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 151.00, 'timestamp' => now()]);
    expect($user->fresh()->notifications()->count())->toBe(1);
});

test('does not fire a notification when HCI is within the safe zone', function () {
    Notification::fake();
    // HCI = 50, thresholds are 75 high / 25 low, show no alert
    $user = User::factory()->create();
    $ticker = Ticker::create(['ticker' => 'GOOGL']);
    UserThreshold::create(['user_id' => $user->id, 'ticker_id' => $ticker->id, 'hci_high' => 75, 'hci_low' => 25]);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 50, 'timestamp' => now()]);

    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 175.00, 'timestamp' => now()]);

    Notification::assertNothingSent();
});
