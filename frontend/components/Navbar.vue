<template>
  <nav class="bg-gray-900 text-white shadow-lg">
    <div class="container-fluid mx-auto px-4">
      <div class="flex items-center justify-between h-16">
        <!-- Brand -->
        <NuxtLink to="/dashboard" class="flex items-center text-xl font-bold hover:text-indigo-300 transition">
          <i class="fas fa-store mr-2"></i>Gadgethub
        </NuxtLink>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-4">
          <NuxtLink
            to="/dashboard"
            class="px-3 py-2 rounded hover:bg-gray-800 transition"
            :class="{ 'bg-gray-800': route.path === '/dashboard' }"
          >
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
          </NuxtLink>
          <NuxtLink
            to="/gadgets"
            class="px-3 py-2 rounded hover:bg-gray-800 transition"
            :class="{ 'bg-gray-800': route.path.startsWith('/gadgets') }"
          >
            <i class="fas fa-mobile-alt mr-2"></i>Gadgets
          </NuxtLink>
          <NuxtLink
            to="/stocks"
            class="px-3 py-2 rounded hover:bg-gray-800 transition"
            :class="{ 'bg-gray-800': route.path.startsWith('/stocks') }"
          >
            <i class="fas fa-warehouse mr-2"></i>Stocks
          </NuxtLink>
          <NuxtLink
            to="/purchase-orders"
            class="px-3 py-2 rounded hover:bg-gray-800 transition"
            :class="{ 'bg-gray-800': route.path.startsWith('/purchase-orders') }"
          >
            <i class="fas fa-shopping-cart mr-2"></i>Purchase Orders
          </NuxtLink>
          <NuxtLink
            to="/suppliers"
            class="px-3 py-2 rounded hover:bg-gray-800 transition"
            :class="{ 'bg-gray-800': route.path.startsWith('/suppliers') }"
          >
            <i class="fas fa-truck mr-2"></i>Suppliers
          </NuxtLink>
          <NuxtLink
            to="/reports"
            class="px-3 py-2 rounded hover:bg-gray-800 transition"
            :class="{ 'bg-gray-800': route.path.startsWith('/reports') }"
          >
            <i class="fas fa-chart-bar mr-2"></i>Reports
          </NuxtLink>
        </div>

        <!-- User Menu -->
        <div class="relative">
          <button
            @click="showDropdown = !showDropdown"
            class="flex items-center px-3 py-2 rounded hover:bg-gray-800 transition"
          >
            <i class="fas fa-user-circle mr-2"></i>
            {{ userName }}
            <small class="ml-2 text-gray-400">({{ userType }})</small>
            <i class="fas fa-chevron-down ml-2 text-xs"></i>
          </button>

          <!-- Dropdown Menu -->
          <div
            v-if="showDropdown"
            class="absolute right-0 mt-2 w-56 bg-white text-gray-900 rounded-lg shadow-xl z-50"
            @click.stop
          >
            <div class="py-2">
              <div class="px-4 py-3 border-b">
                <div class="flex items-center">
                  <i class="fas fa-user-circle text-2xl mr-3 text-gray-600"></i>
                  <div>
                    <div class="font-semibold">{{ userName }}</div>
                    <small class="text-gray-500">{{ userType }}</small>
                  </div>
                </div>
              </div>
              
              <NuxtLink
                to="/dashboard"
                class="block px-4 py-2 hover:bg-gray-100 transition"
                @click="showDropdown = false"
              >
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
              </NuxtLink>
              <NuxtLink
                to="/gadgets"
                class="block px-4 py-2 hover:bg-gray-100 transition"
                @click="showDropdown = false"
              >
                <i class="fas fa-mobile-alt mr-2"></i>Gadgets
              </NuxtLink>
              <NuxtLink
                to="/stocks"
                class="block px-4 py-2 hover:bg-gray-100 transition"
                @click="showDropdown = false"
              >
                <i class="fas fa-warehouse mr-2"></i>Stocks
              </NuxtLink>
              <NuxtLink
                to="/purchase-orders"
                class="block px-4 py-2 hover:bg-gray-100 transition"
                @click="showDropdown = false"
              >
                <i class="fas fa-shopping-cart mr-2"></i>Purchase Orders
              </NuxtLink>
              <NuxtLink
                to="/suppliers"
                class="block px-4 py-2 hover:bg-gray-100 transition"
                @click="showDropdown = false"
              >
                <i class="fas fa-truck mr-2"></i>Suppliers
              </NuxtLink>
              <NuxtLink
                to="/reports"
                class="block px-4 py-2 hover:bg-gray-100 transition"
                @click="showDropdown = false"
              >
                <i class="fas fa-chart-bar mr-2"></i>Reports
              </NuxtLink>
              
              <div class="border-t my-2"></div>
              
              <button
                @click="handleLogout"
                class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition"
              >
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <button
          @click="showMobileMenu = !showMobileMenu"
          class="md:hidden px-3 py-2 rounded hover:bg-gray-800"
        >
          <i :class="showMobileMenu ? 'fas fa-times' : 'fas fa-bars'"></i>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div v-if="showMobileMenu" class="md:hidden py-4 border-t border-gray-700">
        <NuxtLink
          to="/dashboard"
          class="block px-3 py-2 rounded hover:bg-gray-800 transition"
          @click="showMobileMenu = false"
        >
          <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </NuxtLink>
        <NuxtLink
          to="/gadgets"
          class="block px-3 py-2 rounded hover:bg-gray-800 transition"
          @click="showMobileMenu = false"
        >
          <i class="fas fa-mobile-alt mr-2"></i>Gadgets
        </NuxtLink>
        <NuxtLink
          to="/stocks"
          class="block px-3 py-2 rounded hover:bg-gray-800 transition"
          @click="showMobileMenu = false"
        >
          <i class="fas fa-warehouse mr-2"></i>Stocks
        </NuxtLink>
        <NuxtLink
          to="/purchase-orders"
          class="block px-3 py-2 rounded hover:bg-gray-800 transition"
          @click="showMobileMenu = false"
        >
          <i class="fas fa-shopping-cart mr-2"></i>Purchase Orders
        </NuxtLink>
        <NuxtLink
          to="/suppliers"
          class="block px-3 py-2 rounded hover:bg-gray-800 transition"
          @click="showMobileMenu = false"
        >
          <i class="fas fa-truck mr-2"></i>Suppliers
        </NuxtLink>
        <NuxtLink
          to="/reports"
          class="block px-3 py-2 rounded hover:bg-gray-800 transition"
          @click="showMobileMenu = false"
        >
          <i class="fas fa-chart-bar mr-2"></i>Reports
        </NuxtLink>
        <button
          @click="handleLogout"
          class="w-full text-left px-3 py-2 rounded hover:bg-gray-800 transition text-red-400"
        >
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
// @ts-ignore - Nuxt auto-imports
const route = useRoute()
// @ts-ignore - Nuxt path alias
import { useAuth } from '~/composables/useAuth' // eslint-disable-line
// @ts-ignore - Nuxt path alias  
import { useAuthStore } from '~/stores/auth' // eslint-disable-line
const { logout } = useAuth()
const authStore = useAuthStore()

const showDropdown = ref(false)
const showMobileMenu = ref(false)

const userName = computed(() => authStore.userName)
const userType = computed(() => authStore.userType)

const handleLogout = async () => {
  showDropdown.value = false
  showMobileMenu.value = false
  await logout()
}

// Click outside handler
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

