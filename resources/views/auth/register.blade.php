@extends('layouts.guest')

@section('title', 'Register')
@section('subtitle', 'Create a new account')

@section('content')
<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
               class="mt-1 block w-full rounded-lg border @error('name') border-red-500 @else border-gray-300 @enderror
                      px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required
               class="mt-1 block w-full rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror
                      px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" id="password" name="password" required
               class="mt-1 block w-full rounded-lg border @error('password') border-red-500 @else border-gray-300 @enderror
                      px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm
                      focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <button type="submit"
            class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow
                   hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
        Create account
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-500">
    Already have an account?
    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Sign in</a>
</p>
@endsection
