<script setup>
import { Icon } from '@iconify/vue'


</script>
<template>
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm mx-auto max-w-xl">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800">Verifikasi email anda</h1>
            </div>

            <div class="mt-5">
                <p class="text-sm text-center text-gray-600">Cek email anda dan klik link untuk verifikasi akun anda.</p>
                <Icon class="w-40 h-40 mx-auto text-primary-500" icon="mdi:email-check"></Icon>
                <p class="text-center">Terkirim ke email : syarifmuhammad369@gmail.com</p>
            </div>
            <div class="flex justify-center gap-x-4 mt-4">
                <!-- <button class="btn btn-primary" disabled>Kirim Ulang, dalam 09:00 menit</button> -->
                <button class="btn btn-primary">Kirim Ulang</button>
            </div>
        </div>
    </div>
</template>