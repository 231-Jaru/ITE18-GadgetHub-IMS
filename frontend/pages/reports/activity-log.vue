<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
          <h1 class="text-3xl sm:text-4xl font-bold mb-2">
            <i class="fas fa-history mr-2"></i> Activity Log
          </h1>
          <p class="text-sm sm:text-base opacity-90">
            Recent inventory actions based on stock transactions (restocks).
          </p>
        </div>
        <div class="flex-shrink-0">
          <NuxtLink 
            to="/reports" 
            class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
          >
            <i class="fas fa-arrow-left mr-2"></i> Back to Reports
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div
      v-if="error"
      class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-center justify-between"
      role="alert"
    >
      <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>{{ error }}</span>
      </div>
      <button @click="error = ''" class="text-red-500 hover:text-red-700">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div
        class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
      ></div>
      <p class="mt-4 text-gray-600">Loading activity log...</p>
    </div>

    <!-- Activity Table -->
    <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="bg-indigo-600 text-white px-6 py-4 flex justify-between items-center">
        <h5 class="text-lg sm:text-xl font-semibold">
          <i class="fas fa-list mr-2"></i> Recent Inventory Activity
        </h5>
        <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-xs sm:text-sm font-semibold">
          {{ activities.length }} record{{ activities.length === 1 ? '' : 's' }}
        </span>
      </div>
      <div class="p-4 sm:p-6">
        <div v-if="activities.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">
                  When
                </th>
                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">
                  Admin
                </th>
                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">
                  Gadget
                </th>
                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">
                  Action
                </th>
                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">
                  Details
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="item in activities"
                :key="item.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-3 text-xs sm:text-sm text-gray-700">
                  {{ formatDateTime(item.timestamp) }}
                </td>
                <td class="px-4 py-3 text-xs sm:text-sm">
                  <span class="font-semibold text-gray-900">
                    {{ item.admin || 'System' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-xs sm:text-sm text-gray-700">
                  {{ item.gadget || 'N/A' }}
                </td>
                <td class="px-4 py-3 text-xs sm:text-sm">
                  <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold"
                    :class="item.type === 'RESTOCK' ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800'"
                  >
                    <i
                      :class="item.type === 'RESTOCK' ? 'fas fa-boxes mr-1' : 'fas fa-receipt mr-1'"
                    ></i>
                    {{ item.typeLabel }}
                  </span>
                </td>
                <td class="px-4 py-3 text-xs sm:text-sm text-gray-700">
                  {{ item.description }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-10 text-gray-500 text-sm">
          No recent activity found.
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

type ActivityItem = {
  id: number | string
  timestamp: string
  admin: string | null
  gadget: string | null
  type: 'RESTOCK' | 'PURCHASE'
  typeLabel: string
  description: string
}

const loading = ref(true)
const error = ref('')
const activities = ref<ActivityItem[]>([])

const loadActivity = async () => {
  try {
    loading.value = true
    error.value = ''

    const response: any = await api('/dashboard/analytics')
    const data = response?.data || response

    const txns = data?.recent_transactions || []
    const topPurchases = data?.top_purchased_gadgets || []

    const items: ActivityItem[] = []

    // Recent restock transactions
    for (const txn of txns) {
      items.push({
        id: `txn-${txn.TransactionID}`,
        timestamp: txn.TransactionDate,
        admin: txn.admin?.Username || null,
        gadget: txn.gadget?.GadgetName || null,
        type: 'RESTOCK',
        typeLabel: 'Stock Restocked',
        description: `Restocked ${txn.Quantity ?? 0} unit(s) at ₱${Number(
          txn.TotalAmount ?? 0
        ).toLocaleString('en-PH', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        })}`
      })
    }

    // Top purchased gadgets as summary purchases (optional context)
    for (const g of topPurchases) {
      items.push({
        id: `top-${g.GadgetID}`,
        timestamp: new Date().toISOString(),
        admin: null,
        gadget: g.GadgetName || null,
        type: 'PURCHASE',
        typeLabel: 'Purchase Summary',
        description: `Total purchased: ${g.total_purchased ?? 0} unit(s), total cost ₱${Number(
          g.total_cost ?? 0
        ).toLocaleString('en-PH', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        })}`
      })
    }

    activities.value = items
  } catch (err: any) {
    console.error('Error loading activity log:', err)
    error.value =
      err?.data?.message || err?.message || 'Failed to load activity log. Please try again.'
  } finally {
    loading.value = false
  }
}

const formatDateTime = (value: string) => {
  if (!value) return 'N/A'
  const date = new Date(value)
  return date.toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadActivity()
})
</script>

