<script setup>
import { reactive } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue';
import Modal from '@/components/Modal.vue';
import axios from 'axios'

const url = 'https://9056e12e-0ee3-4a34-aa6e-778790fa873f.mock.pstmn.io/api'

const thead = [
    "",
    "#",
    "Nama",
    "Jumlah Surat",
    "Aksi",
]

const form_category = reactive({
    id: "",
    name: '',
    show: true,
})

function open_modal_form_category(form_category_args = null) {
    if (form_category_args) {
        form_category.id = form_category_args.id
        form_category.name = form_category_args.name
        form_category.show = form_category_args.show
    }

    const modal = document.getElementById('form_category')
    HSOverlay.open(modal)
}

function save_category() {
    if (form_category.id.trim()) {
        // update
    } else {
        // create
        let payload = {
            name: form_category.name,
            show: form_category.show,
        }

        axios.post(`${url}/incoming-letter/categories`, payload)
            .then(res => {
                console.log(res)
            })
            .catch(err => {
                console.log(err)
            })
    }
}


</script>

<template>
    <SubHeader :title="`Kategori Surat Masuk`">
        <button type="button" class="btn btn-outline-primary inline-flex gap-2 justify-center items-center">
            Kategori Surat Masuk
            <Icon class="text-lg" icon="fe:sync" />
        </button>
    </SubHeader>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Kategori Surat Masuk</h3>
                <button @click="open_modal_form_category()" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Kategori
                </button>
            </div>
            <CustomTable :thead="thead" :url="`${url}/incoming-letter/categories`" v-slot="item">
                <td :class="[item.defaultClass]">
                    <button v-if="item.show" class="btn btn-outline-danger" title="Jangan tampilkan kategori berikut">
                        <Icon class="text-lg" icon="tabler:eye-off" />
                    </button>
                    <button v-else class="btn btn-outline-info" title="Tampilkan kategori berikut">
                        <Icon class="text-lg" icon="tabler:eye" />
                    </button>
                </td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.key }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.name }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.quantity }}</td>
                <td :class="[item.defaultClass]">
                    <button class="btn btn-info">
                        <Icon class="text-lg" icon="cil:pencil" />
                    </button>
                    <button class="btn btn-danger" v-if="item.quantity < 1">
                        <Icon class="text-lg" icon="jam:trash" />
                    </button>
                </td>
            </CustomTable>
        </div>
        <Modal id="form_category">
            <form @submit.prevent="save_category">
                <div class="p-4 sm:p-10 overflow-y-auto">
                    <div class="text-center mb-6">
                        <h3 class="mb-2 text-xl font-bold text-gray-800">
                            Form Tambah Kategori
                        </h3>
                        <p class="text-gray-500">
                            Tambahkan kategori surat dengan mengisi form dibawah ini
                        </p>
                    </div>
                    <div class="mb-4">
                        <label for="input-label" class="block text-sm font-medium mb-2">Kategori</label>
                        <input v-model="form_category.name" type="text" id="input-label" class="form-control" placeholder="Cth: Surat Undangan">
                    </div>
                    <div class="mb-4">
                        <label for="input-label" class="block text-sm font-medium mb-2">Tampilkan Kategori ?</label>
                        <div class="flex items-center">
                            <input v-model="form_category.show" type="checkbox" id="hs-basic-with-description" class="form-switch">
                        </div>
                    </div>
                </div>
                <div class="border-t p-4 sm:px-10 flex justify-end">
                    <button class="btn btn-primary px-14 py-3">Simpan</button>
                </div>
            </form>
        </Modal>
    </div>
</template>

<style scoped>
td.disabled {
    opacity: .5;
}

td.disabled:hover {
    background-color: initial !important;
}
</style>