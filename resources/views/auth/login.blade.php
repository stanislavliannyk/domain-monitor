@extends('layouts.guest')

@section('title', 'Sign In')
@section('subtitle', 'Sign in to your account')

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
               class="mt-1 block w-full rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror
                      px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" id="password" name="password" required
               class="mt-1 block w-full rounded-lg border @error('password') border-red-500 @else border-gray-300 @enderror
                      px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600">
            Remember me
        </label>
    </div>

    <button type="submit"
            class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow
                   hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
        Sign in
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-500">
    No account?
    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">Register</a>
</p>
@endsection
