<template>
    <form @submit.prevent="$emit('submit', form)" class="space-y-6">

        <!-- Основная информация -->
        <div class="grid gap-5 sm:grid-cols-2">
            <FormField id="name" label="Название" :error="errors.name?.[0]" required>
                <input id="name" v-model="form.name" type="text" maxlength="100" required
                       placeholder="Мой сайт"
                       :class="['form-input mt-1', errors.name ? 'form-input-error' : '']" />
            </FormField>

            <FormField id="url" label="URL" :error="errors.url?.[0]" required>
                <input id="url" v-model="form.url" type="url" maxlength="2048" required
                       placeholder="https://example.com"
                       :class="['form-input mt-1', errors.url ? 'form-input-error' : '']" />
            </FormField>
        </div>

        <!-- Настройки проверок -->
        <div class="rounded-lg border border-gray-200 p-5 space-y-5">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Настройки проверок</h3>

            <div class="grid gap-5 sm:grid-cols-3">
                <FormField id="check_interval" label="Интервал (минуты)" :error="errors.check_interval?.[0]">
                    <select id="check_interval" v-model="form.check_interval" class="form-input mt-1">
                        <option v-for="m in [1, 5, 10, 15, 30, 60]" :key="m" :value="m">
                            {{ m }} {{ pluralMin(m) }}
                        </option>
                    </select>
                </FormField>

                <FormField id="request_timeout" label="Таймаут (секунды)" :error="errors.request_timeout?.[0]">
                    <select id="request_timeout" v-model="form.request_timeout" class="form-input mt-1">
                        <option v-for="s in [5, 10, 15, 20, 30, 60]" :key="s" :value="s">{{ s }} с</option>
                    </select>
                </FormField>

                <FormField id="check_method" label="HTTP-метод"
                           hint="HEAD быстрее и потребляет меньше трафика.">
                    <select id="check_method" v-model="form.check_method" class="form-input mt-1">
                        <option value="HEAD">HEAD</option>
                        <option value="GET">GET</option>
                    </select>
                </FormField>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="form.is_active" type="checkbox"
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span class="text-sm text-gray-700">Включить мониторинг для этого домена</span>
            </label>
        </div>

        <!-- Уведомления -->
        <div class="rounded-lg border border-gray-200 p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Уведомления</h3>

            <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="form.notify_on_failure" type="checkbox"
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span class="text-sm text-gray-700">Отправить email, когда домен станет недоступен</span>
            </label>

            <Transition name="slide">
                <FormField v-if="form.notify_on_failure"
                           id="notification_email" label="Email для уведомлений"
                           :error="errors.notification_email?.[0]"
                           hint="Оставьте пустым, чтобы использовать email вашей учётной записи.">
                    <input id="notification_email" v-model="form.notification_email"
                           type="email" placeholder="ops@example.com"
                           :class="['form-input mt-1', errors.notification_email ? 'form-input-error' : '']" />
                </FormField>
            </Transition>
        </div>

        <!-- Кнопки -->
        <div class="flex items-center gap-3">
            <button type="submit" :disabled="loading" class="btn-primary">
                <span v-if="loading">Сохранение…</span>
                <span v-else>{{ submitLabel }}</span>
            </button>
            <button type="button" @click="$emit('cancel')" class="text-sm text-gray-500 hover:text-gray-700">
                Отмена
            </button>
        </div>
    </form>
</template>

<script setup>
import { reactive, watch } from 'vue'
import FormField from '@/components/FormField.vue'

const props = defineProps({
    initial: {
        type: Object,
        default: () => ({
            name:               '',
            url:                '',
            check_interval:     5,
            request_timeout:    10,
            check_method:       'HEAD',
            is_active:          true,
            notify_on_failure:  false,
            notification_email: '',
        }),
    },
    errors:      { type: Object,  default: () => ({}) },
    loading:     { type: Boolean, default: false },
    submitLabel: { type: String,  default: 'Сохранить' },
})

defineEmits(['submit', 'cancel'])

const form = reactive({ ...props.initial })

watch(() => props.initial, (val) => Object.assign(form, val), { deep: true })

function pluralMin(n) {
    if (n === 1) return 'минута'
    if (n >= 2 && n <= 4) return 'минуты'
    return 'минут'
}
</script>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: all 0.2s ease; }
.slide-enter-from, .slide-leave-to       { opacity: 0; transform: translateY(-6px); }
</style>
