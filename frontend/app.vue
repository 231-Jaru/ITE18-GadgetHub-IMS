<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
// @ts-ignore - Nuxt path alias
import { useAuthStore } from '~/stores/auth'

const authStore = useAuthStore()

// Initialize auth from localStorage on app mount
onMounted(() => {
  authStore.initFromStorage()
})
</script>

