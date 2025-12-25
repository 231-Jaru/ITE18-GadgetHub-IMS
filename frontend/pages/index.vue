<template>
  <div></div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'

// Redirect to dashboard if authenticated, otherwise to login
// @ts-ignore - Nuxt auto-imports
const authStore = useAuthStore()

onMounted(() => {
  authStore.initFromStorage()
  
  if (authStore.isAuthenticated) {
    // @ts-ignore - Nuxt auto-imports
    navigateTo('/dashboard')
  } else {
    // @ts-ignore - Nuxt auto-imports
    navigateTo('/login')
  }
})
</script>

