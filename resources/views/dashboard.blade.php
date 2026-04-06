@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <a href="{{ route('domains.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white
                  hover:bg-blue-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Add Domain
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @php
            $statCards = [
                ['label' => 'Total',   'value' => $stats['total'],   'color' => 'blue'],
                ['label' => 'Up',      'value' => $stats['up'],      'color' => 'green'],
                ['label' => 'Down',    'value' => $stats['down'],    'color' => 'red'],
                ['label' => 'Unknown', 'value' => $stats['unknown'], 'color' => 'gray'],
            ];
        @endphp

        @foreach ($statCards as $card)
        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
            <p class="mt-1 text-3xl font-bold
                @if($card['color'] === 'green') text-green-600
                @elseif($card['color'] === 'red') text-red-600
                @elseif($card['color'] === 'blue') text-blue-600
                @else text-gray-500 @endif">
                {{ $card['value'] }}
            </p>
        </div>
        @endforeach
    </div>

    {{-- Domains table --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Your Domains</h2>
        </div>

        @if ($domains->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253"/>
                </svg>
                <p>No domains yet.</p>
                <a href="{{ route('domains.create') }}" class="mt-2 inline-block text-blue-600 hover:underline text-sm">Add your first domain</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Domain</th>
                            <th class="px-6 py-3 hidden sm:table-cell">Last check</th>
                            <th class="px-6 py-3 hidden md:table-cell">Interval</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($domains as $domain)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                @include('components.status-badge', ['status' => $domain->status])
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('domains.show', $domain) }}"
                                   class="font-medium text-gray-900 hover:text-blue-600">
                                    {{ $domain->name }}
                                </a>
                                <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $domain->url }}</p>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell text-sm text-gray-500">
                                {{ $domain->last_checked_at ? $domain->last_checked_at->diffForHumans() : '—' }}
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-500">
                                every {{ $domain->check_interval }}m
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('domains.show', $domain) }}"
                                   class="text-xs text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
