
import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import('preline');
import Loading from '@/components/Loading.vue'
import './assets/main.css'
import VueApexCharts from "vue3-apexcharts";

import Swal from 'sweetalert2'
window.Swal = Swal.mixin({
    confirmButtonColor: '#AF5660',
    cancelButtonColor: '#ef4444',
    reverseButtons: true,
})

import axios from 'axios'
const url = import.meta.env.VITE_URL_API

axios.interceptors.request.use(function (config) {
    const token = localStorage.getItem(import.meta.env.VITE_TOKEN_NAME)
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

//make sure if response 401, logout this application
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status === 401) {
            localStorage.removeItem(import.meta.env.VITE_TOKEN_NAME)

        }
        return Promise.reject(error)
    }
)

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(VueApexCharts);

app.mount('#app')
app.component('Loading', Loading)
app.config.globalProperties.$axios = axios
