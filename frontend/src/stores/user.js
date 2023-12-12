import { reactive } from 'vue'
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', () => {

  const user = reactive({
    name: '',
    email: '',
    roles: '',
    is_verified: false,
  })

  function is_empty() {
    return user.email === '' && user.roles === ''
  }

  function setUser(user_args) {
    user.name = user_args.name
    user.email = user_args.email
    user.roles = user_args.roles
    user.is_verified = user_args.is_verified
  }

  function clearUser() {
    user.name = ''
    user.email = ''
    user.roles = ''
  }

  return { user, setUser, clearUser, is_empty }
})
