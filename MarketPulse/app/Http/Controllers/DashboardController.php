<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticker;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function chartData(): JsonResponse
    {
        $tickers = Ticker::with([
         'snapshots'       => fn($q) => $q->latest('timestamp')->limit(60),
         'sentimentScores' => fn($q) => $q->latest('timestamp')->limit(60),
     ])->get();

     return response()->json($tickers);
    }
}
