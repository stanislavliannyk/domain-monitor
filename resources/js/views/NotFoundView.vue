<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="text-center">
            <p class="text-6xl font-bold text-blue-600">404</p>
            <h1 class="mt-4 text-2xl font-bold text-gray-900">Page not found</h1>
            <p class="mt-2 text-gray-500">The page you are looking for does not exist.</p>
            <RouterLink :to="{ name: 'dashboard' }" class="mt-6 inline-block btn-primary">
                Go to Dashboard
            </RouterLink>
        </div>
    </div>
</template>
