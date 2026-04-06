import client from './client'

export const domainsApi = {
    list(page = 1) {
        return client.get('/domains', { params: { page } })
    },

    get(id) {
        return client.get(`/domains/${id}`)
    },

    create(payload) {
        return client.post('/domains', payload)
    },

    update(id, payload) {
        return client.put(`/domains/${id}`, payload)
    },

    remove(id) {
        return client.delete(`/domains/${id}`)
    },

    checkNow(id) {
        return client.post(`/domains/${id}/check-now`)
    },

    logs(id, page = 1) {
        return client.get(`/domains/${id}/logs`, { params: { page } })
    },
}
