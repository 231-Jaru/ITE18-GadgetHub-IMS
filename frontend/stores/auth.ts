import { defineStore } from 'pinia'

interface UserInfo {
  id: number
  name: string
  username: string
  role: string
  type: string
}

// Helper function to check if we're in the browser (client-side)
const isClient = (): boolean => {
  return typeof window !== 'undefined' && typeof localStorage !== 'undefined'
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as UserInfo | null,
    token: null as string | null
  }),

  getters: { 
    isAuthenticated: (state) => {
      if (isClient()) {
        // Check both store and localStorage
        return !!(state.token || localStorage.getItem('auth_token'))
      }
      return !!state.token
    },
    userName: (state) => state.user?.name || state.user?.username || 'User',
    userRole: (state) => state.user?.role || 'user',
    userType: (state) => state.user?.type || 'user'
  },

  actions: {
    setUser(user: UserInfo) {
      this.user = user
      if (isClient()) {
        localStorage.setItem('user_info', JSON.stringify(user))
      }
    },

    setToken(token: string) {
      this.token = token
      if (isClient()) {
        localStorage.setItem('auth_token', token)
      }
    },

    clearUser() {
      this.user = null
      this.token = null
      if (isClient()) {
        localStorage.removeItem('user_info')
        localStorage.removeItem('auth_token')
      }
    },

    clearToken() {
      this.token = null
      if (isClient()) {
        localStorage.removeItem('auth_token')
      }
    },

    // Initialize from localStorage (call this on app mount)
    initFromStorage() {
      if (isClient()) {
        const token = localStorage.getItem('auth_token')
        const userInfo = localStorage.getItem('user_info')
        
        if (token) {
          this.token = token
        }
        
        if (userInfo) {
          try {
            this.user = JSON.parse(userInfo)
          } catch (e) {
            console.error('Failed to parse user info:', e)
          }
        }
      }
    }
  }
})

