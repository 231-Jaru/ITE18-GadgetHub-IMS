<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
          <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-sliders mr-2"></i> Stock Adjustments
          </h1>
          <p class="text-lg opacity-90">View all stock adjustments and corrections</p>
        </div>
        <NuxtLink 
          to="/stock-adjustments/create" 
          class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
        >
          <i class="fas fa-plus mr-2"></i> New Adjustment
        </NuxtLink>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div v-if="message" :class="`mb-4 p-4 rounded-lg flex items-center justify-between ${messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'}`">
      <div class="flex items-center">
        <i :class="`fas ${messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2`"></i>
        <span>{{ message }}</span>
      </div>
      <button @click="message = ''" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading stock adjustments...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="adjustments.length === 0" class="bg-white rounded-xl shadow-lg p-12 text-center">
      <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
      <h5 class="text-xl font-semibold text-gray-600 mb-2">No Stock Adjustments Yet</h5>
      <p class="text-gray-500 mb-6">Create your first stock adjustment to get started.</p>
      <NuxtLink 
        to="/stock-adjustments/create" 
        class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
      >
        <i class="fas fa-plus mr-2"></i> Create Adjustment
      </NuxtLink>
    </div>

    <!-- Adjustments Table -->
    <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="bg-indigo-600 text-white px-6 py-4">
        <h5 class="text-xl font-semibold">
          <i class="fas fa-table mr-2"></i> Stock Adjustments
        </h5>
      </div>
      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Before</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Change</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">After</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Reason</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Admin</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="adjustment in adjustments" 
                :key="adjustment.AdjustmentID"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-3">
                  <div>{{ formatDate(adjustment.AdjustmentDate) }}</div>
                </td>
                <td class="px-4 py-3">
                  <div v-if="adjustment.gadget">
                    <div class="font-semibold">{{ adjustment.gadget.GadgetName }}</div>
                    <div v-if="adjustment.gadget.category" class="text-sm text-gray-500">
                      {{ adjustment.gadget.category.CategoryName }}
                    </div>
                  </div>
                  <span v-else class="text-gray-400">Gadget not found</span>
                </td>
                <td class="px-4 py-3">
                  <span :class="`px-3 py-1 rounded-full text-sm font-semibold text-white ${
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
                </td>
                <td class="px-4 py-3">
                  <div class="font-semibold">{{ adjustment.QuantityBefore }}</div>
                </td>
                <td class="px-4 py-3">
                  <div :class="`font-semibold ${
                    adjustment.AdjustmentType === 'INCREASE' ? 'text-green-600' :
                    adjustment.AdjustmentType === 'DECREASE' ? 'text-red-600' :
                    'text-blue-600'
                  }`">
                    {{ adjustment.AdjustmentType === 'INCREASE' ? '+' : adjustment.AdjustmentType === 'DECREASE' ? '-' : '' }}{{ Math.abs(adjustment.QuantityChanged) }}
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="font-semibold">{{ adjustment.QuantityAfter }}</div>
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm">{{ adjustment.Reason || 'N/A' }}</div>
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm">{{ adjustment.admin?.Username || 'N/A' }}</div>
                </td>
                <td class="px-4 py-3">
                  <NuxtLink 
                    :to="`/stock-adjustments/${adjustment.AdjustmentID}`"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-semibold"
                  >
                    <i class="fas fa-eye mr-1"></i> View
                  </NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="mt-6 flex justify-center gap-2">
          <button 
            v-for="page in pagination.last_page" 
            :key="page"
            @click="goToPage(page)"
            :class="`px-4 py-2 rounded-lg transition-colors ${
              pagination.current_page === page 
                ? 'bg-indigo-600 text-white' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            }`"
          >
            {{ page }}
          </button>
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
const { api } = useApi()

// State
const adjustments = ref<any[]>([])
const loading = ref(true)
const message = ref('')
const messageType = ref<'success' | 'error'>('success')
const pagination = ref<any>(null)

// Methods
const fetchAdjustments = async () => {
  try {
    loading.value = true
    const response = await api('/stock-adjustments')
    
    // Handle paginated response
    if (response.data) {
      adjustments.value = response.data
      pagination.value = {
        current_page: response.current_page,
        last_page: response.last_page,
        per_page: response.per_page,
        total: response.total
      }
    } else if (Array.isArray(response)) {
      adjustments.value = response
    } else {
      adjustments.value = []
    }
  } catch (err: any) {
    console.error('Error fetching stock adjustments:', err)
    message.value = err.data?.message || err.message || 'Failed to load stock adjustments.'
    messageType.value = 'error'
  } finally {
    loading.value = false
  }
}

const goToPage = (page: number) => {
  // For now, just refetch - in a real app, you'd pass page as query param
  fetchAdjustments()
}

const formatDate = (date: string | Date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

// Lifecycle
onMounted(() => {
  fetchAdjustments()
})
</script>

