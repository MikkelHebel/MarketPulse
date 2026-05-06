<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Ticker;
use App\Models\Snapshot;

class StockHistoryService
{
    public function __construct(private Client $client) {}

    public function getHistoryIfEmpty(Ticker $ticker): void
    {
        if ($ticker->snapshots()->exists()) return;

        $strategy = new SingleStockStrategy($this->client, $ticker->ticker);
        $data     = $strategy->fetch();
        $result   = $data[$ticker->ticker]['chart']['result'][0] ?? null;

        if (!$result) return;

        $timestamps = $result['timestamp'] ?? [];
        $closes     = $result['indicators']['quote'][0]['close'] ?? [];

        $rows = [];
        foreach ($timestamps as $i => $ts) {
            if (isset($closes[$i]) && $closes[$i] !== null) {
                $rows[] = [
                    'ticker_id' => $ticker->id,
                    'price'     => $closes[$i],
                    'timestamp' => date('Y-m-d H:i:s', $ts),
                ];
            }
        }

        if (!empty($rows)) {
            Snapshot::insert($rows);
        }
    }
}
