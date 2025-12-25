export const useApi = () => {
  // @ts-ignore - Nuxt auto-imports
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  // Helper function to check if we're in the browser (client-side)
  const isClient = (): boolean => {
    return typeof window !== 'undefined' && typeof localStorage !== 'undefined'
  }

  // Get auth token from localStorage
  const getToken = (): string | null => {
    if (isClient()) {
      return localStorage.getItem('auth_token')
    }
    return null
  }

  // API wrapper function
  const api = async (url: string, options: any = {}) => {
    const token = getToken()
    
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers
    }

    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    try {
      // @ts-ignore - Nuxt auto-imports
      const response = await $fetch(apiBase + url, {
        ...options,
        headers,
        // Don't use credentials for token-based auth (avoids CORS issues)
        // credentials: 'include', // Only needed for session-based auth
        mode: 'cors', // Explicitly set CORS mode
      })
      return response
    } catch (error: any) {
      // Handle network errors
      if (error.message?.includes('Failed to fetch') || error.message?.includes('NetworkError')) {
        console.error('Network error - is Laravel running?', apiBase + url)
        throw new Error('Cannot connect to server. Please make sure Laravel is running on http://localhost:8000')
      }
      
      // Handle 401 Unauthorized - redirect to login
      if (error.status === 401 || error.statusCode === 401) {
        if (isClient()) {
          localStorage.removeItem('auth_token')
          localStorage.removeItem('user_info')
          // @ts-ignore - Nuxt auto-imports
          navigateTo('/login')
        }
      }
      throw error
    }
  }

  return {
    api,
    apiBase
  }
}

