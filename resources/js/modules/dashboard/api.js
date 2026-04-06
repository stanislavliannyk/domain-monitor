import client from '@/shared/api/client'

export const dashboardApi = {
    get() {
        return client.get('/dashboard')
    },
}
