<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-200">
    <div class="max-w-md w-full mx-4">
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-8 text-center relative overflow-hidden">
          <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-32 -mt-32"></div>
          <i class="fas fa-user-plus text-5xl mb-4 relative z-10"></i>
          <h2 class="text-2xl font-bold mb-2 relative z-10">Create Admin Account</h2>
          <p class="text-indigo-100 relative z-10">Register a new administrator</p>
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

          <!-- Registration Form -->
          <form @submit.prevent="handleRegister" class="space-y-6">
            <!-- Username Field -->
            <div>
              <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-user text-indigo-600 mr-2"></i>Username
              </label>
              <input
                id="username"
                name="username"
                v-model="form.Username"
                type="text"
                required
                autofocus
                autocomplete="username"
                maxlength="50"
                placeholder="Enter a username"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 outline-none transition"
                :class="{ 'border-red-500': errors.Username }"
              />
              <p v-if="errors.Username" class="mt-1 text-sm text-red-600">{{ errors.Username }}</p>
              <small class="text-gray-500 text-xs mt-1 block">Choose a unique username for your admin account</small>
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
                  v-model="form.PasswordHash"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autocomplete="new-password"
                  minlength="6"
                  placeholder="Enter a password (min. 6 characters)"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 outline-none transition pr-12"
                  :class="{ 'border-red-500': errors.PasswordHash }"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <p v-if="errors.PasswordHash" class="mt-1 text-sm text-red-600">{{ errors.PasswordHash }}</p>
              <small class="text-gray-500 text-xs mt-1 block">Password must be at least 6 characters long</small>
            </div>

            <!-- Confirm Password Field -->
            <div>
              <label for="confirmPassword" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-lock text-indigo-600 mr-2"></i>Confirm Password
              </label>
              <div class="relative">
                <input
                  id="confirmPassword"
                  name="confirmPassword"
                  v-model="form.confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  autocomplete="new-password"
                  placeholder="Confirm your password"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 outline-none transition pr-12"
                  :class="{ 'border-red-500': errors.confirmPassword }"
                />
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600"
                >
                  <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <p v-if="errors.confirmPassword" class="mt-1 text-sm text-red-600">{{ errors.confirmPassword }}</p>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-indigo-600 to-indigo-800 text-white py-3 px-6 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none"
            >
              <span v-if="loading" class="inline-block">
                <i class="fas fa-spinner fa-spin mr-2"></i>Registering...
              </span>
              <span v-else>
                <i class="fas fa-user-plus mr-2"></i>Register
              </span>
            </button>
          </form>

          <!-- Login Link -->
          <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
              Already have an account?
              <NuxtLink to="/login" class="text-indigo-600 hover:text-indigo-800 font-semibold transition">
                Log in here
              </NuxtLink>
            </p>
          </div>

          <!-- Info Box -->
          <div class="mt-6 p-4 bg-blue-50 border-l-4 border-indigo-600 rounded">
            <div class="flex items-start">
              <i class="fas fa-info-circle text-indigo-600 mr-2 mt-1"></i>
              <div>
                <strong class="text-indigo-900">Admin Registration</strong>
                <p class="text-sm text-indigo-700 mt-1">
                  Create a new administrator account to access the inventory management system.
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
const { register } = useAuth()
// @ts-ignore - Nuxt auto-imports
const router = useRouter()

const form = reactive({
  Username: '',
  PasswordHash: '',
  confirmPassword: '',
  Role: 'Staff'
})

const errors = reactive({
  Username: '',
  PasswordHash: '',
  confirmPassword: ''
})

const loading = ref(false)
const error = ref('')
const success = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)

// Clear errors when user types
watch(() => form.Username, () => { errors.Username = ''; error.value = '' })
watch(() => form.PasswordHash, () => { errors.PasswordHash = ''; error.value = '' })
watch(() => form.confirmPassword, () => { errors.confirmPassword = ''; error.value = '' })

const handleRegister = async () => {
  // Reset errors
  error.value = ''
  errors.Username = ''
  errors.PasswordHash = ''
  errors.confirmPassword = ''
  loading.value = true

  // Frontend validation
  if (!form.Username || form.Username.trim().length === 0) {
    errors.Username = 'Username is required'
    loading.value = false
    return
  }

  if (form.Username.length > 50) {
    errors.Username = 'Username must be 50 characters or less'
    loading.value = false
    return
  }

  if (!form.PasswordHash || form.PasswordHash.length < 6) {
    errors.PasswordHash = 'Password must be at least 6 characters long'
    loading.value = false
    return
  }

  if (form.PasswordHash !== form.confirmPassword) {
    errors.confirmPassword = 'Passwords do not match'
    loading.value = false
    return
  }

  try {
    // Default role is 'Staff' for all new registrations
    const result = await register(form.Username, form.PasswordHash, 'Staff')
    
    if (result.success) {
      success.value = 'Registration successful! Redirecting to dashboard...'
      // Redirect to dashboard after short delay
      setTimeout(() => {
        router.push('/dashboard')
      }, 1000)
    } else {
      // Handle validation errors from backend
      if (result.errors) {
        const backendErrors = result.errors
        if (backendErrors.Username) {
          errors.Username = Array.isArray(backendErrors.Username) ? backendErrors.Username[0] : backendErrors.Username
        }
        if (backendErrors.PasswordHash) {
          errors.PasswordHash = Array.isArray(backendErrors.PasswordHash) ? backendErrors.PasswordHash[0] : backendErrors.PasswordHash
        }
        error.value = result.message || 'Registration failed. Please check the errors above.'
      } else {
        error.value = result.error || result.message || 'Registration failed. Please try again.'
      }
    }
  } catch (err: any) {
    error.value = err.message || 'An error occurred during registration.'
  } finally {
    loading.value = false
  }
}
</script>

