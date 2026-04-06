<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="text-center">
            <p class="text-6xl font-bold text-blue-600">404</p>
            <h1 class="mt-4 text-2xl font-bold text-gray-900">Страница не найдена</h1>
            <p class="mt-2 text-gray-500">Запрашиваемая страница не существует.</p>
            <RouterLink :to="{ name: 'dashboard' }" class="mt-6 inline-block btn-primary">
                На главную
            </RouterLink>
        </div>
    </div>
</template>
