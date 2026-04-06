<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-50 space-y-2 w-80">
            <TransitionGroup name="flash">
                <div
                    v-for="msg in flash.messages"
                    :key="msg.id"
                    :class="[
                        'flex items-start justify-between gap-2 rounded-lg px-4 py-3 shadow-lg text-sm',
                        msg.type === 'success'
                            ? 'bg-green-50 text-green-800 border border-green-200'
                            : 'bg-red-50 text-red-800 border border-red-200',
                    ]"
                >
                    <span>{{ msg.text }}</span>
                    <button
                        @click="flash.remove(msg.id)"
                        class="shrink-0 opacity-60 hover:opacity-100 text-lg leading-none"
                    >&times;</button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { useFlashStore } from '@/shared/stores/flash'

const flash = useFlashStore()
</script>

<style scoped>
.flash-enter-active, .flash-leave-active { transition: all 0.3s ease; }
.flash-enter-from { opacity: 0; transform: translateX(100%); }
.flash-leave-to   { opacity: 0; transform: translateX(100%); }
</style>
