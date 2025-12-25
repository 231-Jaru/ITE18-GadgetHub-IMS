// @ts-ignore - Nuxt auto-imports
export default defineNuxtRouteMiddleware((to, from) => {
  // @ts-ignore - Nuxt auto-imports
  const authStore = useAuthStore()
  
  // Initialize from storage if not already loaded
  if (!authStore.isAuthenticated) {
    authStore.initFromStorage()
  }

  // If not authenticated and trying to access protected route
  if (!authStore.isAuthenticated && to.path !== '/login') {
    // @ts-ignore - Nuxt auto-imports
    return navigateTo('/login')
  }

  // If authenticated and trying to access login page, redirect to dashboard
  if (authStore.isAuthenticated && to.path === '/login') {
    // @ts-ignore - Nuxt auto-imports
    return navigateTo('/dashboard')
  }
})

