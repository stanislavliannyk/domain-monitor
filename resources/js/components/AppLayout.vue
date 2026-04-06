<template>
    <div class="min-h-full">
        <!-- Навигация -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <!-- Логотип -->
                    <RouterLink
                        :to="{ name: 'dashboard' }"
                        class="flex items-center gap-2 font-bold text-blue-600 text-lg"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.745 3.745 0 013.296-1.043A3.745 3.745 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 013.296 1.043 3.745 3.745 0 011.043 3.296A3.745 3.745 0 0121 12z"
                            />
                        </svg>
                        Монитор доменов
                    </RouterLink>

                    <!-- Ссылки навигации -->
                    <div class="hidden md:flex items-center gap-6">
                        <RouterLink
                            :to="{ name: 'dashboard' }"
                            class="text-sm font-medium transition-colors"
                            :class="$route.name === 'dashboard' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                        >Главная</RouterLink>

                        <RouterLink
                            :to="{ name: 'domains.index' }"
                            class="text-sm font-medium transition-colors"
                            :class="$route.name?.startsWith('domains') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                        >Домены</RouterLink>
                    </div>

                    <!-- Пользователь / выход -->
                    <div class="flex items-center gap-4">
                        <span class="hidden sm:block text-sm text-gray-500">{{ auth.user?.name }}</span>
                        <button
                            @click="handleLogout"
                            :disabled="loggingOut"
                            class="text-sm text-gray-600 hover:text-red-600 transition-colors disabled:opacity-50"
                        >Выйти</button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Основной контент -->
        <main class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useFlashStore } from '@/stores/flash'

const auth       = useAuthStore()
const flash      = useFlashStore()
const router     = useRouter()
const loggingOut = ref(false)

async function handleLogout() {
    loggingOut.value = true
    try {
        await auth.logout()
        router.push({ name: 'login' })
    } catch {
        flash.error('Не удалось выйти. Попробуйте снова.')
    } finally {
        loggingOut.value = false
    }
}
</script>
