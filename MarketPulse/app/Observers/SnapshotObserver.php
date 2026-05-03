<?php

namespace App\Observers;

use App\Models\Snapshot;
use App\Models\SentimentScore;
use App\Models\UserThreshold;
use App\Services\HypeCorrelationService;
use App\Notifications\HciAlertNotification;

class SnapshotObserver
{
    /**
     * Handle the Snapshot "created" event.
     */
    public function created(Snapshot $snapshot): void
    {
        if (!SentimentScore::where('ticker_id', $snapshot->ticker_id)->exists()) {
            return;
        }

        $ticker = $snapshot->ticker->ticker;
        $hci = app(HypeCorrelationService::class)->calculate($ticker);

        $thresholds = UserThreshold::whereHas('ticker', fn($q) => $q->where('ticker', $ticker))->get();

        foreach ($thresholds as $threshold) {
            if ($threshold->hci_high !== null && $threshold->hci_high > 0 && $hci >= $threshold->hci_high) {
                $threshold->user->notify(new HciAlertNotification($ticker, $hci, 'hype'));
            } elseif ($threshold->hci_low !== null && $hci <= $threshold->hci_low) {
                $threshold->user->notify(new HciAlertNotification($ticker, $hci, 'crash'));
            }
        }
    }

    /**
     * Handle the Snapshot "updated" event.
     */
    public function updated(Snapshot $snapshot): void
    {
        //
    }

    /**
     * Handle the Snapshot "deleted" event.
     */
    public function deleted(Snapshot $snapshot): void
    {
        //
    }

    /**
     * Handle the Snapshot "restored" event.
     */
    public function restored(Snapshot $snapshot): void
    {
        //
    }

    /**
     * Handle the Snapshot "force deleted" event.
     */
    public function forceDeleted(Snapshot $snapshot): void
    {
        //
    }
}
