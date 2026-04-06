import axios from 'axios'

const client = axios.create({
    baseURL: '/api',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
})

/** Expose raw axios for the CSRF cookie request (needs full base URL). */
export const http = axios.create({
    withCredentials: true,
})

export default client
