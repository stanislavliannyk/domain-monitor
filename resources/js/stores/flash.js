import { defineStore } from 'pinia'
import { ref } from 'vue'

let _nextId = 0

export const useFlashStore = defineStore('flash', () => {
    const messages = ref([])

    function add(type, text, ttl = 4000) {
        const id = ++_nextId
        messages.value.push({ id, type, text })
        setTimeout(() => remove(id), ttl)
    }

    function success(text) { add('success', text) }
    function error(text)   { add('error', text) }

    function remove(id) {
        messages.value = messages.value.filter(m => m.id !== id)
    }

    return { messages, success, error, remove }
})
