import { ref } from 'vue'

/**
 * Extracts Laravel validation errors (422) into a reactive object.
 * Usage:
 *   const { errors, clearErrors, handleError } = useFormErrors()
 *   errors.value.email   // "The email has already been taken."
 */
export function useFormErrors() {
    const errors = ref({})

    function clearErrors() {
        errors.value = {}
    }

    function handleError(err) {
        if (err?.response?.status === 422) {
            errors.value = err.response.data.errors ?? {}
        } else {
            throw err
        }
    }

    return { errors, clearErrors, handleError }
}
