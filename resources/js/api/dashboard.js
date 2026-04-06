import client from './client'

export const dashboardApi = {
    get() {
        return client.get('/dashboard')
    },
}
