<template>
    <AppLayout>
        <div class="space-y-8">
            <!-- Заголовок -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Главная</h1>
                <RouterLink :to="{ name: 'domains.create' }" class="btn-primary inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Добавить домен
                </RouterLink>
            </div>

            <!-- Скелетон загрузки -->
            <template v-if="loading">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div v-for="i in 4" :key="i" class="card p-5 h-20 animate-pulse bg-gray-100" />
                </div>
            </template>

            <template v-else>
                <!-- Статистика -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="card p-5">
                        <p class="text-sm text-gray-500">Всего</p>
                        <p class="mt-1 text-3xl font-bold text-blue-600">{{ stats.total }}</p>
                    </div>
                    <div class="card p-5">
                        <p class="text-sm text-gray-500">Работают</p>
                        <p class="mt-1 text-3xl font-bold text-green-600">{{ stats.up }}</p>
                    </div>
                    <div class="card p-5">
                        <p class="text-sm text-gray-500">Недоступны</p>
                        <p class="mt-1 text-3xl font-bold text-red-600">{{ stats.down }}</p>
                    </div>
                    <div class="card p-5">
                        <p class="text-sm text-gray-500">Неизвестно</p>
                        <p class="mt-1 text-3xl font-bold text-gray-500">{{ stats.unknown }}</p>
                    </div>
                </div>

                <!-- Таблица доменов -->
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-800">Ваши домены</h2>
                    </div>

                    <div v-if="domains.length === 0" class="px-6 py-12 text-center text-gray-400">
                        <p>Домены ещё не добавлены.</p>
                        <RouterLink :to="{ name: 'domains.create' }"
                                    class="mt-2 inline-block text-blue-600 hover:underline text-sm">
                            Добавить первый домен
                        </RouterLink>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Статус</th>
                                    <th class="px-6 py-3">Домен</th>
                                    <th class="px-6 py-3 hidden sm:table-cell">Последняя проверка</th>
                                    <th class="px-6 py-3 hidden md:table-cell">Интервал</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="domain in domains" :key="domain.id"
                                    class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <StatusBadge :status="domain.status" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <RouterLink
                                            :to="{ name: 'domains.show', params: { id: domain.id } }"
                                            class="font-medium text-gray-900 hover:text-blue-600">
                                            {{ domain.name }}
                                        </RouterLink>
                                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ domain.url }}</p>
                                    </td>
                                    <td class="px-6 py-4 hidden sm:table-cell text-sm text-gray-500">
                                        {{ domain.last_checked_at ? diffForHumans(domain.last_checked_at) : '—' }}
                                    </td>
                                    <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-500">
                                        каждые {{ domain.check_interval }} мин.
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <RouterLink
                                            :to="{ name: 'domains.show', params: { id: domain.id } }"
                                            class="text-xs text-blue-600 hover:underline">
                                            Подробнее
                                        </RouterLink>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { dashboardApi } from '@/api/dashboard'
import { useFlashStore } from '@/stores/flash'
import { diffForHumans } from '@/utils/date'
import AppLayout from '@/components/AppLayout.vue'
import StatusBadge from '@/components/StatusBadge.vue'

const flash   = useFlashStore()
const loading = ref(true)
const stats   = ref({ total: 0, up: 0, down: 0, unknown: 0 })
const domains = ref([])

onMounted(async () => {
    try {
        const { data } = await dashboardApi.get()
        stats.value   = data.data.stats
        domains.value = data.data.domains
    } catch {
        flash.error('Не удалось загрузить данные главной страницы.')
    } finally {
        loading.value = false
    }
})
</script>
