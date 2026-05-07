<?php

use App\Models\Ticker;
use App\Models\Snapshot;
use App\Models\SentimentScore;
use App\Models\User;
use App\Models\UserThreshold;
use App\Notifications\HciAlertNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

test('fires a hype notification when HCI meets or exceeds the high threshold', function () {
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
    $user = User::factory()->create();
    $ticker = Ticker::create(['ticker' => 'MSFT']);
    UserThreshold::create(['user_id' => $user->id, 'ticker_id' => $ticker->id, 'hci_high' => 75, 'hci_low' => 25]);

    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 300.00, 'timestamp' => now()]);

    Notification::assertNothingSent();
});

test('does not fire a notification when HCI is within the safe zone', function () {
    // HCI = 50, thresholds are 75 high / 25 low, show no alert
    $user = User::factory()->create();
    $ticker = Ticker::create(['ticker' => 'GOOGL']);
    UserThreshold::create(['user_id' => $user->id, 'ticker_id' => $ticker->id, 'hci_high' => 75, 'hci_low' => 25]);
    SentimentScore::create(['ticker_id' => $ticker->id, 'score' => 50, 'timestamp' => now()]);

    Snapshot::create(['ticker_id' => $ticker->id, 'price' => 175.00, 'timestamp' => now()]);

    Notification::assertNothingSent();
});
