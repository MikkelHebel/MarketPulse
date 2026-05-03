@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Market Overview</h1>
        <div class="text-right">
            <span class="text-sm text-gray-400">Updates every minute</span>
            <p class="text-xs text-gray-400">Last updated: <span id="last-updated">--:--</span></p>
        </div>
    </div>

    <div class="grid grid-cols-5 gap-6">
        {{-- Chart --}}
        <div class="col-span-3 rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Price Movement (% change)</h2>
            <canvas id="price-chart"></canvas>
            <div id="chart-filters"></div>
        </div>

        {{-- Ticker table --}}
        <div class="col-span-2 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Ticker</th>
                        <th class="px-6 py-4 text-right">Price (USD)</th>
                        <th class="px-6 py-4 text-right">Sentiment</th>
                        <th class="px-6 py-4 text-right">HCI Score</th>
                    </tr>
                </thead>
                <tbody id="ticker-table" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <p class="text-xs text-gray-400 mt-2">HCI Score: Hype Correlation Index</p>

    {{-- Recently Searched --}}
    <div class="mt-10 rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Recently Searched</h2>
        </div>
        @if($recentSearches->isEmpty())
            <p class="px-6 py-8 text-center text-gray-400 text-sm">No searches yet.</p>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">Ticker</th>
                        <th class="px-6 py-3 text-right">Searched At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentSearches as $search)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 font-semibold">
                                @auth
                                    <a href="{{ route('search') }}?ticker={{ $search->ticker->ticker }}" class="text-orange-500 hover:underline">
                                        {{ $search->ticker->ticker }}
                                    </a>
                                @endauth
                                @guest
                                    {{ $search->ticker->ticker }}
                                @endguest
                            </td>
                            <td class="px-6 py-3 text-right text-gray-400">
                                {{ $search->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
