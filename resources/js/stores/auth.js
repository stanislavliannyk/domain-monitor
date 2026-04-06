import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { authApi } from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const initialized = ref(false)

    const isAuthenticated = computed(() => user.value !== null)

    async function fetchUser() {
        try {
            const { data } = await authApi.getUser()
            user.value = data.data
        } catch {
            user.value = null
        } finally {
            initialized.value = true
        }
    }

    async function login(credentials) {
        const { data } = await authApi.login(credentials)
        user.value = data.data
    }

    async function register(payload) {
        const { data } = await authApi.register(payload)
        user.value = data.data
    }

    async function logout() {
        await authApi.logout()
        user.value = null
    }

    return { user, initialized, isAuthenticated, fetchUser, login, register, logout }
})
