<template>
    <GuestLayout>
        <template #subtitle>Sign in to your account</template>

        <form @submit.prevent="submit" class="space-y-5">
            <FormField id="email" label="Email address" :error="errors.email?.[0]" required>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    required
                    autofocus
                    :class="['form-input mt-1', errors.email ? 'form-input-error' : '']"
                />
            </FormField>

            <FormField id="password" label="Password" :error="errors.password?.[0]" required>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    required
                    :class="['form-input mt-1', errors.password ? 'form-input-error' : '']"
                />
            </FormField>

            <div class="flex items-center gap-2">
                <input id="remember" v-model="form.remember" type="checkbox"
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <label for="remember" class="text-sm text-gray-600 cursor-pointer">Remember me</label>
            </div>

            <button type="submit" :disabled="loading" class="btn-primary w-full">
                <span v-if="loading">Signing in…</span>
                <span v-else>Sign in</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            No account?
            <RouterLink :to="{ name: 'register' }" class="font-medium text-blue-600 hover:text-blue-500">
                Register
            </RouterLink>
        </p>
    </GuestLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useFlashStore } from '@/stores/flash'
import { useFormErrors } from '@/composables/useFormErrors'
import GuestLayout from '@/components/GuestLayout.vue'
import FormField from '@/components/FormField.vue'

const auth   = useAuthStore()
const flash  = useFlashStore()
const router = useRouter()
const route  = useRoute()

const { errors, clearErrors, handleError } = useFormErrors()
const loading = ref(false)

const form = reactive({ email: '', password: '', remember: false })

async function submit() {
    clearErrors()
    loading.value = true
    try {
        await auth.login(form)
        const redirect = route.query.redirect || '/dashboard'
        router.push(redirect)
    } catch (err) {
        handleError(err)
        if (!Object.keys(errors.value).length) {
            flash.error('Login failed. Please try again.')
        }
    } finally {
        loading.value = false
    }
}
</script>
