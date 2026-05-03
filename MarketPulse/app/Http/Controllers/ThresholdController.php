<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticker;
use App\Models\UserThreshold;

class ThresholdController extends Controller
{
    public function index(Request $request)
    {
        $thresholds = UserThreshold::where('user_id', $request->user()->id)
            ->with('ticker')
            ->get();

        $configuredIds = $thresholds->pluck('ticker_id');
        $availableTickers = Ticker::whereNotIn('id', $configuredIds)->get();

        return view('thresholds', compact('thresholds', 'availableTickers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ticker_id' => 'required|exists:tickers,id',
            'hci_high'  => 'nullable|integer|min:1|max:100',
            'hci_low'   => 'nullable|integer|min:0|max:99',
        ]);

        if (UserThreshold::where('user_id', $request->user()->id)->count() >= 9) {
            return back()->withErrors(['limit' => 'You have reached the maximum of 9 alerts.']);
        }

        if (UserThreshold::where('user_id', $request->user()->id)->where('ticker_id', $request->ticker_id)->exists()) {
            return back()->withErrors(['ticker_id' => 'An alert for this ticker already exists.']);
        }

        UserThreshold::create([
            'user_id'   => $request->user()->id,
            'ticker_id' => $request->ticker_id,
            'hci_high'  => $request->hci_high ?: null,
            'hci_low'   => $request->hci_low !== null && $request->hci_low !== '' ? (int) $request->hci_low : null,
        ]);

        return back()->with('success', 'Alert added.');
    }

    public function destroy(Request $request, UserThreshold $threshold)
    {
        if ($threshold->user_id !== $request->user()->id) {
            abort(403);
        }

        $threshold->delete();
        return back()->with('success', 'Alert removed.');
    }
}
