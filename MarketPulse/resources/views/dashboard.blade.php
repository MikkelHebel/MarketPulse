@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Market Overview</h1>
        <div class="text-right">
            <span class="text-sm text-gray-400">Updates every minute</span>
            <p class="text-xs text-gray-400">Last updated: <span id="last-updated">--:--</span></p>
        </div>
    </div>

    {{-- Ticker table --}}
    <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">#</th>
                    <th class="px-6 py-4 text-left">Ticker</th>
                    <th class="px-6 py-4 text-right">Price (USD)</th>
                    <th class="px-6 py-4 text-right">Sentiment</th>
                    <th class="px-6 py-4 text-right">HCI Score</th>
                </tr>
            </thead>
            <tbody id="ticker-table" class="divide-y divide-gray-100">
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="text-xs text-gray-400 mt-2">HCI Score: Hype Correlation Index</p>
@endsection
