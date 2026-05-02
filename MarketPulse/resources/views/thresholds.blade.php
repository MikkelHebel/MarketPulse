@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Alert Thresholds</h1>
        <p class="text-sm text-gray-400">You will be notified when a Ticker crosses your HCI limits.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('thresholds.upsert') }}">
        @csrf
        <div class="rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Ticker</th>
                        <th class="px-6 py-4 text-center">HCI High (alert above)</th>
                        <th class="px-6 py-4 text-center">HCI Low (alert below)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tickers as $i => $ticker)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">{{ $ticker->ticker }}</td>
                            <input type="hidden" name="thresholds[{{ $i }}][ticker_id]" value="{{ $ticker->id }}">
                            <td class="px-6 py-4 text-center">
                                <input type="number" name="thresholds[{{ $i }}][hci_high]" min="0" max="100" value="{{ $thresholds[$ticker->id]->hci_high ?? '' }}" placeholder="e.g. 75" class="w-24 text-center border border-gray-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="number" name="thresholds[{{ $i }}][hci_low]" min="0" max="100" value="{{ $thresholds[$ticker->id]->hci_low ?? '' }}" placeholder="e.g. 25" class="w-24 text-center border border-gray-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-5 py-2 rounded-lg cursor-pointer">
                  Save Thresholds
            </button>
        </div>
    </form>
@endsection
