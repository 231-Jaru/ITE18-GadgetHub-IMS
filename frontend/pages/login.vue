<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-200">
    <div class="max-w-md w-full mx-4">
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-8 text-center relative overflow-hidden">
          <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-32 -mt-32"></div>
          <i class="fas fa-warehouse text-5xl mb-4 relative z-10"></i>
          <h2 class="text-2xl font-bold mb-2 relative z-10">Inventory System</h2>
          <p class="text-indigo-100 relative z-10">Admin Login</p>
        </div>

        <!-- Body -->
        <div class="p-8">
          <!-- Error Alert -->
          <div v-if="error" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <div class="flex items-center">
              <i class="fas fa-exclamation-triangle mr-2"></i>
              <span>{{ error }}</span>
            </div>
          </div>

          <!-- Success Alert -->
          <div v-if="success" class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
            <div class="flex items-center">
              <i class="fas fa-check-circle mr-2"></i>
              <span>{{ success }}</span>
            </div>
          </div>

          <!-- Login Form -->
          <form @submit.prevent="handleLogin" class="space-y-6">
            <!-- Username Field -->
            <div>
              <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-user text-indigo-600 mr-2"></i>Username
              </label>
              <input
                id="email"
                name="email"
                v-model="form.email"
                type="text"
                required
                autofocus
                autocomplete="username"
                maxlength="255"
                placeholder="Enter your username"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 outline-none transition"
                :class="{ 'border-red-500': errors.email }"
              />
              <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
              <small class="text-gray-500 text-xs mt-1 block">Enter your admin username to access the system</small>
            </div>

            <!-- Password Field -->
            <div>
              <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-lock text-indigo-600 mr-2"></i>Password
              </label>
              <div class="relative">
                <input
                  id="password"
                  name="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autocomplete="current-password"
                  placeholder="Enter your password"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 outline-none transition pr-12"
                  :class="{ 'border-red-500': errors.password }"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
              <small class="text-gray-500 text-xs mt-1 block">Enter your password to login</small>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-indigo-600 to-indigo-800 text-white py-3 px-6 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none"
            >
              <span v-if="loading" class="inline-block">
                <i class="fas fa-spinner fa-spin mr-2"></i>Logging in...
              </span>
              <span v-else>
                <i class="fas fa-sign-in-alt mr-2"></i>Log In
              </span>
            </button>
          </form>

          <!-- Register Link -->
          <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
              Don't have an account?
              <NuxtLink to="/register" class="text-indigo-600 hover:text-indigo-800 font-semibold transition">
                Register here
              </NuxtLink>
            </p>
          </div>

          <!-- Info Box -->
          <div class="mt-6 p-4 bg-blue-50 border-l-4 border-indigo-600 rounded">
            <div class="flex items-start">
              <i class="fas fa-shield-alt text-indigo-600 mr-2 mt-1"></i>
              <div>
                <strong class="text-indigo-900">Admin Access Only</strong>
                <p class="text-sm text-indigo-700 mt-1">
                  This system is restricted to authorized administrators. Create an account or contact your system administrator for access.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  layout: false,
  middleware: []
})

// @ts-ignore - Nuxt auto-imports
const { login } = useAuth()
// @ts-ignore - Nuxt auto-imports
const router = useRouter()

const form = reactive({
  email: '',
  password: ''
})

const errors = reactive({
  email: '',
  password: ''
})

const loading = ref(false)
const error = ref('')
const success = ref('')
const showPassword = ref(false)

// Clear errors when user types
watch(() => form.email, () => { errors.email = ''; error.value = '' })
watch(() => form.password, () => { errors.password = ''; error.value = '' })

const handleLogin = async () => {
  // Reset errors
  error.value = ''
  errors.email = ''
  errors.password = ''
  loading.value = true

  try {
    const result = await login(form.email, form.password)
    
    if (result.success) {
      success.value = 'Login successful! Redirecting...'
      // Redirect to dashboard after short delay
      setTimeout(() => {
        router.push('/dashboard')
      }, 500)
    } else {
      error.value = result.error || 'Login failed. Please check your credentials.'
    }
  } catch (err: any) {
    error.value = err.message || 'An error occurred during login.'
  } finally {
    loading.value = false
  }
}
</script>

