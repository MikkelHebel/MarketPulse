<?php

namespace App\Observers;

use App\Models\Snapshot;
use App\Models\UserThreshold;
use App\Services\HypeCorrelationService;

class SnapshotObserver
{
    /**
     * Handle the Snapshot "created" event.
     */
    public function created(Snapshot $snapshot): void
    {
        $ticker = $snapshot->ticker->ticker;
        $hci = app(HypeCorrelationService::class)->calculate($ticker);

        $thresholds = UserThreshold::whereHas('ticker', fn($q) => $q->where('ticker', $ticker))->get();

        foreach ($thresholds as $threshold) {
            if ($hci >= $threshold->hci_high) {

            } elseif ($hci <= $threshold->hci_low) {

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
