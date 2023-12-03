<script setup>
import { ref } from 'vue'
defineProps({
    customClass: {
        type: String,
        default: "",
    },
    have_button_close: {
        type: Boolean,
        default: false,
    },
})

const modal = ref(null)

function close() {
    HSOverlay.close(modal.value)
}

function open() {
    HSOverlay.open(modal.value)
}

defineExpose({ open, close })
</script>
<template>
    <div ref="modal" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[60] overflow-auto">
        <div :class="customClass"
            class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-[700px] sm:w-full m-3 sm:mx-auto">
            <div class="relative flex flex-col bg-white border shadow-sm rounded-xl">
                <div class="absolute top-2 end-2" v-if="have_button_close">
                    <button @click="close" type="button"
                        class="flex justify-center items-center w-7 h-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
                        <span class="sr-only">Close</span>
                        <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <slot v-bind="{ close }"></slot>
            </div>
        </div>
    </div>
</template>