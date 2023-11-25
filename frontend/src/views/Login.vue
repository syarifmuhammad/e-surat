<script setup>
import { ref, reactive } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import axios from 'axios'
import Loading from '@/components/Loading.vue'
import { useAuthStore } from '@/stores/auth'
import { useUserStore } from '../stores/user'

const router = useRouter()

const url = import.meta.env.VITE_URL_API
const loading = ref(null)

const form = reactive({
    email: '',
    password: '',
})

function submit() {
    loading.value.open()
    axios.post(`${url}/login`, form).then(response => {
        useAuthStore().login(response.data.data.token)
        useUserStore().setUser(response.data.data.user)
        router.push({ name: 'dashboard' })
    }).catch(e => {
        console.log(e)
    }).finally(() => {
        loading.value.close()
    })
}
</script>
<template>
    <Loading ref="loading" />
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm max-w-md mx-auto">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800">Sign in</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Belum mempunyai akun ?
                    <RouterLink class="text-primary-500 decoration-2 hover:underline font-medium"
                        :to="{ name: 'register' }">
                        Buat akun disini
                    </RouterLink>
                </p>
            </div>

            <div class="mt-5">
                <form @submit.prevent="submit">
                    <div class="grid gap-y-4">
                        <div>
                            <label for="email" class="block text-sm mb-2">Email</label>
                            <div class="relative">
                                <input v-model="form.email" type="email" id="email" name="email" class="form-control"
                                    autocomplete="username" required>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center">
                                <label for="password" class="block text-sm mb-2">Password</label>
                                <a class="text-sm text-primary-500 decoration-2 hover:underline font-medium"
                                    href="../examples/html/recover-account.html">Lupa password?</a>
                            </div>
                            <div class="relative">
                                <input v-model="form.password" type="password" id="password" name="password"
                                    autocomplete="current-password" class="form-control" required>
                            </div>
                        </div>
                        <div class="flex items-center gap-x-2">
                            <input id="remember-me" type="checkbox"
                                class="border-gray-200 rounded text-primary-500 focus:ring-primary-500">
                            <label for="remember-me" class="text-sm">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary text-center justify-center py-3 font-semibold">Sign
                            in</button>
                    </div>
                </form>
                <!-- End Form -->
            </div>
        </div>
    </div>
</template>