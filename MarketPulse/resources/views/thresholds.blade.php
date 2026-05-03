@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Alert Thresholds</h1>
        <p class="text-sm text-gray-400">You will be notified when a ticker's HCI crosses your limits.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('limit') || $errors->has('ticker_id'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
            {{ $errors->first('limit') ?? $errors->first('ticker_id') }}
        </div>
    @endif

    {{-- Existing alerts --}}
    @if($thresholds->isEmpty())
        <p class="text-gray-400 text-sm mb-6">No alerts configured yet.</p>
    @else
        <div class="rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Ticker</th>
                        <th class="px-6 py-4 text-center">HCI High (hype alert)</th>
                        <th class="px-6 py-4 text-center">HCI Low (crash alert)</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($thresholds as $threshold)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">{{ $threshold->ticker->ticker }}</td>
                            <td class="px-6 py-4 text-center text-orange-500 font-medium">
                                {{ $threshold->hci_high ?? '--' }}
                            </td>
                            <td class="px-6 py-4 text-center text-orange-500 font-medium">
                                {{ $threshold->hci_low ?? '--' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('thresholds.destroy', $threshold->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-5 py-2 rounded-lg cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Add new alert --}}
    @if($availableTickers->isNotEmpty())
        <div class="rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Add New Alert</h2>
            <form method="POST" action="{{ route('thresholds.store') }}" class="flex flex-col gap-4">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500">Ticker</label>
                    <select name="ticker_id" required class="border border-gray-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <option value="">Select a ticker</option>
                        @foreach($availableTickers as $ticker)
                            <option value="{{ $ticker->id }}">{{ $ticker->ticker }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-4">
                    <div class="flex flex-col gap-1 flex-1">
                        <label class="text-xs text-gray-500">HCI High — alert above (1–100)</label>
                        <input type="number" name="hci_high" min="1" max="100" placeholder="e.g. 75"
                            class="border border-gray-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div class="flex flex-col gap-1 flex-1">
                        <label class="text-xs text-gray-500">HCI Low — alert below (0–99)</label>
                        <input type="number" name="hci_low" min="0" max="99" placeholder="e.g. 25"
                            class="border border-gray-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-5 py-2 rounded-lg cursor-pointer">
                        Add Alert
                    </button>
                </div>
            </form>
        </div>
    @else
        <p class="text-sm text-gray-400">All 9 tickers are configured.</p>
    @endif
@endsection
