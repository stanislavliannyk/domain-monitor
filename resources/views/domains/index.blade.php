@extends('layouts.app')

@section('title', 'Domains')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Domains</h1>
        <a href="{{ route('domains.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white
                  hover:bg-blue-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Add Domain
        </a>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        @if ($domains->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400">
                <p>No domains monitored yet.</p>
                <a href="{{ route('domains.create') }}" class="mt-2 inline-block text-blue-600 hover:underline text-sm">
                    Add your first domain
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Name / URL</th>
                            <th class="px-6 py-3 hidden sm:table-cell">Method</th>
                            <th class="px-6 py-3 hidden md:table-cell">Interval</th>
                            <th class="px-6 py-3 hidden lg:table-cell">Last check</th>
                            <th class="px-6 py-3 hidden lg:table-cell">Active</th>
                            <th class="px-6 py-3">Actions</th>
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
                                <p class="text-xs text-gray-400 truncate max-w-xs">{{ $domain->url }}</p>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-mono text-gray-600">
                                    {{ $domain->check_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-500">
                                {{ $domain->check_interval }}m
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell text-sm text-gray-500">
                                {{ $domain->last_checked_at?->diffForHumans() ?? '—' }}
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @if ($domain->is_active)
                                    <span class="text-green-600 text-xs font-medium">Yes</span>
                                @else
                                    <span class="text-gray-400 text-xs font-medium">Paused</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('domains.edit', $domain) }}"
                                       class="text-xs text-blue-600 hover:underline">Edit</a>

                                    <form method="POST" action="{{ route('domains.check-now', $domain) }}">
                                        @csrf
                                        <button class="text-xs text-gray-500 hover:text-blue-600 hover:underline">
                                            Check now
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('domains.destroy', $domain) }}"
                                          onsubmit="return confirm('Delete {{ addslashes($domain->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($domains->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $domains->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
