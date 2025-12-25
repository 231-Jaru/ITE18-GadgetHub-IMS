<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
          <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-chart-line mr-2"></i> Purchase Reports
          </h1>
          <p class="text-lg opacity-90">Monthly inventory purchases and procurement trends</p>
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

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading report data...</p>
    </div>

    <!-- Report Content -->
    <div v-else>
      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stats-card primary">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              ₱<AnimatedNumber :value="totalPurchaseValue" :decimals="2" />
            </h4>
            <p class="text-gray-600 font-medium">Total Purchase Value</p>
          </div>
        </div>
        <div class="stats-card success">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-receipt"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              <AnimatedNumber :value="totalTransactions" />
            </h4>
            <p class="text-gray-600 font-medium">Transactions</p>
          </div>
        </div>
        <div class="stats-card info">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-calculator"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              ₱<AnimatedNumber :value="averageOrder" :decimals="2" />
            </h4>
            <p class="text-gray-600 font-medium">Avg. Purchase</p>
          </div>
        </div>
        <div class="stats-card warning">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-chart-line"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              {{ growthPercentage >= 0 ? '+' : '' }}<AnimatedNumber :value="growthPercentage || 0" :decimals="1" />%
            </h4>
            <p class="text-gray-600 font-medium">Growth</p>
          </div>
        </div>
      </div>

      <!-- Purchase Analytics -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Monthly Purchase Chart -->
        <div class="lg:col-span-8 bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-indigo-600 text-white px-6 py-4">
          <h5 class="text-xl font-semibold">
              <i class="fas fa-chart-line mr-2"></i> Monthly Purchase Trend
          </h5>
        </div>
        <div class="p-6">
            <ClientOnly>
              <div v-if="hasMonthlyChartData">
                <canvas ref="monthlyChart" style="max-height: 300px;"></canvas>
          </div>
          <div v-else class="text-center py-12 text-gray-500">
                <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
                <p>No chart data available</p>
              </div>
              <template #fallback>
                <div class="text-center py-8">
                  <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                  <p class="mt-2 text-sm text-gray-600">Loading chart...</p>
                </div>
              </template>
            </ClientOnly>
          </div>
        </div>

        <!-- Top Purchased Products -->
        <div class="lg:col-span-4 bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-amber-500 text-white px-6 py-4">
            <h5 class="text-xl font-semibold">
              <i class="fas fa-trophy mr-2"></i> Top Purchased Products
            </h5>
          </div>
          <div class="p-6">
            <div v-if="topPurchasedProducts.length > 0" class="space-y-3">
              <div 
                v-for="(product, index) in topPurchasedProducts" 
                :key="product.GadgetID || index"
                class="flex justify-between items-center py-2 border-b border-gray-200 last:border-0"
              >
                <div>
                  <h6 class="font-semibold text-gray-900 mb-1">{{ product.GadgetName }}</h6>
                  <small class="text-gray-600">{{ product.category?.CategoryName || 'No Category' }}</small>
                </div>
                <span class="bg-indigo-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                  {{ product.total_sold }} units
                </span>
              </div>
            </div>
            <div v-else class="text-center text-gray-500 py-8">
              <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
              <p class="text-sm">No purchase data available</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Purchase Performance Table -->
      <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="bg-green-600 text-white px-6 py-4">
          <h5 class="text-xl font-semibold">
            <i class="fas fa-table mr-2"></i> Purchase Performance by Category
          </h5>
        </div>
        <div class="p-6">
          <div v-if="categoryPurchases.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category Name</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Purchases</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="category in categoryPurchases" :key="category.CategoryName" class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3">
                    <div class="font-semibold">{{ category.CategoryName || 'Unknown Category' }}</div>
                  </td>
                  <td class="px-4 py-3 font-semibold text-green-600">₱{{ formatPrice(category.total_purchases || 0) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-12 text-gray-500">
            <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
            <p>No category purchase data available</p>
          </div>
        </div>
      </div>

      <!-- Charts and Recent Transactions -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Purchases by Category Chart -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-green-600 text-white px-6 py-4">
            <h5 class="text-xl font-semibold">
              <i class="fas fa-chart-pie mr-2"></i> Purchases by Category
            </h5>
          </div>
          <div class="p-6">
            <ClientOnly>
              <div v-if="hasCategoryChartData">
                <canvas ref="categoryChart" style="max-height: 300px;"></canvas>
              </div>
              <div v-else class="text-center py-12 text-gray-500">
                <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
                <p>No chart data available</p>
              </div>
              <template #fallback>
                <div class="text-center py-8">
                  <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                  <p class="mt-2 text-sm text-gray-600">Loading chart...</p>
                </div>
              </template>
            </ClientOnly>
          </div>
        </div>

        <!-- Recent Transactions -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-cyan-600 text-white px-6 py-4">
          <h5 class="text-xl font-semibold">
              <i class="fas fa-history mr-2"></i> Recent Transactions
          </h5>
        </div>
        <div class="p-6">
            <div v-if="recentTransactions.length > 0" class="overflow-x-auto">
              <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Purchased By</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                  <tr 
                    v-for="transaction in recentTransactions" 
                    :key="transaction.TransactionID || transaction.id"
                    class="hover:bg-gray-50 transition-colors"
                  >
                  <td class="px-4 py-3">
                      <span v-if="getAdminName(transaction)" class="font-medium text-gray-900">
                        {{ getAdminName(transaction) }}
                      </span>
                      <span v-else class="text-gray-500 italic">
                        System
                      </span>
                    </td>
                    <td class="px-4 py-3">{{ transaction.gadget?.GadgetName || 'Unknown Product' }}</td>
                    <td class="px-4 py-3 font-semibold text-green-600">
                      ₱{{ formatPrice(calculateTransactionAmount(transaction)) }}
                  </td>
                  <td class="px-4 py-3">
                      {{ formatDateShort(transaction.TransactionDate || transaction.transaction_date) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-12 text-gray-500">
              <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
              <p>No recent transactions</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
useHead({
  script: [
    {
      src: 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
      defer: false
    }
  ]
})

// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

// State
const loading = ref(true)
const monthlyPurchases = ref<any[]>([])
const recentTransactions = ref<any[]>([])
const topPurchasedProducts = ref<any[]>([])
const categoryPurchases = ref<any[]>([])
const totalPurchaseValue = ref(0)
const totalTransactions = ref(0)
const averageOrder = ref(0)
const growthPercentage = ref(0)
const monthlyChartData = ref<{ labels: string[], data: number[] }>({ labels: [], data: [] })
const categoryChartData = ref<{ labels: string[], data: number[] }>({ labels: [], data: [] })

// Chart refs
const monthlyChart = ref<HTMLCanvasElement | null>(null)
const categoryChart = ref<HTMLCanvasElement | null>(null)
let monthlyChartInstance: any = null
let categoryChartInstance: any = null

// Computed
const hasMonthlyChartData = computed(() => {
  return monthlyChartData.value.labels.length > 0 && monthlyChartData.value.data.length > 0
})

const hasCategoryChartData = computed(() => {
  return categoryChartData.value.labels.length > 0 && categoryChartData.value.data.length > 0
})

// Load Chart.js
const ensureChartJS = (): Promise<any> => {
  return new Promise((resolve, reject) => {
    if (typeof window === 'undefined') {
      reject(new Error('Window not available'))
      return
    }

    // Check if already loaded
    if ((window as any).Chart) {
      resolve((window as any).Chart)
      return
    }

    // Check if script exists
    const existing = document.querySelector('script[src*="chart.js"]')
    if (existing) {
      const check = setInterval(() => {
        if ((window as any).Chart) {
          clearInterval(check)
          resolve((window as any).Chart)
        }
      }, 50)
      setTimeout(() => {
        clearInterval(check)
        if ((window as any).Chart) {
          resolve((window as any).Chart)
        } else {
          reject(new Error('Chart.js loading timeout'))
        }
      }, 10000)
      return
    }

    // Load script
    const script = document.createElement('script')
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'
    script.onload = () => {
      setTimeout(() => {
        if ((window as any).Chart) {
          resolve((window as any).Chart)
        } else {
          reject(new Error('Chart.js loaded but Chart not available'))
        }
      }, 100)
    }
    script.onerror = () => {
      reject(new Error('Failed to load Chart.js'))
    }
    document.head.appendChild(script)
  })
}

// Initialize Charts
const initCharts = async () => {
  try {
    // Ensure Chart.js is loaded
    const Chart = await ensureChartJS()
    
    if (typeof window === 'undefined' || !Chart) {
      console.error('Chart.js not available')
      return
    }

    // Wait for canvas elements
    await nextTick()

    // Monthly Purchase Chart
    if (hasMonthlyChartData.value && monthlyChart.value) {
      try {
        const ctx1 = monthlyChart.value.getContext('2d')
        if (ctx1) {
          // Update existing chart or create new one
          if (monthlyChartInstance) {
            try {
              monthlyChartInstance.data.labels = monthlyChartData.value.labels
              monthlyChartInstance.data.datasets[0].data = monthlyChartData.value.data
              monthlyChartInstance.update()
              console.log('Monthly chart updated')
            } catch (e) {
              console.warn('Error updating monthly chart, recreating:', e)
              try {
                monthlyChartInstance.destroy()
              } catch (destroyErr) {}
              monthlyChartInstance = null
              // Will create new chart below
            }
          }
          
          // Create new chart if it doesn't exist
          if (!monthlyChartInstance) {
            monthlyChartInstance = new Chart(ctx1, {
              type: 'line',
              data: {
                labels: monthlyChartData.value.labels,
                datasets: [{
                  label: 'Purchases (₱)',
                  data: monthlyChartData.value.data,
                  borderColor: 'rgb(13, 110, 253)',
                  backgroundColor: 'rgba(13, 110, 253, 0.1)',
                  tension: 0.4,
                  fill: true
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                  duration: 750
                },
                plugins: {
                  legend: {
                    display: false
                  }
                },
                scales: {
                  y: {
                    beginAtZero: true,
                    ticks: {
                      callback: function(value: any) {
                        return '₱' + value.toLocaleString()
                      }
                    },
                    title: {
                      display: true,
                      text: 'Purchase Value (₱)'
                    }
                  },
                  x: {
                    title: {
                      display: true,
                      text: 'Month'
                    }
                  }
                }
              }
            })
            console.log('Monthly chart initialized')
          }
        }
      } catch (e) {
        console.error('Error creating monthly chart:', e)
      }
    }

    // Category Chart
    if (hasCategoryChartData.value && categoryChart.value) {
      try {
        const ctx2 = categoryChart.value.getContext('2d')
        if (ctx2) {
          // Update existing chart or create new one
          if (categoryChartInstance) {
            try {
              categoryChartInstance.data.labels = categoryChartData.value.labels
              categoryChartInstance.data.datasets[0].data = categoryChartData.value.data
              categoryChartInstance.update()
              console.log('Category chart updated')
            } catch (e) {
              console.warn('Error updating category chart, recreating:', e)
              try {
                categoryChartInstance.destroy()
              } catch (destroyErr) {}
              categoryChartInstance = null
              // Will create new chart below
            }
          }
          
          // Create new chart if it doesn't exist
          if (!categoryChartInstance) {
            categoryChartInstance = new Chart(ctx2, {
              type: 'doughnut',
              data: {
                labels: categoryChartData.value.labels,
                datasets: [{
                  data: categoryChartData.value.data,
                  backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(253, 126, 20, 0.7)',
                    'rgba(111, 66, 193, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                  ],
                  borderColor: [
                    'rgba(13, 110, 253, 1)',
                    'rgba(25, 135, 84, 1)',
                    'rgba(253, 126, 20, 1)',
                    'rgba(111, 66, 193, 1)',
                    'rgba(108, 117, 125, 1)'
                  ],
                  borderWidth: 1
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                  duration: 750
                },
                plugins: {
                  legend: {
                    position: 'bottom'
                  }
                }
              }
            })
            console.log('Category chart initialized')
          }
        }
      } catch (e) {
        console.error('Error creating category chart:', e)
      }
    }
  } catch (error) {
    console.error('Chart init error:', error)
  }
}

// Methods
const fetchReportData = async (silent = false) => {
  try {
    if (!silent) {
      loading.value = true
    }
    
    const response = await api('/transactions/purchase-report-data') as any
    
    totalPurchaseValue.value = parseFloat(response.totalPurchaseValue) || 0
    totalTransactions.value = response.totalTransactions || 0
    averageOrder.value = parseFloat(response.averageOrder) || 0
    growthPercentage.value = parseFloat(response.growthPercentage) || 0
    
    monthlyPurchases.value = Array.isArray(response.monthlyPurchases) 
      ? response.monthlyPurchases 
      : []
    
    if (response.monthlyChartData) {
      monthlyChartData.value = {
        labels: response.monthlyChartData.labels || [],
        data: response.monthlyChartData.data || []
      }
    } else {
      monthlyChartData.value = { labels: [], data: [] }
    }
    
    categoryPurchases.value = Array.isArray(response.categoryPurchases)
      ? response.categoryPurchases
      : []
    
    if (response.categoryChartData) {
      categoryChartData.value = {
        labels: response.categoryChartData.labels || [],
        data: response.categoryChartData.data || []
      }
    } else {
      categoryChartData.value = { labels: [], data: [] }
      }
    
    topPurchasedProducts.value = Array.isArray(response.topPurchasedProducts)
      ? response.topPurchasedProducts
      : []
    
    recentTransactions.value = Array.isArray(response.recentTransactions)
      ? response.recentTransactions
      : []
    
    // Debug: Log transaction admin data
    if (recentTransactions.value.length > 0) {
      console.log('Recent transactions with admin data:', recentTransactions.value.map((t: any) => ({
        TransactionID: t.TransactionID || t.id,
        AdminID: t.AdminID,
        admin: t.admin,
        admin_username: t.admin_username
      })))
    }
    
  } catch (err: any) {
    console.error('Error fetching report data:', err)
    monthlyPurchases.value = []
    recentTransactions.value = []
    topPurchasedProducts.value = []
    categoryPurchases.value = []
    totalPurchaseValue.value = 0
    totalTransactions.value = 0
    averageOrder.value = 0
    growthPercentage.value = 0
    monthlyChartData.value = { labels: [], data: [] }
    categoryChartData.value = { labels: [], data: [] }
  } finally {
    loading.value = false
  }
}

const calculateTransactionAmount = (transaction: any) => {
  const quantity = parseInt(transaction.Quantity || transaction.quantity) || 0
  
  if (transaction.stock?.CostPrice) {
    const costPrice = parseFloat(transaction.stock.CostPrice) || 0
    return quantity * costPrice
  }
  
  if (transaction.CostPrice) {
    const costPrice = parseFloat(transaction.CostPrice) || 0
    return quantity * costPrice
  }
  
  if (transaction.TotalAmount) {
    return parseFloat(transaction.TotalAmount) || 0
  }
  
  return 0
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price || 0)
}

const formatNumber = (num: number) => {
  return new Intl.NumberFormat('en-US').format(num || 0)
}

const formatDateShort = (date: string | Date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' })
}

const getAdminName = (transaction: any): string | null => {
  // Try multiple possible paths for admin username
  if (transaction.admin) {
    return transaction.admin.Username || transaction.admin.username || transaction.admin.AdminName || null
  }
  
  // Fallback to admin_username if directly on transaction
  if (transaction.admin_username) {
    return transaction.admin_username
  }
  
  // If AdminID exists but admin object is missing, try to indicate it
  if (transaction.AdminID) {
    return `Admin #${transaction.AdminID}`
  }
  
  return null
}

// Watch for data changes and initialize charts
watch(
  () => [hasMonthlyChartData.value, hasCategoryChartData.value, !loading.value],
  ([hasMonthly, hasCategory, dataLoaded]) => {
    if (dataLoaded && (hasMonthly || hasCategory) && typeof window !== 'undefined') {
      nextTick(() => {
        setTimeout(() => {
          initCharts()
        }, 200)
      })
    }
  },
  { immediate: false }
)

// Auto-refresh functionality
let refreshInterval: NodeJS.Timeout | null = null

const startAutoRefresh = () => {
  // Refresh every 30 seconds (silent refresh - no loading indicator)
  refreshInterval = setInterval(() => {
    if (typeof window !== 'undefined' && !document.hidden) {
      fetchReportData(true) // Silent refresh
    }
  }, 30000) // 30 seconds
}

const stopAutoRefresh = () => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
    refreshInterval = null
  }
}

// Refresh when page becomes visible
const handleVisibilityChange = () => {
  if (typeof window !== 'undefined' && !document.hidden) {
    fetchReportData(true) // Silent refresh
  }
}

// Refresh when window gains focus
const handleFocus = () => {
  if (typeof window !== 'undefined') {
    fetchReportData(true) // Silent refresh
  }
}

// Lifecycle
onMounted(async () => {
  await fetchReportData()
  // Initialize charts after data is loaded
  if (hasMonthlyChartData.value || hasCategoryChartData.value) {
    await nextTick()
    setTimeout(() => {
      initCharts()
    }, 500)
  }
  
  // Start auto-refresh
  startAutoRefresh()
  
  // Listen for visibility and focus changes
  if (typeof window !== 'undefined') {
    document.addEventListener('visibilitychange', handleVisibilityChange)
    window.addEventListener('focus', handleFocus)
  }
})

onUnmounted(() => {
  // Stop auto-refresh
  stopAutoRefresh()
  
  // Remove event listeners
  if (typeof window !== 'undefined') {
    document.removeEventListener('visibilitychange', handleVisibilityChange)
    window.removeEventListener('focus', handleFocus)
  }
  
  // Cleanup charts
  if (monthlyChartInstance) {
    try {
      monthlyChartInstance.destroy()
    } catch (e) {
      // Ignore errors during cleanup
    }
    monthlyChartInstance = null
  }
  if (categoryChartInstance) {
    try {
      categoryChartInstance.destroy()
    } catch (e) {
      // Ignore errors during cleanup
    }
    categoryChartInstance = null
  }
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
