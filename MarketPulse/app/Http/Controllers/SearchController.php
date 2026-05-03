<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Client;
use App\Models\Ticker;
use App\Models\RecentSearch;
use App\Services\SingleStockStrategy;

class SearchController extends Controller
{
    public function __construct(private Client $client) {}

    public function show(Request $request)
    {
        $request->validate([
            'ticker' => 'required|string|max:10|alpha_dash',
        ]);

        $ticker = strtoupper(trim($request->ticker));
        $tickerModel = Ticker::where('ticker', $ticker)->first();
        if ($tickerModel) {
            RecentSearch::create(['ticker_id' => $tickerModel->id]);
        }

        return view('search', ['ticker' => $ticker]);
    }

    public function data(Request $request): JsonResponse
    {
        $request->validate([
            'ticker' => 'required|string|max:10|alpha_dash',
        ]);

        $ticker = strtoupper(trim($request->validated()['ticker']));
        $tickerModel = Ticker::with([
            'snapshots' => fn($q) => $q->latest('timestamp')->limit(60),
            'sentimentScores' => fn($q) => $q->latest('timestamp')->limit(1),
        ])->where('ticker', $ticker)->first();


        if ($tickerModel) {
            return response()->json([
                'ticker' => $tickerModel->ticker,
                'snapshots' => $tickerModel->snapshots,
                'sentiment' => $tickerModel->sentimentScores->first()?->score,
            ]);
        } else {
            $strategy = new SingleStockStrategy($this->client, $ticker);
            $data = $strategy->fetch();

            $result = $data[$ticker]['chart']['result'][0] ?? null;
            $snapshots = [];

            if ($result) {
                $timestamps = $result['timestamp'] ?? [];
                $closes = $result['indicators']['quote'][0]['close'] ?? [];
                foreach ($timestamps as $i => $ts) {
                    $snapshots[] = ['price' => $closes[$i], 'timestamp' => date('Y-m-d H:i:s', $ts)];
                }
            }

            return response()->json([
                'ticker' => $ticker,
                'snapshots' => $snapshots,
                'sentiment' => null,
            ]);
        }
    }
}
