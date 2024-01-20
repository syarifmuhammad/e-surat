<script setup>
import { ref } from 'vue'
import Modal from "@/components/Modal.vue"
import Loading from "@/components/Loading.vue"
import SearchInput from '@/components/SearchInput.vue';
import CustomSelect from '@/components/CustomSelect.vue';
import { Icon } from '@iconify/vue'

const props = defineProps({
    title: {
        type: String,
        default: 'Pilih Pegawai'
    }
})

const emit = defineEmits(["choosed"])

const url = import.meta.env.VITE_URL_API
const loading = ref(null)
const modal = ref(null)

const selected_employee = ref(null)
const position = ref('')

function reset_employee() {
    selected_employee.value = null
}

function reset_form() {
    reset_employee()
    position.value = ''
}

function open() {
    modal.value.open()
}
function close() {
    modal.value.close()
}

function closeAndReset() {
    reset_form()
    close()
}

function choose(e) {
    e.preventDefault()
    if (selected_employee.value && position.value) {
        emit('choosed', {
            employee: selected_employee.value,
            position: position.value
        })
        closeAndReset()
    }
}

defineExpose({ open, close })
</script>
<template>
    <Loading ref="loading" />
    <Modal ref="modal" class="[--overlay-backdrop:static]" customClass="mt-40 hs-overlay-open:mt-52"
        data-hs-overlay-keyboard="false">
        <form @submit="choose">
            <div class="p-4 pb-0 sm:p-10 sm:pb-0 h-full flex flex-col">
                <div class="text-center mb-6">
                    <h3 class="mb-2 text-xl font-bold text-gray-800">
                        {{ title }}
                    </h3>
                </div>
                <div class="overflow-auto max-h-[400px]">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Pegawai</label>
                        <search-input v-if="!selected_employee" v-model="selected_employee" :url="`${url}/employees`"
                            id="employee" placeholder="Cari Pegawai ...">
                            <template v-slot="{ data }">
                                <small>{{ data.nip }}</small>
                                <p class="mb-0">{{ data.name }}</p>
                                <hr class="m-0" />
                                <small class="mb-0">
                                    <template v-for="(position, i) in data.positions">
                                        {{ position }}
                                        <template v-if="i != data.positions.length - 1"> | </template>
                                    </template>
                                </small>
                            </template>
                        </search-input>
                        <div v-if="selected_employee"
                            class="form-control bg-primary-200/20 mt-2 flex justify-between items-center gap-x-4">
                            <div class="w-full">
                                <small>{{ selected_employee.nip }}</small>
                                <p class="mb-0">{{ selected_employee.name }}</p>
                            </div>
                            <span @click="reset_employee"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </div>
                    <div class="mb-4" v-if="selected_employee">
                        <label class="block text-sm font-medium mb-2">Pilih Jabatan Pegawai</label>
                        <custom-select :required="true" v-model="position" :data="selected_employee.positions"
                            placeholder="Jabatan pegawai"></custom-select>
                    </div>
                </div>
            </div>
            <div class="border-t p-4 sm:px-10 flex justify-end">
                <button type="button" class="btn btn-outline-primary px-14 py-3 mr-6" @click="closeAndReset">Batal</button>
                <button class="btn btn-primary px-14 py-3">Pilih</button>
            </div>
        </form>
    </Modal>
</template>