<template>
    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Домены</h1>
                <RouterLink :to="{ name: 'domains.create' }" class="btn-primary inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Добавить домен
                </RouterLink>
            </div>

            <div class="card overflow-hidden">
                <!-- Скелетон -->
                <div v-if="loading" class="p-6 space-y-3">
                    <div v-for="i in 3" :key="i" class="h-10 rounded bg-gray-100 animate-pulse" />
                </div>

                <div v-else-if="domains.length === 0" class="px-6 py-16 text-center text-gray-400">
                    <p>Мониторинг доменов не настроен.</p>
                    <RouterLink :to="{ name: 'domains.create' }"
                                class="mt-2 inline-block text-blue-600 hover:underline text-sm">
                        Добавить первый домен
                    </RouterLink>
                </div>

                <template v-else>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Статус</th>
                                    <th class="px-6 py-3">Название / URL</th>
                                    <th class="px-6 py-3 hidden sm:table-cell">Метод</th>
                                    <th class="px-6 py-3 hidden md:table-cell">Интервал</th>
                                    <th class="px-6 py-3 hidden lg:table-cell">Последняя проверка</th>
                                    <th class="px-6 py-3">Действия</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
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
                                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ domain.url }}</p>
                                    </td>
                                    <td class="px-6 py-4 hidden sm:table-cell">
                                        <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-mono text-gray-600">
                                            {{ domain.check_method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 hidden md:table-cell text-gray-500">
                                        {{ domain.check_interval }} мин.
                                    </td>
                                    <td class="px-6 py-4 hidden lg:table-cell text-gray-500">
                                        {{ domain.last_checked_at ? diffForHumans(domain.last_checked_at) : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <RouterLink
                                                :to="{ name: 'domains.edit', params: { id: domain.id } }"
                                                class="text-xs text-blue-600 hover:underline">
                                                Изменить
                                            </RouterLink>
                                            <button @click="handleCheckNow(domain)"
                                                    class="text-xs text-gray-500 hover:text-blue-600 hover:underline">
                                                Проверить
                                            </button>
                                            <button @click="handleDelete(domain)"
                                                    class="text-xs text-red-500 hover:underline">
                                                Удалить
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-gray-100">
                        <Pagination :meta="pagination" @change="loadPage" />
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { domainsApi } from '@/api/domains'
import { useFlashStore } from '@/stores/flash'
import { diffForHumans } from '@/utils/date'
import AppLayout from '@/components/AppLayout.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Pagination from '@/components/Pagination.vue'

const flash      = useFlashStore()
const loading    = ref(true)
const domains    = ref([])
const pagination = ref({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0 })

async function loadPage(page = 1) {
    loading.value = true
    try {
        const { data } = await domainsApi.list(page)
        domains.value    = data.data
        pagination.value = data.meta
    } catch {
        flash.error('Не удалось загрузить список доменов.')
    } finally {
        loading.value = false
    }
}

async function handleCheckNow(domain) {
    try {
        await domainsApi.checkNow(domain.id)
        flash.success(`Проверка «${domain.name}» поставлена в очередь.`)
    } catch {
        flash.error('Не удалось запустить проверку.')
    }
}

async function handleDelete(domain) {
    if (!confirm(`Удалить «${domain.name}» и всю историю проверок?`)) return
    try {
        await domainsApi.remove(domain.id)
        flash.success(`Домен «${domain.name}» удалён.`)
        domains.value = domains.value.filter(d => d.id !== domain.id)
    } catch {
        flash.error('Не удалось удалить домен.')
    }
}

onMounted(() => loadPage())
</script>
