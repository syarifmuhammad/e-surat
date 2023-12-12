<script setup>
import { ref } from 'vue'
import Modal from "@/components/Modal.vue"

const modal = ref(null)
const is_active = ref(false)

function open() {
    if (is_active.value) return
    is_active.value = true
    modal.value.open()
}
function close() {
    if (!is_active.value) return
    is_active.value = false
    modal.value.close()
}

defineExpose({ open, close })
</script>
<template>
    <Modal ref="modal" class="[--overlay-backdrop:static]" customClass="mt-40 hs-overlay-open:mt-52" data-hs-overlay-keyboard="false">
        <div class="px-8 py-5 text-center">
            <span
                class="animate-spin inline-block w-16 aspect-square border-[8px] border-primary-300 border-current border-t-primary-500 text-white rounded-full"
                role="status" aria-label="loading">
                <span class="sr-only">Loading...</span>
            </span>
            <p class="text-primary-500 mt-1">Loading ...</p>
        </div>
    </Modal>
</template>