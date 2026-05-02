<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticker;
use App\Models\UserThreshold;

class ThresholdController extends Controller
{
    public function index(Request $request)
    {
        $tickers = Ticker::all();
        $thresholds = UserThreshold::where('user_id', $request->user()->id)->get()->keyBy('ticker_id');

        return view('thresholds', compact('tickers', 'thresholds'));
    }

    public function upsert(Request $request)
    {
        $request->validate([
            'thresholds' => 'array',
            'thresholds.*.ticker_id' => 'required|exists:tickers,id',
            'thresholds.*.hci_high' => 'nullable|integer|min:0|max:100',
            'thresholds.*.hci_low' => 'nullable|integer|min:0|max:100',
        ]);

        foreach($request->input('thresholds', []) as $data) {
            $high = $data['hci_high'] !== '' ? (int) $data['hci_high'] : null;
            $low = $data['hci_low'] !== '' ? (int) $data['hci_low'] : null;

            if ($high === null && $low === null) {
                UserThreshold::where('user_id', $request->user()->id)->where('ticker_id', $data['ticker_id'])->delete();
                continue;
            }

            UserThreshold::updateOrCreate(
                ['user_id' => $request->user()->id, 'ticker_id' => $data['ticker_id']],
                ['hci_high' => $high ?? 75, 'hci_low' => $low ?? 25]
            );
        }

        return back()->with('success', 'Alert thresholds saved.');
    }
}
