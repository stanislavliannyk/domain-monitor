<template>
    <GuestLayout>
        <template #subtitle>Создайте новую учётную запись</template>

        <form @submit.prevent="submit" class="space-y-5">
            <FormField id="name" label="Полное имя" :error="errors.name?.[0]" required>
                <input id="name" v-model="form.name" type="text" required autofocus
                       :class="['form-input mt-1', errors.name ? 'form-input-error' : '']" />
            </FormField>

            <FormField id="email" label="Email" :error="errors.email?.[0]" required>
                <input id="email" v-model="form.email" type="email" required
                       :class="['form-input mt-1', errors.email ? 'form-input-error' : '']" />
            </FormField>

            <FormField id="password" label="Пароль" :error="errors.password?.[0]" required>
                <input id="password" v-model="form.password" type="password" required
                       :class="['form-input mt-1', errors.password ? 'form-input-error' : '']" />
            </FormField>

            <FormField id="password_confirmation" label="Подтверждение пароля">
                <input id="password_confirmation" v-model="form.password_confirmation"
                       type="password" required class="form-input mt-1" />
            </FormField>

            <button type="submit" :disabled="loading" class="btn-primary w-full">
                <span v-if="loading">Создание…</span>
                <span v-else>Создать учётную запись</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Уже есть учётная запись?
            <RouterLink :to="{ name: 'login' }" class="font-medium text-blue-600 hover:text-blue-500">
                Войти
            </RouterLink>
        </p>
    </GuestLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useFlashStore } from '@/stores/flash'
import { useFormErrors } from '@/composables/useFormErrors'
import GuestLayout from '@/components/GuestLayout.vue'
import FormField from '@/components/FormField.vue'

const auth   = useAuthStore()
const flash  = useFlashStore()
const router = useRouter()

const { errors, clearErrors, handleError } = useFormErrors()
const loading = ref(false)

const form = reactive({ name: '', email: '', password: '', password_confirmation: '' })

async function submit() {
    clearErrors()
    loading.value = true
    try {
        await auth.register(form)
        router.push({ name: 'dashboard' })
    } catch (err) {
        handleError(err)
        if (!Object.keys(errors.value).length) {
            flash.error('Ошибка регистрации. Попробуйте снова.')
        }
    } finally {
        loading.value = false
    }
}
</script>
