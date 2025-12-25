<template>
  <div class="w-full max-w-4xl mx-auto px-4 py-6">
    <!-- Success Notification -->
    <SuccessNotification 
      :show="showSuccess" 
      message="Supplier created successfully!"
    />

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <h1 class="text-4xl font-bold mb-2">
        <i class="fas fa-plus-circle mr-2"></i> Add New Supplier
      </h1>
      <p class="text-lg opacity-90">Create a new supplier profile for your inventory system</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <form @submit.prevent="handleSubmit" novalidate>
        <!-- Supplier Name -->
        <div class="mb-6">
          <label for="supplierName" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-truck mr-2 text-indigo-600"></i>Supplier Name <span class="text-red-500">*</span>
          </label>
          <input 
            type="text" 
            id="supplierName"
            name="SupplierName"
            v-model="form.SupplierName"
            class="w-full px-4 py-3 border-2 rounded-lg focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            :class="errors.SupplierName ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500'"
            placeholder="Enter supplier name"
            required
          >
          <p v-if="errors.SupplierName" class="mt-1 text-sm text-red-600">{{ errors.SupplierName }}</p>
          <small class="text-gray-500 text-xs mt-1 block">
            <i class="fas fa-info-circle mr-1"></i>Use the official supplier name as it will appear in reports.
          </small>
        </div>

        <!-- Contact Person -->
        <div class="mb-6">
          <label for="contactPerson" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-user mr-2 text-indigo-600"></i>Contact Person
          </label>
          <input 
            type="text" 
            id="contactPerson"
            name="ContactPerson"
            v-model="form.ContactPerson"
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            placeholder="Enter contact person name (optional)"
          >
        </div>

        <!-- Email and Phone -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-envelope mr-2 text-indigo-600"></i>Email
            </label>
            <input 
              type="email" 
              id="email"
              name="Email"
              v-model="form.Email"
              class="w-full px-4 py-3 border-2 rounded-lg focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
              :class="errors.Email ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500'"
              placeholder="supplier@example.com (optional)"
            >
            <p v-if="errors.Email" class="mt-1 text-sm text-red-600">{{ errors.Email }}</p>
            <small class="text-gray-500 text-xs mt-1 block">
              <i class="fas fa-info-circle mr-1"></i>If provided, this should be a valid email. It helps for sending order updates.
            </small>
          </div>
          <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-phone mr-2 text-indigo-600"></i>Phone
            </label>
            <input 
              type="tel" 
              id="phone"
              name="Phone"
              v-model="form.Phone"
              class="w-full px-4 py-3 border-2 rounded-lg focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
              :class="errors.Phone ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500'"
              placeholder="+1 234 567 8900 (optional)"
            >
            <p v-if="errors.Phone" class="mt-1 text-sm text-red-600">{{ errors.Phone }}</p>
            <small class="text-gray-500 text-xs mt-1 block">
              <i class="fas fa-info-circle mr-1"></i>Include country code if possible. Digits only are recommended.
            </small>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
          <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
          <NuxtLink 
            to="/suppliers" 
            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
          >
            <i class="fas fa-times mr-2"></i> Cancel
          </NuxtLink>
          <button 
            type="submit" 
            :disabled="submitting"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            <i v-else class="fas fa-save mr-2"></i> Create Supplier
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
const router = useRouter()
// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

// State
const form = ref({
  SupplierName: '',
  ContactPerson: '',
  Email: '',
  Phone: ''
})
const submitting = ref(false)
const error = ref('')
const showSuccess = ref(false)
const errors = ref<{ SupplierName?: string; Email?: string; Phone?: string }>({})

const validate = (): boolean => {
  const newErrors: { SupplierName?: string; Email?: string; Phone?: string } = {}

  if (!form.value.SupplierName || !form.value.SupplierName.trim()) {
    newErrors.SupplierName = 'Supplier name is required.'
  }

  if (form.value.Email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailPattern.test(form.value.Email.trim())) {
      newErrors.Email = 'Please enter a valid email address.'
    }
  }

  if (form.value.Phone) {
    const digits = form.value.Phone.replace(/\D/g, '')
    if (digits.length < 7) {
      newErrors.Phone = 'Please enter a valid phone number (at least 7 digits).'
    }
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

// Methods
const handleSubmit = async () => {
  try {
    submitting.value = true
    error.value = ''
    errors.value = {}

    // Frontend validation
    if (!validate()) {
      submitting.value = false
      return
    }

    await api('/suppliers', {
      method: 'POST',
      body: form.value
    })

    // Show success notification
    showSuccess.value = true
    
    // Navigate after a short delay to show the notification
    setTimeout(() => {
      router.push('/suppliers?created=true')
    }, 1500)
  } catch (err: any) {
    console.error('Error creating supplier:', err)
    if (err.data?.errors) {
      const errors = err.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else {
      error.value = err.data?.message || err.message || 'Failed to create supplier. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}
</script>

