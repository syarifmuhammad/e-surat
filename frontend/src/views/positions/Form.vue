<script setup>
import { reactive, ref } from 'vue'
import SubHeader from '@/components/SubHeader.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'

const url = import.meta.env.VITE_URL_API

const form = reactive({
    id: "",
    name: "",
})

const errors = reactive({
    id: "",
    name: "",
})

const loading = ref(null)

function reset_errors() {
    errors.id = ""
    errors.name = ""

}

function reset() {
    form.id = ""
    form.name = ""
    reset_errors()
}

function save() {
    loading.value.open()
    axios.post(`${url}/positions`, form).then((res) => {
        if (res.status == 201) {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.data.message,
            });
            reset()
        }
    }).catch(e => {
        if (e.response.status == 422) {
            errors.id = e.response.data.errors.id[0]
            errors.name = e.response.data.errors.name[0]
        } else {
            console.log(e)
        }
    }).finally(() => {
        loading.value.close()
    })
}

</script>

<template>
    <loading ref="loading"></loading>
    <SubHeader :title="`Tambah Jabatan`"></SubHeader>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <form @submit.prevent="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama <span class="text-red-400">*</span></label>
                    <input v-model="form.name" type="text" class="form-control" :class="{ 'border-red-500': errors.name }"
                        placeholder="Nama" required>
                    <p v-if="errors.name" class="text-xs text-red-600 mt-2" id="name-error">{{
                        errors.name }}</p>
                </div>
                <div class="flex justify-end">
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped></style>