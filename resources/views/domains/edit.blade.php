@extends('layouts.app')

@section('title', 'Edit — ' . $domain->name)

@section('content')
<div class="max-w-2xl">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('domains.show', $domain) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Domain</h1>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        @include('components.domain-form', [
            'domain' => $domain,
            'action' => route('domains.update', $domain),
            'method' => 'PUT',
        ])
    </div>
</div>
@endsection
