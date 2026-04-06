@php
    $map = [
        'up'      => ['bg-green-100 text-green-800', 'UP'],
        'down'    => ['bg-red-100 text-red-800',     'DOWN'],
        'unknown' => ['bg-gray-100 text-gray-600',   'UNKNOWN'],
    ];
    [$classes, $label] = $map[$status] ?? $map['unknown'];
@endphp
<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $classes }}">
    <span class="inline-block h-1.5 w-1.5 rounded-full
        {{ $status === 'up' ? 'bg-green-500' : ($status === 'down' ? 'bg-red-500' : 'bg-gray-400') }}"></span>
    {{ $label }}
</span>
