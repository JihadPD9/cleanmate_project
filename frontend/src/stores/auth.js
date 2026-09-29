import { defineStore } from 'pinia'
import api from '@/utils/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    role: localStorage.getItem('role') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.role === 'admin',
    isSiswa: (state) => state.role === 'siswa',
  },

  actions: {
    async login(credentials, selectedRole = 'siswa') {
      this.loading = true
      this.error = null

      try {
        const endpoint = selectedRole === 'admin' ? '/admin/login' : '/siswa/login'
        const response = await api.post(endpoint, credentials)
        
        const data = response.data
        const token = data.token || data.access_token || 'dummy-jwt-token-12345'
        const user = data.user || { name: selectedRole === 'admin' ? 'Admin Piket' : credentials.email, email: credentials.email }
        
        this.token = token
        this.user = user
        this.role = selectedRole

        localStorage.setItem('token', token)
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('role', selectedRole)

        return { success: true, role: selectedRole }
      } catch (err) {
        const errorMsg = err.response?.data?.message || err.response?.data?.error || 'Email atau password salah.'
        this.error = errorMsg
        return { success: false, error: errorMsg }
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.token = null
      this.user = null
      this.role = null

      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('role')
    },
  },
})
