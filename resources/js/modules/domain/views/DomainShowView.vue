<template>
    <AppLayout>
        <div v-if="fetching" class="space-y-6">
            <div class="h-10 w-64 rounded bg-gray-200 animate-pulse" />
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div v-for="i in 4" :key="i" class="card p-5 h-20 animate-pulse bg-gray-100" />
            </div>
        </div>

        <div v-else-if="domain" class="space-y-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <RouterLink :to="{ name: 'domains.index' }" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                    </RouterLink>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900">{{ domain.name }}</h1>
                            <StatusBadge :status="domain.status" />
                        </div>
                        <a :href="domain.url" target="_blank" rel="noopener noreferrer"
                           class="text-sm text-gray-400 hover:text-blue-600 hover:underline break-all">
                            {{ domain.url }}
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="handleCheckNow" :disabled="checking" class="btn-secondary text-sm">
                        {{ checking ? 'Запуск…' : 'Проверить сейчас' }}
                    </button>
                    <RouterLink :to="{ name: 'domains.edit', params: { id: domain.id } }"
                                class="btn-primary text-sm">
                        Редактировать
                    </RouterLink>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="card p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Доступность (7 дн.)</p>
                    <p class="mt-1 text-2xl font-bold"
                       :class="domain.stats.uptime_7d >= 99 ? 'text-green-600'
                             : domain.stats.uptime_7d >= 95 ? 'text-yellow-600'
                             : 'text-red-600'">
                        {{ domain.stats.uptime_7d.toFixed(1) }}%
                    </p>
                </div>
                <div class="card p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Среднее время ответа (7 дн.)</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">
                        <template v-if="domain.stats.avg_response_7d">
                            {{ Math.round(domain.stats.avg_response_7d) }}<span class="text-sm font-normal text-gray-400"> мс</span>
                        </template>
                        <template v-else>—</template>
                    </p>
                </div>
                <div class="card p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Интервал проверок</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">
                        {{ domain.check_interval }}<span class="text-sm font-normal text-gray-400"> мин.</span>
                    </p>
                </div>
                <div class="card p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Последняя проверка</p>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        {{ domain.last_checked_at ? diffForHumans(domain.last_checked_at) : 'Никогда' }}
                    </p>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Конфигурация</h2>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-4 text-sm">
                    <div>
                        <dt class="text-gray-400">Метод</dt>
                        <dd class="font-mono font-medium text-gray-800">{{ domain.check_method }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Таймаут</dt>
                        <dd class="font-medium text-gray-800">{{ domain.request_timeout }} с</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Активен</dt>
                        <dd class="font-medium" :class="domain.is_active ? 'text-green-600' : 'text-gray-400'">
                            {{ domain.is_active ? 'Да' : 'Приостановлен' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Уведомления</dt>
                        <dd class="font-medium text-gray-800">{{ domain.notify_on_failure ? 'Включены' : 'Отключены' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800">История проверок</h2>
                    <span class="text-xs text-gray-400">Сначала новые</span>
                </div>

                <div v-if="logsLoading" class="p-6 space-y-2">
                    <div v-for="i in 5" :key="i" class="h-8 rounded bg-gray-100 animate-pulse" />
                </div>

                <div v-else-if="logs.length === 0" class="px-6 py-10 text-center text-gray-400 text-sm">
                    Проверки ещё не выполнялись.
                </div>

                <template v-else>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Результат</th>
                                    <th class="px-6 py-3">Дата и время</th>
                                    <th class="px-6 py-3 hidden sm:table-cell">HTTP-код</th>
                                    <th class="px-6 py-3 hidden sm:table-cell">Время ответа</th>
                                    <th class="px-6 py-3">Ошибка</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                <tr v-for="log in logs" :key="log.id"
                                    :class="['hover:bg-gray-50 transition-colors', !log.is_up ? 'bg-red-50' : '']">
                                    <td class="px-6 py-3">
                                        <StatusBadge :status="log.is_up ? 'up' : 'down'" />
                                    </td>
                                    <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                        {{ formatDatetime(log.checked_at) }}
                                    </td>
                                    <td class="px-6 py-3 hidden sm:table-cell">
                                        <span v-if="log.http_code"
                                              class="font-mono font-medium"
                                              :class="log.http_code < 400 ? 'text-green-700' : 'text-red-600'">
                                            {{ log.http_code }}
                                        </span>
                                        <span v-else class="text-gray-300">—</span>
                                    </td>
                                    <td class="px-6 py-3 hidden sm:table-cell text-gray-600">
                                        {{ log.response_time_ms != null
                                            ? (log.response_time_ms / 1000).toFixed(3) + ' с'
                                            : '—' }}
                                    </td>
                                    <td class="px-6 py-3 text-red-600 text-xs max-w-xs truncate">
                                        {{ log.error_message ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="logsMeta.last_page > 1" class="px-6 py-4 border-t border-gray-100">
                        <Pagination :meta="logsMeta" @change="loadLogs" />
                    </div>
                </template>
            </div>

            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h2 class="text-sm font-semibold text-red-700 mb-3">Зона опасности</h2>
                <button @click="handleDelete" class="btn-danger text-sm">Удалить домен</button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { domainsApi } from '@/modules/domain/api'
import { useFlashStore } from '@/shared/stores/flash'
import { diffForHumans, formatDatetime } from '@/shared/utils/date'
import AppLayout from '@/shared/components/AppLayout.vue'
import StatusBadge from '@/shared/components/StatusBadge.vue'
import Pagination from '@/shared/components/Pagination.vue'

const route      = useRoute()
const router     = useRouter()
const flash      = useFlashStore()
const domainId   = route.params.id

const fetching    = ref(true)
const domain      = ref(null)
const checking    = ref(false)
const logsLoading = ref(true)
const logs        = ref([])
const logsMeta    = ref({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0 })

onMounted(async () => {
    try {
        const { data } = await domainsApi.get(domainId)
        domain.value = data.data
    } catch {
        flash.error('Домен не найден.')
        return router.push({ name: 'domains.index' })
    } finally {
        fetching.value = false
    }

    loadLogs()
})

async function loadLogs(page = 1) {
    logsLoading.value = true
    try {
        const { data } = await domainsApi.logs(domainId, page)
        logs.value     = data.data
        logsMeta.value = data.meta
    } catch {
        flash.error('Не удалось загрузить историю проверок.')
    } finally {
        logsLoading.value = false
    }
}

async function handleCheckNow() {
    checking.value = true
    try {
        await domainsApi.checkNow(domainId)
        flash.success('Проверка поставлена в очередь. Результат появится в ближайшее время.')
    } catch {
        flash.error('Не удалось запустить проверку.')
    } finally {
        checking.value = false
    }
}

async function handleDelete() {
    if (!confirm(`Это действие безвозвратно удалит «${domain.value.name}» и всю историю проверок. Продолжить?`)) return
    try {
        await domainsApi.remove(domainId)
        flash.success('Домен удалён.')
        router.push({ name: 'domains.index' })
    } catch {
        flash.error('Не удалось удалить домен.')
    }
}
</script>
