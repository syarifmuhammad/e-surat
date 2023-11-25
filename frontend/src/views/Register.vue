<script setup>
import { ref, reactive } from 'vue'
import axios from 'axios'
import Loading from '@/components/Loading.vue'
import { RouterLink, useRouter } from 'vue-router';

const router = useRouter()
const url = 'http://localhost:8000/api'
const loading = ref(null)

const form = reactive({
    email: '',
    password: '',
    password_confirmation: '',
})

const form_errors = reactive({
    email: null,
    password: null,
    password_confirmation: null,
})

function submit() {
    if (form.password !== form.password_confirmation) {
        form_errors.password_confirmation = 'Konfirmasi password tidak sesuai'
        return
    }

    loading.value.open()
    axios.post(`${url}/register`, form).then(response => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil mendaftar',
            text: 'Silahkan login pada halaman berikut !',
        }).then(() => {
            router.push({ name: 'login' })
        })
    }).catch(error => {
        if (error.response.status === 422) {
            form_errors.email = error.response.data.errors.email[0] ?? ""
            form_errors.password = error.response.data.errors.password[0] ?? ""
            form_errors.password_confirmation = error.response.data.errors.password_confirmation[0] ?? ""
        }
    }).finally(() => {
        loading.value.close()
    })
}

</script>
<template>
    <Loading ref="loading"></Loading>
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm max-w-md mx-auto">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800">Sign Up</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Sudah mempunyai akun ?
                    <RouterLink class="text-primary-500 decoration-2 hover:underline font-medium" :to="{ name: 'login' }">
                        Login disini
                    </RouterLink>
                </p>
            </div>

            <div class="mt-5">
                <form @submit.prevent="submit">
                    <div class="grid gap-y-4">
                        <div>
                            <label for="email" class="block text-sm mb-2">Email</label>
                            <div class="relative">
                                <input @input="form_errors.email = ''" v-model="form.email" type="email" id="email"
                                    name="email" class="form-control" :class="{ 'border-red-500': form_errors.email }"
                                    autocomplete="username" required>
                            </div>
                            <p v-if="form_errors.email" class="text-xs text-red-600 mt-2" id="email-error">{{
                                form_errors.email }}</p>
                        </div>
                        <div>
                            <div class="flex justify-between items-center">
                                <label for="password" class="block text-sm mb-2">Password</label>
                            </div>
                            <div class="relative">
                                <input @input="form_errors.password = ''" v-model="form.password" type="password"
                                    id="password" name="password" autocomplete="current-password" class="form-control"
                                    :class="{ 'border-red-500': form_errors.password }" required>
                            </div>
                            <p v-if="form_errors.password" class="text-xs text-red-600 mt-2" id="password-error">{{
                                form_errors.password }}</p>
                        </div>
                        <div>
                            <div class="flex justify-between items-center">
                                <label for="confirm-password" class="block text-sm mb-2">Konfirmasi Password</label>
                            </div>
                            <div class="relative">
                                <input @input="form_errors.password_confirmation = ''" v-model="form.password_confirmation"
                                    type="password" id="confirm-password" name="confirm-password"
                                    autocomplete="current-password" class="form-control"
                                    :class="{ 'border-red-500': form_errors.password_confirmation }" required>
                            </div>
                            <p v-if="form_errors.password_confirmation" class="text-xs text-red-600 mt-2"
                                id="password-confirm-error">{{ form_errors.password_confirmation }}</p>
                        </div>

                        <!-- Checkbox -->
                        <!-- <div class="flex items-center gap-x-2">
                            <input id="remember-me" type="checkbox"
                                class="border-gray-200 rounded text-primary-500 focus:ring-primary-500">
                            <label for="remember-me" class="text-sm">Remember me</label>
                        </div> -->
                        <!-- End Checkbox -->

                        <button type="submit" class="btn btn-primary text-center justify-center py-3 font-semibold">Daftar
                            Sekarang</button>
                    </div>
                </form>
                <!-- End Form -->
            </div>
    </div>
</div></template>