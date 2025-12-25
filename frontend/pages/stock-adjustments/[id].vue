<template>
  <div class="w-full max-w-4xl mx-auto px-4 py-6">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading stock adjustment...</p>
    </div>

    <!-- Error State -->
    <div 
      v-else-if="error" 
      class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center justify-between"
    >
      <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>{{ error }}</span>
      </div>
      <NuxtLink 
        to="/stock-adjustments" 
        class="ml-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
      >
        <i class="fas fa-arrow-left mr-1"></i>Back to Adjustments
      </NuxtLink>
    </div>

    <!-- Adjustment Details -->
    <div v-else-if="adjustment">
      <!-- Header Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="mb-4 md:mb-0">
            <h1 class="text-4xl font-bold mb-2">
              <i class="fas fa-adjust mr-2"></i> Stock Adjustment Details
            </h1>
            <p class="text-lg opacity-90">Adjustment ID: {{ adjustment.AdjustmentID }}</p>
          </div>
          <NuxtLink 
            to="/stock-adjustments" 
            class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
          >
            <i class="fas fa-arrow-left mr-2"></i> Back to Adjustments
          </NuxtLink>
        </div>
      </div>

      <!-- Adjustment Information -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h3 class="text-2xl font-bold mb-4">
          <i class="fas fa-info-circle mr-2 text-indigo-600"></i> Adjustment Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <p class="text-sm text-gray-600 mb-1">Gadget</p>
            <p class="text-lg font-semibold">{{ adjustment.gadget?.GadgetName || 'N/A' }}</p>
            <p v-if="adjustment.gadget?.category" class="text-sm text-gray-500">
              {{ adjustment.gadget.category.CategoryName }}
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Adjustment Type</p>
            <span :class="`inline-block px-4 py-2 rounded-full text-sm font-semibold text-white ${
              adjustment.AdjustmentType === 'INCREASE' ? 'bg-green-600' :
              adjustment.AdjustmentType === 'DECREASE' ? 'bg-red-600' :
              'bg-blue-600'
            }`">
              <i :class="`fas mr-1 ${
                adjustment.AdjustmentType === 'INCREASE' ? 'fa-arrow-up' :
                adjustment.AdjustmentType === 'DECREASE' ? 'fa-arrow-down' :
                'fa-equals'
              }`"></i>
              {{ adjustment.AdjustmentType }}
            </span>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Quantity Before</p>
            <p class="text-lg font-semibold">{{ adjustment.QuantityBefore }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Quantity Changed</p>
            <p :class="`text-lg font-semibold ${
              adjustment.AdjustmentType === 'INCREASE' ? 'text-green-600' :
              adjustment.AdjustmentType === 'DECREASE' ? 'text-red-600' :
              'text-blue-600'
            }`">
              {{ adjustment.AdjustmentType === 'INCREASE' ? '+' : adjustment.AdjustmentType === 'DECREASE' ? '-' : '' }}{{ Math.abs(adjustment.QuantityChanged) }}
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Quantity After</p>
            <p class="text-lg font-semibold">{{ adjustment.QuantityAfter }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Adjustment Date</p>
            <p class="text-lg font-semibold">{{ formatDate(adjustment.AdjustmentDate) }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Reason</p>
            <p class="text-lg font-semibold">{{ adjustment.Reason || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Admin</p>
            <p class="text-lg font-semibold">{{ adjustment.admin?.Username || 'N/A' }}</p>
          </div>
        </div>
        <div v-if="adjustment.Notes" class="mt-6 pt-6 border-t border-gray-200">
          <p class="text-sm text-gray-600 mb-1">Notes</p>
          <p class="text-gray-700">{{ adjustment.Notes }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
const route = useRoute()
// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

// State
const adjustment = ref<any>(null)
const loading = ref(true)
const error = ref('')

// Methods
const fetchAdjustment = async () => {
  try {
    loading.value = true
    error.value = ''
    const response = await api(`/stock-adjustments/${route.params.id}`)
    adjustment.value = response
  } catch (err: any) {
    console.error('Error fetching stock adjustment:', err)
    error.value = err.data?.message || err.message || 'Failed to load stock adjustment.'
  } finally {
    loading.value = false
  }
}

const formatDate = (date: string | Date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

// Lifecycle
onMounted(() => {
  fetchAdjustment()
})
</script>

