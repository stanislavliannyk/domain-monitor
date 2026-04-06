@extends('layouts.app')

@section('title', $domain->name)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('domains.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $domain->name }}</h1>
                    @include('components.status-badge', ['status' => $domain->status])
                </div>
                <a href="{{ $domain->url }}" target="_blank" rel="noopener noreferrer"
                   class="text-sm text-gray-400 hover:text-blue-600 hover:underline break-all">
                    {{ $domain->url }}
                </a>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('domains.check-now', $domain) }}">
                @csrf
                <button class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600
                               hover:bg-gray-50 hover:border-gray-400 transition-colors">
                    Check Now
                </button>
            </form>
            <a href="{{ route('domains.edit', $domain) }}"
               class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                Edit
            </a>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Uptime (7d)</p>
            <p class="mt-1 text-2xl font-bold {{ $stats['uptime_7d'] >= 99 ? 'text-green-600' : ($stats['uptime_7d'] >= 95 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ number_format($stats['uptime_7d'], 1) }}%
            </p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Avg Response (7d)</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">
                @if ($stats['avg_response_7d'])
                    {{ number_format($stats['avg_response_7d']) }}<span class="text-sm font-normal text-gray-400">ms</span>
                @else
                    —
                @endif
            </p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Check Interval</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">
                {{ $domain->check_interval }}<span class="text-sm font-normal text-gray-400">m</span>
            </p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Last Checked</p>
            <p class="mt-1 text-lg font-semibold text-gray-800">
                {{ $domain->last_checked_at?->diffForHumans() ?? 'Never' }}
            </p>
        </div>
    </div>

    {{-- Check configuration --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-5">
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Configuration</h2>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-4 text-sm">
            <div>
                <dt class="text-gray-400">Method</dt>
                <dd class="font-mono font-medium text-gray-800">{{ $domain->check_method }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Timeout</dt>
                <dd class="font-medium text-gray-800">{{ $domain->request_timeout }}s</dd>
            </div>
            <div>
                <dt class="text-gray-400">Active</dt>
                <dd class="font-medium {{ $domain->is_active ? 'text-green-600' : 'text-gray-400' }}">
                    {{ $domain->is_active ? 'Yes' : 'Paused' }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Next check</dt>
                <dd class="font-medium text-gray-800">{{ $domain->nextCheckAt()?->diffForHumans() ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Check history --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Check History</h2>
            <span class="text-xs text-gray-400">Latest first</span>
        </div>

        @if ($logs->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">No checks recorded yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Result</th>
                            <th class="px-6 py-3">Date / Time</th>
                            <th class="px-6 py-3 hidden sm:table-cell">HTTP Code</th>
                            <th class="px-6 py-3 hidden sm:table-cell">Response Time</th>
                            <th class="px-6 py-3">Error</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach ($logs as $log)
                        <tr class="{{ $log->is_up ? '' : 'bg-red-50' }} hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3">
                                @include('components.status-badge', ['status' => $log->is_up ? 'up' : 'down'])
                            </td>
                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                {{ $log->checked_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-3 hidden sm:table-cell">
                                @if ($log->http_code)
                                    <span class="font-mono font-medium
                                        {{ $log->http_code < 400 ? 'text-green-700' : 'text-red-600' }}">
                                        {{ $log->http_code }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 hidden sm:table-cell text-gray-600">
                                {{ $log->responseTimeSec() ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-red-600 text-xs max-w-xs truncate">
                                {{ $log->error_message ?? '' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Danger zone --}}
    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
        <h2 class="text-sm font-semibold text-red-700 mb-3">Danger Zone</h2>
        <form method="POST" action="{{ route('domains.destroy', $domain) }}"
              onsubmit="return confirm('This will permanently delete the domain and ALL its check history. Continue?')">
            @csrf @method('DELETE')
            <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white
                           hover:bg-red-700 transition-colors">
                Delete Domain
            </button>
        </form>
    </div>

</div>
@endsection
