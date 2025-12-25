// @ts-ignore - Nuxt path alias
import { useAuthStore } from '~/stores/auth'
import { computed } from 'vue'

export const useAuth = () => {
  const authStore = useAuthStore()
  // @ts-ignore - Nuxt auto-imports
  const { api } = useApi()

  // Helper function to check if we're in the browser (client-side)
  const isClient = (): boolean => {
    return typeof window !== 'undefined' && typeof localStorage !== 'undefined'
  }

  const login = async (email: string, password: string) => {
    try {
      // @ts-ignore - Nuxt auto-imports
      const config = useRuntimeConfig()
      const apiBase = config.public.apiBase
      
      // Skip CSRF cookie - not needed for token-based authentication
      // Token-based auth uses Bearer tokens, not session cookies

      // Step 2: Make login request directly (token-based auth doesn't need CSRF)
      const response = await api('/login', {
        method: 'POST',
        body: {
          email,
          password
        }
      }) as any

      if (response.status === 'success' && response.token) {
        // Store token and user info
        if (isClient()) {
          localStorage.setItem('auth_token', response.token)
          localStorage.setItem('user_info', JSON.stringify(response.user_info))
        }
        
        // Update store
        authStore.setUser(response.user_info)
        authStore.setToken(response.token)
        
        return { success: true, data: response }
      } else {
        return { success: false, error: response.message || 'Login failed' }
      }
    } catch (error: any) {
      console.error('Login error:', error)
      
      // Handle network errors
      if (error.message?.includes('Failed to fetch') || error.message?.includes('NetworkError')) {
        return { 
          success: false, 
          error: 'Cannot connect to server. Please make sure Laravel is running on http://localhost:8000' 
        }
      }
      
      // Check if it's a CSRF error
      if (error.status === 419 || error.statusCode === 419 || error.message?.includes('CSRF')) {
        return { 
          success: false, 
          error: 'CSRF token mismatch. Please refresh the page and try again.' 
        }
      }
      
      return { 
        success: false, 
        error: error.data?.message || error.message || 'Login failed. Please check your credentials.' 
      }
    }
  }

  const register = async (username: string, password: string, role: string = 'Staff') => {
    try {
      const response = await api('/register', {
        method: 'POST',
        body: {
          Username: username,
          PasswordHash: password,
          Role: role
        }
      }) as any

      if (response.status === 'success' && response.token) {
        // Store token and user info
        if (isClient()) {
          localStorage.setItem('auth_token', response.token)
          localStorage.setItem('user_info', JSON.stringify(response.user_info))
        }
        
        // Update store
        authStore.setUser(response.user_info)
        authStore.setToken(response.token)
        
        return { success: true, data: response }
      } else {
        return { 
          success: false, 
          error: response.message || 'Registration failed',
          message: response.message,
          errors: response.errors
        }
      }
    } catch (error: any) {
      console.error('Registration error:', error)
      
      // Handle network errors
      if (error.message?.includes('Failed to fetch') || error.message?.includes('NetworkError')) {
        return { 
          success: false, 
          error: 'Cannot connect to server. Please make sure Laravel is running on http://localhost:8000',
          message: 'Cannot connect to server. Please make sure Laravel is running on http://localhost:8000'
        }
      }
      
      // Handle validation errors
      if (error.data?.errors) {
        return {
          success: false,
          error: error.data.message || 'Validation failed',
          message: error.data.message || 'Validation failed',
          errors: error.data.errors
        }
      }
      
      return { 
        success: false, 
        error: error.data?.message || error.message || 'Registration failed. Please try again.',
        message: error.data?.message || error.message || 'Registration failed. Please try again.',
        errors: error.data?.errors
      }
    }
  }

  const logout = async () => {
    try {
      await api('/logout', {
        method: 'POST'
      })
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      // Clear local storage
      if (isClient()) {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_info')
      }
      
      // Clear store
      authStore.clearUser()
      authStore.clearToken()
      
      // Redirect to login
      // @ts-ignore - Nuxt auto-imports
      navigateTo('/login')
    }
  }

  const checkAuth = async () => {
    try {
      const response = await api('/get-user', {
        method: 'GET'
      }) as any

      if (response.status === 'success' && response.user_info) {
        authStore.setUser(response.user_info)
        return true
      }
      return false
    } catch (error) {
      console.error('Auth check error:', error)
      return false
    }
  }

  const isAuthenticated = computed(() => {
    return authStore.isAuthenticated
  })

  const user = computed(() => {
    return authStore.user
  })

  return {
    login,
    register,
    logout,
    checkAuth,
    isAuthenticated,
    user
  }
}

