<template>
  <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl px-4 sm:px-8 py-6 sm:py-8 mb-6 shadow-lg">
      <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-1 sm:mb-2">
        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
      </h1>
      <p class="text-sm sm:text-base md:text-lg opacity-90">
        Welcome back, {{ userName }}! Here's an overview of your inventory.
      </p>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-center justify-between" role="alert">
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
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading dashboard data...</p>
    </div>

    <!-- Statistics Cards -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <div class="stats-card primary">
        <div class="text-center py-4 px-3 sm:p-6">
          <div class="icon-wrapper">
            <i class="fas fa-mobile-alt"></i>
          </div>
          <h3 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">
            <AnimatedNumber :value="stats.total_gadgets || 0" />
          </h3>
          <p class="text-gray-600 font-medium text-xs sm:text-sm">Total Gadgets</p>
        </div>
      </div>

      <div class="stats-card success">
        <div class="text-center py-4 px-3 sm:p-6">
          <div class="icon-wrapper">
            <i class="fas fa-boxes"></i>
          </div>
          <h3 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">
            <AnimatedNumber :value="stats.total_stocks || 0" />
          </h3>
          <p class="text-gray-600 font-medium text-xs sm:text-sm">Total Stock</p>
        </div>
      </div>

      <div class="stats-card info">
        <div class="text-center py-4 px-3 sm:p-6">
          <div class="icon-wrapper">
            <i class="fas fa-receipt"></i>
          </div>
          <h3 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">
            <AnimatedNumber :value="stats.total_transactions || 0" />
          </h3>
          <p class="text-gray-600 font-medium text-xs sm:text-sm">Transactions</p>
        </div>
      </div>

      <div class="stats-card warning">
        <div class="text-center py-4 px-3 sm:p-6">
          <div class="icon-wrapper">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h3 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">
            <AnimatedNumber :value="lowStockItems.length || 0" />
          </h3>
          <p class="text-gray-600 font-medium text-xs sm:text-sm">Low Stock Items</p>
        </div>
      </div>
    </div>

    <!-- Content Grid -->
    <div v-if="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
      <!-- Low Stock Items -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-red-600 text-white px-4 sm:px-6 py-3 sm:py-4">
          <h5 class="text-lg sm:text-xl font-semibold">
            <i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Items
          </h5>
        </div>
        <div class="p-4 sm:p-6">
          <div v-if="lowStockItems.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Stock</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="item in lowStockItems" :key="item.StockID" class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3">{{ item.gadget?.GadgetName || 'Unknown' }}</td>
                  <td class="px-4 py-3">
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">
                      {{ item.QuantityAdded }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex flex-col gap-1.5">
                      <NuxtLink 
                        :to="`/purchase-orders/create?gadget=${item.gadget?.GadgetID}&supplier=${item.supplier?.SupplierID || ''}`"
                        class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 text-white px-3 py-1.5 rounded-md text-xs sm:text-sm hover:bg-indigo-700 transition"
                        aria-label="Create purchase order"
                        title="Create purchase order"
                      >
                        <i class="fas fa-shopping-cart"></i>
                        <span>Order</span>
                      </NuxtLink>
                      <NuxtLink 
                        :to="`/stock-adjustments/create?gadget=${item.gadget?.GadgetID}`"
                        class="inline-flex items-center justify-center gap-1.5 bg-gray-600 text-white px-3 py-1.5 rounded-md text-xs sm:text-sm hover:bg-gray-700 transition"
                        aria-label="Record found inventory or make adjustments"
                        title="Record found inventory or make adjustments"
                      >
                        <i class="fas fa-sliders"></i>
                        <span>Adjust</span>
                      </NuxtLink>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            All stock levels are good!
          </div>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-indigo-600 text-white px-4 sm:px-6 py-3 sm:py-4">
          <h5 class="text-lg sm:text-xl font-semibold">
            <i class="fas fa-clock mr-2"></i>Recent Transactions
          </h5>
        </div>
        <div class="p-4 sm:p-6">
          <div v-if="recentTransactions.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Details</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="transaction in recentTransactions" :key="transaction.TransactionID" class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3">
                    <div class="font-semibold text-indigo-600">{{ transaction.admin?.Username || 'Admin' }}</div>
                    <div class="text-xs text-gray-500">{{ transaction.gadget?.GadgetName || '' }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs">Restock</span>
                  </td>
                  <td class="px-4 py-3">₱{{ formatCurrency(transaction.TotalAmount || 0) }}</td>
                  <td class="px-4 py-3 text-xs text-gray-600">
                    {{ formatDate(transaction.TransactionDate) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            No recent transactions.
          </div>
        </div>
      </div>

      <!-- Top Restocked Gadgets -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-purple-600 text-white px-4 sm:px-6 py-3 sm:py-4">
          <h5 class="text-lg sm:text-xl font-semibold">
            <i class="fas fa-trophy mr-2"></i>Top Restocked Gadgets
          </h5>
        </div>
        <div class="p-4 sm:p-6">
          <div v-if="topPurchasedGadgets.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Quantity Restocked</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Cost</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="gadget in topPurchasedGadgets" :key="gadget.GadgetID" class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3">{{ gadget.GadgetName }}</td>
                  <td class="px-4 py-3">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">
                      {{ gadget.total_purchased }}
                    </span>
                  </td>
                  <td class="px-4 py-3">₱{{ formatCurrency(gadget.total_cost) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            No restocking data available.
          </div>
        </div>
      </div>

      <!-- Monthly Inventory Purchases Chart -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-purple-600 text-white px-4 sm:px-6 py-3 sm:py-4">
          <h5 class="text-lg sm:text-xl font-semibold">
            <i class="fas fa-chart-line mr-2"></i>Monthly Inventory Purchases (Last 6 Months)
          </h5>
        </div>
        <div class="p-6">
          <div v-if="last6MonthsPurchases.length > 0" class="space-y-3">
            <div v-for="(purchase, index) in last6MonthsPurchases" :key="index" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
              <span class="text-gray-700 font-medium">{{ purchase.month_name }}</span>
              <span class="text-indigo-600 font-semibold">₱{{ formatCurrency(purchase.total) }}</span>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            No inventory purchase data available.
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
const { api } = useApi()
// @ts-ignore - Nuxt auto-imports
const authStore = useAuthStore()
const userName = computed(() => authStore.userName)

const stats = ref({
  total_gadgets: 0,
  total_stocks: 0,
  total_transactions: 0,
  total_suppliers: 0
})

const lowStockItems = ref<any[]>([])
const recentTransactions = ref<any[]>([])
const topPurchasedGadgets = ref<any[]>([])
const monthlyPurchases = ref<any[]>([])
const loading = ref(true)
const error = ref('')

// Limit monthly purchases to last 6 months
const last6MonthsPurchases = computed(() => {
  return monthlyPurchases.value.slice(0, 6)
})

// Fetch dashboard data
const fetchDashboardData = async () => {
  try {
    loading.value = true
    error.value = ''
    const response = await api('/dashboard/analytics') as any
    
    if (response.status === 'success' && response.data) {
      stats.value = response.data.stats || stats.value
      lowStockItems.value = response.data.low_stock_items || []
      recentTransactions.value = response.data.recent_transactions || []
      topPurchasedGadgets.value = response.data.top_purchased_gadgets || []
      monthlyPurchases.value = response.data.monthly_purchases || []
    } else {
      error.value = 'Failed to load dashboard data. Please try again.'
    }
  } catch (err: any) {
    console.error('Error fetching dashboard data:', err)
    error.value = err.data?.message || err.message || 'Failed to load dashboard data. Please refresh the page.'
  } finally {
    loading.value = false
  }
}


// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

// Format date
const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}


// Fetch data on mount
onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
.stats-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  height: 100%;
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.icon-wrapper {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  font-size: 1.5rem;
}

.stats-card.primary .icon-wrapper {
  background: linear-gradient(135deg, #4f46e5, #3730a3);
  color: white;
}

.stats-card.success .icon-wrapper {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.stats-card.warning .icon-wrapper {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
}

.stats-card.info .icon-wrapper {
  background: linear-gradient(135deg, #06b6d4, #0891b2);
  color: white;
}
</style>

