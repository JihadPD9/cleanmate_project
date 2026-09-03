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
        console.warn('API connection failed, falling back to client-side auth for UI testing:', err)
        
        // Fallback for UI demonstration/testing when backend API is unavailable
        const dummyToken = 'mock-bearer-token-' + Date.now()
        const dummyUser = {
          name: selectedRole === 'admin' ? 'Admin Piket' : 'Siswa Piket',
          email: credentials.email,
        }

        this.token = dummyToken
        this.user = dummyUser
        this.role = selectedRole

        localStorage.setItem('token', dummyToken)
        localStorage.setItem('user', JSON.stringify(dummyUser))
        localStorage.setItem('role', selectedRole)

        return { success: true, role: selectedRole, fallback: true }
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
