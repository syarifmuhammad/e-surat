<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import UploadFile from '@/components/UploadFile.vue';
import Loading from '@/components/Loading.vue';
import { useRoute } from 'vue-router';

const route = useRoute()

const url = import.meta.env.VITE_URL_API
//ref element
const loading = ref(null)
const file = ref(null)


// ref data
const name = ref("")
const letter_type = ref("")
const default_files = ref(null)
const template_id = ref("")
const is_edit_file = ref(false)

const errors = reactive({
    name: "",
    letter_type: "",
    file: "",
})

function reset_form() {
    name.value = ""
    letter_type.value = ""
    file.value.reset()
    default_files.value = null
    template_id.value = ""
    is_edit_file.value = false

}

function base64ToFile(data, filename) {
    let arr = data
    let mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    let bstr = atob(arr)
    let n = bstr.length
    let u8arr = new Uint8Array(n)
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n)
    }
    return new File([u8arr], filename, { type: mime })
}

async function get_template() {
    await axios.get(`${url}/outcoming-letters/templates/${route.params.id}`).then(res => {
        name.value = res.data.name
        template_id.value = res.data.id
        letter_type.value = res.data.letter_type
        default_files.value = base64ToFile(res.data.file, `${res.data.id}-${res.data.name}-${res.data.letter_type}.docx`)
    })
}

function download(id, name, letter_type) {
    loading.value.open()
    axios.get(`${url}/outcoming-letters/templates/${id}/download`, {
        responseType: 'blob',
    }).then(res => {
        const url = window.URL.createObjectURL(new Blob([res.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `${id}-${name}-${letter_type}.docx`)
        document.body.appendChild(link)
        link.click()
    }).finally(() => {
        loading.value.close()
    })
}

function download_example(letter_type) {
    loading.value.open()
    axios.get(`${url}/outcoming-letters/templates/download_example?letter_type=${letter_type}`, {
        responseType: 'blob',
    }).then(res => {
        const url = window.URL.createObjectURL(new Blob([res.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `${letter_type}.docx`)
        document.body.appendChild(link)
        link.click()
    }).finally(() => {
        loading.value.close()
    })
}

function save() {
    if (!file.value.files) {
        Swal.fire({
            title: 'Gagal!',
            text: 'File tidak boleh kosong!',
            icon: 'error',
        })
        return
    }
    loading.value.open()
    let request = new FormData()
    request.append('name', name.value)
    request.append('letter_type', letter_type.value)
    request.append('file', file.value.files)

    if (template_id.value != "") {
        update(request)
    } else {
        create(request)
    }
}

function create(request) {
    axios.post(`${url}/outcoming-letters/templates`, request).then(res => {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Template berhasil ditambahkan!',
            icon: 'success',
        })
        reset_form()
    }).catch(err => {
        if (err.response.status == 422) {
            Object.entries(err.response.data.errors).forEach(([key, value]) => {
                errors[key] = value[0]
            })
        }
        Swal.fire({
            title: 'Gagal!',
            text: 'Template gagal ditambahkan!',
            icon: 'error',
        })
    }).finally(() => {
        loading.value.close()
    })

}

function update(request) {
    request.append('_method', 'PUT')
    axios.post(`${url}/outcoming-letters/templates/${template_id.value}`, request).then(res => {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Template berhasil diedit!',
            icon: 'success',
        })
    }).catch(err => {
        if (err.response.status == 422) {
            Object.entries(err.response.data.errors).forEach(([key, value]) => {
                errors[key] = value[0]
            })
        }
        Swal.fire({
            title: 'Gagal!',
            text: 'Template gagal diedit!',
            icon: 'error',
        })
    }).finally(() => {
        is_edit_file.value = false
        loading.value.close()
    })
}

onMounted(async () => {
    if (route.name == 'update_letter_templates') {
        loading.value.open()
        await get_template(route.params.id)
        loading.value.close()
    }
})

</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader :title="route.name == 'update_letter_templates' ? `Edit Template Surat` : `Tambah Template Surat`"
        :back_url="{ name: 'letter_templates' }" />
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <button type="button" class="btn btn-primary mb-8" @click="download(template_id, name, letter_type)" v-if="template_id != ''">Download Template Sebelumnya</button>
            <form @submit.prevent="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Kategori Surat Keluar</label>
                    <select class="form-control" required v-model="letter_type">
                        <option value="SURAT_KETERANGAN_KERJA">Surat Keterangan Kerja</option>
                        <option value="SURAT_KEPUTUSAN_ROTASI_KEPEGAWAIAN">Surat Keputusan Rotasi Kepegawaian</option>
                        <option value="SURAT_KEPUTUSAN_PEMBERHENTIAN">Surat Keputusan Pemberhentian</option>
                        <option value="SURAT_KEPUTUSAN_PENGANGKATAN">Surat Keputusan Pengangkatan</option>
                        <option value="SURAT_KEPUTUSAN_PEMBERHENTIAN_DAN_PENGANGKATAN">Surat Keputusan Pemberhentian Dan Pengangkatan</option>
                        <option value="SURAT_PERJANJIAN_KERJA_MAGANG">Surat Perjanjian Kerja Magang</option>
                        <option value="SURAT_PERJANJIAN_KERJA_DOSEN_LUAR_BIASA">Surat Perjanjian Kerja Dosen Luar Biasa</option>
                        <option value="SURAT_PERJANJIAN_KERJA_DOSEN_FULL_TIME">Surat Perjanjian Kerja Dosen Full Time</option>
                    </select>
                    <p class="text-red-400 text-sm" v-if="errors.letter_type != ''">{{ errors.letter_type }}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Template</label>
                    <input class="form-control" required v-model="name"
                    placeholder="Nama template surat" />
                    <p class="text-red-400 text-sm" v-if="errors.name != ''">{{ errors.name }}</p>
                </div>
                <label class="block text-sm font-medium mb-2">File Template Surat (.docx)</label>
                <button type="button" v-if="letter_type != ''" @click="download_example(letter_type)" class="btn btn-primary mb-2">Download Contoh Template</button>
                <div class="mb-4 rounded-md overflow-hidden" style="box-shadow: 0 0.25rem 1rem #a1acb873;">
                    <UploadFile ref="file" :default_files="default_files" @updated_files="is_edit_file = true"
                        :accepted_file_type="['application/vnd.openxmlformats-officedocument.wordprocessingml.document']">
                    </UploadFile>
                    <p class="text-red-400 text-sm" v-if="errors.file != ''">{{ errors.file }}</p>
                </div>
                <div class="flex justify-end">
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</template>