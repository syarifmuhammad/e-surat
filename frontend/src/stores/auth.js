import { ref } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from '@/stores/user'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {

  const router = useRouter()

  const name = import.meta.env.VITE_TOKEN_NAME

  const token = ref(localStorage.getItem(name) ?? "")

  function login(token_args) {
    localStorage.setItem(name, token_args)
    token.value = token_args
  }

  function logout() {
    localStorage.removeItem(name)
    token.value = ''
    useUserStore().clearUser()
    router.push({ name: 'login' })
  }

  return { token, login, logout }
})
