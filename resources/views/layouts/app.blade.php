<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Domain Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eff6ff',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full">

<div class="min-h-full">
    {{-- Navigation --}}
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between items-center">
                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-blue-600 text-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.745 3.745 0 013.296-1.043A3.745 3.745 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 013.296 1.043 3.745 3.745 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                    </svg>
                    Domain Monitor
                </a>

                {{-- Nav links --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('dashboard') }}"
                       class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('domains.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('domains.*') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Domains
                    </a>
                </div>

                {{-- User menu --}}
                <div class="flex items-center gap-4">
                    <span class="hidden sm:block text-sm text-gray-500">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm text-gray-600 hover:text-red-600 transition-colors">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="bg-green-50 border-b border-green-200">
            <div class="mx-auto max-w-7xl px-4 py-3 flex justify-between items-center">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
                <button @click="show = false" class="text-green-600 hover:text-green-800 text-lg leading-none">&times;</button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show"
             class="bg-red-50 border-b border-red-200">
            <div class="mx-auto max-w-7xl px-4 py-3 flex justify-between items-center">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
                <button @click="show = false" class="text-red-600 hover:text-red-800 text-lg leading-none">&times;</button>
            </div>
        </div>
    @endif

    {{-- Page content --}}
    <main class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
