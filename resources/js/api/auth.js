import client, { http } from './client'

export const authApi = {
    /** Must be called before login/register to initialise CSRF protection. */
    async csrfCookie() {
        await http.get('/sanctum/csrf-cookie')
    },

    async register(payload) {
        await this.csrfCookie()
        return client.post('/auth/register', payload)
    },

    async login(payload) {
        await this.csrfCookie()
        return client.post('/auth/login', payload)
    },

    async logout() {
        return client.post('/auth/logout')
    },

    async getUser() {
        return client.get('/auth/user')
    },
}
