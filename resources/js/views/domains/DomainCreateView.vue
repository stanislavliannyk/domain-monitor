<template>
    <AppLayout>
        <div class="max-w-2xl">
            <div class="mb-6 flex items-center gap-3">
                <RouterLink :to="{ name: 'domains.index' }" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </RouterLink>
                <h1 class="text-2xl font-bold text-gray-900">Добавить домен</h1>
            </div>

            <div class="card p-6">
                <DomainForm
                    :errors="errors"
                    :loading="loading"
                    submit-label="Добавить домен"
                    @submit="handleSubmit"
                    @cancel="router.push({ name: 'domains.index' })"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { domainsApi } from '@/api/domains'
import { useFlashStore } from '@/stores/flash'
import { useFormErrors } from '@/composables/useFormErrors'
import AppLayout from '@/components/AppLayout.vue'
import DomainForm from '@/components/DomainForm.vue'

const router  = useRouter()
const flash   = useFlashStore()
const loading = ref(false)

const { errors, clearErrors, handleError } = useFormErrors()

async function handleSubmit(form) {
    clearErrors()
    loading.value = true
    try {
        const { data } = await domainsApi.create(form)
        flash.success('Домен добавлен. Первая проверка поставлена в очередь.')
        router.push({ name: 'domains.show', params: { id: data.data.id } })
    } catch (err) {
        handleError(err)
    } finally {
        loading.value = false
    }
}
</script>
