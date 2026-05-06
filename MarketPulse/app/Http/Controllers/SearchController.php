<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Ticker;
use App\Models\RecentSearch;
use App\Services\StockHistoryService;

class SearchController extends Controller
{
    public function __construct(private StockHistoryService $history) {}

    public function show(Request $request)
    {
        $request->validate([
            'ticker' => 'required|string|max:10|alpha_dash',
        ]);

        $ticker = strtoupper(trim($request->ticker));
        $tickerModel = Ticker::firstOrCreate(['ticker' => $ticker]);
        RecentSearch::create(['ticker_id' => $tickerModel->id]);

        return view('search', ['ticker' => $ticker]);
    }

    public function data(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticker' => 'required|string|max:10|alpha_dash',
        ]);

        $ticker = strtoupper(trim($validated['ticker']));
        $tickerModel = Ticker::firstOrCreate(['ticker' => $ticker]);

        $this->history->getHistoryIfEmpty($tickerModel);

        $snapshots = $tickerModel->snapshots()
            ->latest('timestamp')
            ->limit(288)
            ->get();

        if ($snapshots->isEmpty()) {
            return response()->json(['error' => "No data found for {$ticker}."], 404);
        }

        $sentimentScores = $tickerModel->sentimentScores()
            ->latest('timestamp')
            ->limit(288)
            ->get();

        return response()->json([
            'ticker'          => $ticker,
            'snapshots'       => $snapshots,
            'sentimentScores' => $sentimentScores,
        ]);
    }
}
