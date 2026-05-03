<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Ticker;
use App\Models\RecentSearch;
use App\Services\HypeCorrelationService;
use App\Services\StockStrategy;

class DashboardController extends Controller
{
    public function __construct(private HypeCorrelationService $hype, private StockStrategy $stocks) {}

    public function index()
    {
        $recentSearches = RecentSearch::with('ticker')->latest()->limit(25)->get();

        return view('dashboard', compact('recentSearches'));
    }

    public function chartData(): JsonResponse
    {
        $tickers = Ticker::with([
            'snapshots'       => fn($q) => $q->latest('timestamp')->limit(60),
            'sentimentScores' => fn($q) => $q->latest('timestamp')->limit(60),
        ])->whereIn('ticker', $this->stocks->tickers())
            ->get()
            ->map(fn($ticker) => [
            'ticker'    => $ticker->ticker,
            'price'     => $ticker->snapshots->first()?->price,
            'sentiment' => $ticker->sentimentScores->first()?->score ?? '--',
            'hci'       => $this->hype->calculate($ticker->ticker),
            'snapshots' => $ticker->snapshots,
        ]);

     return response()->json($tickers);
    }
}
