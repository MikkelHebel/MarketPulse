@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">{{ $ticker }}</h1>
        <span class="text-sm text-gray-400">5-day price history (5m intervals)</span>
    </div>

    {{-- Loading state --}}
    <div id="search-loading" class="text-center py-20 text-gray-400">
        <p class="text-lg">Loading data for {{ $ticker }}...</p>
    </div>

    {{-- Charts --}}
    <div id="search-charts" class="hidden" data-ticker="{{ $ticker }}">
        <div class="grid grid-cols-2 gap-6">
            <div class="rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Price History (USD)</h2>
                <canvas id="price-chart"></canvas>
            </div>
            <div class="rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Price Movement (% change)</h2>
                <canvas id="pct-chart"></canvas>
            </div>
        </div>
    </div>
@endsection
