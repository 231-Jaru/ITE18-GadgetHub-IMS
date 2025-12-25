<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
          <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-truck mr-2"></i> Supplier Performance Report
          </h1>
          <p class="text-lg opacity-90">Supplier performance and procurement analytics</p>
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
              <i class="fas fa-truck"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              <AnimatedNumber :value="totalSuppliers" />
            </h4>
            <p class="text-gray-600 font-medium">Total Suppliers</p>
          </div>
        </div>
        <div class="stats-card success">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-check-circle"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              <AnimatedNumber :value="activeSuppliers" />
            </h4>
            <p class="text-gray-600 font-medium">Active Suppliers</p>
          </div>
        </div>
        <div class="stats-card info">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              ₱<AnimatedNumber :value="totalStockValue" :decimals="2" />
            </h4>
            <p class="text-gray-600 font-medium">Total Stock Value</p>
          </div>
        </div>
        <div class="stats-card warning">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              <AnimatedNumber :value="lowPerformanceSuppliers" />
            </h4>
            <p class="text-gray-600 font-medium">Low Performance</p>
          </div>
        </div>
      </div>

      <!-- Supplier Analytics -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Stock Value by Supplier Chart -->
        <div class="lg:col-span-8 bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-blue-600 text-white px-6 py-4">
            <h5 class="text-xl font-semibold">
              <i class="fas fa-chart-bar mr-2"></i> Stock Value by Supplier
            </h5>
          </div>
          <div class="p-6">
            <ClientOnly>
              <div v-if="hasChartData">
                <canvas ref="stockValueChart" style="max-height: 300px;"></canvas>
              </div>
              <div v-else class="text-center py-12 text-gray-500">
                <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
                <p>No chart data available</p>
              </div>
              <template #fallback>
                <div class="text-center py-8">
                  <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                  <p class="mt-2 text-sm text-gray-600">Loading chart...</p>
                </div>
              </template>
            </ClientOnly>
          </div>
        </div>

        <!-- Top Suppliers -->
        <div class="lg:col-span-4 bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-green-600 text-white px-6 py-4">
            <h5 class="text-xl font-semibold">
              <i class="fas fa-trophy mr-2"></i> Top Suppliers
            </h5>
          </div>
          <div class="p-6">
            <div v-if="topSuppliers.length > 0" class="space-y-3">
              <div 
                v-for="supplier in topSuppliers" 
                :key="supplier.SupplierID || supplier.id"
                class="flex justify-between items-center py-2 border-b border-gray-200 last:border-0"
              >
                <div>
                  <h6 class="font-semibold text-gray-900 mb-1">{{ supplier.SupplierName }}</h6>
                  <small class="text-gray-600">{{ supplier.total_stocks }} items</small>
                </div>
                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                  ₱{{ formatPrice(supplier.total_value) }}
                </span>
              </div>
            </div>
            <div v-else class="text-center text-gray-500 py-8">
              <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
              <p class="text-sm">No supplier data available</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Supplier Performance Table -->
      <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="bg-cyan-600 text-white px-6 py-4">
          <h5 class="text-xl font-semibold">
            <i class="fas fa-table mr-2"></i> Supplier Performance
          </h5>
        </div>
        <div class="p-6">
          <div v-if="supplierPerformance.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Supplier Name</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Items</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Quantity</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Value</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="supplier in supplierPerformance" :key="supplier.SupplierID || supplier.id || supplier.SupplierName" class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3">
                    <div class="font-semibold">{{ supplier.SupplierName || 'Unknown Supplier' }}</div>
                  </td>
                  <td class="px-4 py-3">{{ supplier.total_stocks || supplier.total_supplies || 0 }}</td>
                  <td class="px-4 py-3">{{ formatNumber(supplier.total_quantity || supplier.total_quantity_supplied || 0) }}</td>
                  <td class="px-4 py-3 font-semibold text-green-600">₱{{ formatPrice(supplier.total_value || 0) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-12 text-gray-500">
            <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
            <p>No supplier data available</p>
          </div>
        </div>
      </div>

      <!-- Recent Stock Additions -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-yellow-500 text-white px-6 py-4">
          <h5 class="text-xl font-semibold">
            <i class="fas fa-history mr-2"></i> Recent Stock Additions
          </h5>
        </div>
        <div class="p-6">
          <div v-if="recentStockAdditions.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cost Price</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="stock in recentStockAdditions" :key="stock.StockID || stock.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3">{{ stock.supplier?.SupplierName || stock.supplier_name || 'Unknown' }}</td>
                  <td class="px-4 py-3">{{ stock.gadget?.GadgetName || stock.gadget_name || 'Unknown Product' }}</td>
                  <td class="px-4 py-3">{{ stock.QuantityAdded || stock.quantity_added || 0 }}</td>
                  <td class="px-4 py-3 font-semibold text-green-600">₱{{ formatPrice(parseFloat(stock.CostPrice || stock.cost_price) || 0) }}</td>
                  <td class="px-4 py-3">{{ formatDateShort(stock.updated_at || stock.PurchaseDate || stock.created_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-12 text-gray-500">
            <i class="fas fa-info-circle text-4xl mb-2 opacity-50"></i>
            <p>No recent stock additions</p>
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
const supplierPerformance = ref<any[]>([])
const recentStockAdditions = ref<any[]>([])
const totalSuppliers = ref(0)
const activeSuppliers = ref(0)
const totalStockValue = ref(0)
const lowPerformanceSuppliers = ref(0)
const stockValueChart = ref<HTMLCanvasElement | null>(null)
const stockValueBySupplierData = ref<{ labels: string[], data: number[] }>({ labels: [], data: [] })

// Computed
const topSuppliers = computed(() => {
  return supplierPerformance.value.slice(0, 5)
})

const hasChartData = computed(() => {
  return stockValueBySupplierData.value.labels.length > 0 && stockValueBySupplierData.value.data.length > 0
})

// Chart instance
let chartInstance: any = null

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

// Initialize Chart
const initChart = async () => {
  try {
    // Check if we have data
    if (!hasChartData.value) {
      return
    }

    // Wait for canvas element
    await nextTick()
    if (!stockValueChart.value) {
      setTimeout(() => initChart(), 300)
      return
    }

    // Ensure Chart.js is loaded
    const Chart = await ensureChartJS()

    // Get canvas context
    const ctx = stockValueChart.value.getContext('2d')
    if (!ctx) {
      return
    }

    // If chart already exists, update it instead of recreating
    if (chartInstance) {
      try {
        chartInstance.data.labels = stockValueBySupplierData.value.labels
        chartInstance.data.datasets[0].data = stockValueBySupplierData.value.data
        chartInstance.update()
        return
      } catch (e) {
        try {
          chartInstance.destroy()
        } catch (destroyErr) {}
        chartInstance = null
      }
    }

    // Create new chart
    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: stockValueBySupplierData.value.labels,
        datasets: [{
          label: 'Stock Value (₱)',
          data: stockValueBySupplierData.value.data,
          backgroundColor: [
            'rgba(13, 110, 253, 0.7)',
            'rgba(25, 135, 84, 0.7)',
            'rgba(253, 126, 20, 0.7)',
            'rgba(108, 117, 125, 0.7)',
            'rgba(111, 66, 193, 0.7)'
          ],
          borderColor: [
            'rgba(13, 110, 253, 1)',
            'rgba(25, 135, 84, 1)',
            'rgba(253, 126, 20, 1)',
            'rgba(108, 117, 125, 1)',
            'rgba(111, 66, 193, 1)'
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
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Stock Value (₱)'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Supplier'
            }
          }
        }
      }
    })
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

    const response = await api('/suppliers/report-data') as any

    totalSuppliers.value = response.totalSuppliers || 0
    totalStockValue.value = parseFloat(response.totalStockValue) || 0
    activeSuppliers.value = response.activeSuppliers || 0
    lowPerformanceSuppliers.value = response.lowPerformanceSuppliers || 0

    supplierPerformance.value = Array.isArray(response.supplierPerformance) 
      ? response.supplierPerformance 
      : []

    if (response.stockValueBySupplierData) {
      stockValueBySupplierData.value = {
        labels: response.stockValueBySupplierData.labels || [],
        data: response.stockValueBySupplierData.data || []
      }
    } else {
      stockValueBySupplierData.value = { labels: [], data: [] }
    }

    recentStockAdditions.value = Array.isArray(response.recentStockAdditions)
      ? response.recentStockAdditions
      : []

    // Update chart after data refresh
    if (!silent && hasChartData.value) {
      await nextTick()
      setTimeout(() => {
        initChart()
      }, 200)
    }
  } catch (err: any) {
    console.error('Error fetching supplier report data:', err)
    supplierPerformance.value = []
    recentStockAdditions.value = []
    totalSuppliers.value = 0
    activeSuppliers.value = 0
    totalStockValue.value = 0
    lowPerformanceSuppliers.value = 0
    stockValueBySupplierData.value = { labels: [], data: [] }
  } finally {
    if (!silent) {
      loading.value = false
    }
  }
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

// Watch for data changes and initialize chart
watch(
  () => [hasChartData.value, !loading.value],
  ([hasData, dataLoaded]) => {
    if (hasData && dataLoaded && typeof window !== 'undefined') {
      nextTick(() => {
        setTimeout(() => {
          initChart()
        }, 200)
      })
    }
  },
  { immediate: false }
)

// Auto-refresh functionality
let refreshInterval: NodeJS.Timeout | null = null

const startAutoRefresh = () => {
  // Refresh every 30 seconds
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
  // Initialize chart after data is loaded
  if (hasChartData.value) {
    await nextTick()
    setTimeout(() => {
      initChart()
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
  
  // Cleanup chart
  if (chartInstance) {
    try {
      chartInstance.destroy()
    } catch (e) {
      // Ignore errors during cleanup
    }
    chartInstance = null
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
