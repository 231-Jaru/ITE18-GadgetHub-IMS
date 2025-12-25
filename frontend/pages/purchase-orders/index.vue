<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
          <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-shopping-cart mr-2"></i> Purchase Orders
          </h1>
          <p class="text-lg opacity-90">Manage your purchase orders from suppliers</p>
        </div>
        <NuxtLink 
          to="/purchase-orders/create" 
          class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
        >
          <i class="fas fa-plus mr-2"></i> New Purchase Order
        </NuxtLink>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div
      v-if="message"
      :class="`mb-4 p-4 rounded-lg flex items-center justify-between ${
        messageType === 'success'
          ? 'bg-green-50 border border-green-200 text-green-800'
          : 'bg-red-50 border border-red-200 text-red-800'
      }`"
    >
      <div class="flex items-center">
        <i
          :class="`fas ${
            messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'
          } mr-2`"
        ></i>
        <span>{{ message }}</span>
      </div>
      <button @click="message = ''" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading purchase orders...</p>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="purchaseOrders.length === 0"
      class="bg-white rounded-xl shadow-lg p-12 text-center"
    >
      <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
      <h5 class="text-xl font-semibold text-gray-600 mb-2">No Purchase Orders Yet</h5>
      <p class="text-gray-500 mb-6">Create your first purchase order to get started.</p>
      <NuxtLink 
        to="/purchase-orders/create" 
        class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
      >
        <i class="fas fa-plus mr-2"></i> Create Purchase Order
      </NuxtLink>
    </div>

    <!-- Purchase Orders Table + Filters -->
    <div v-else class="space-y-4">
      <!-- Advanced Filters -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
          <h5 class="text-lg font-semibold flex items-center">
            <i class="fas fa-filter mr-2 text-gray-600"></i>
            Advanced Filters
          </h5>
          <button
            type="button"
            class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors"
            @click="advancedFiltersOpen = !advancedFiltersOpen"
          >
            <i :class="['fas mr-1', advancedFiltersOpen ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            {{ advancedFiltersOpen ? 'Hide Filters' : 'Show Filters' }}
          </button>
        </div>

        <div v-if="advancedFiltersOpen" class="space-y-4">
          <!-- Presets -->
          <div class="flex flex-wrap gap-2 mb-2">
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-semibold border border-gray-300 hover:bg-gray-100 transition-colors"
              @click="applyPreset('thisMonth')"
            >
              <i class="fas fa-calendar-day mr-1"></i>This Month
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-semibold border border-gray-300 hover:bg-gray-100 transition-colors"
              @click="applyPreset('last30')"
            >
              <i class="fas fa-calendar-alt mr-1"></i>Last 30 Days
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-semibold border border-gray-300 hover:bg-gray-100 transition-colors"
              @click="applyPreset('highValue')"
            >
              <i class="fas fa-coins mr-1"></i>High Value (≥ ₱50,000)
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-semibold border border-gray-300 hover:bg-gray-100 transition-colors"
              @click="clearFilters"
            >
              <i class="fas fa-undo mr-1"></i>Clear Filters
            </button>
          </div>

          <!-- Filter Fields -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date range -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                Order Date From
              </label>
              <input
                type="date"
                v-model="filters.dateFrom"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                Order Date To
              </label>
              <input
                type="date"
                v-model="filters.dateTo"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"
              />
            </div>

            <!-- Amount range -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                Min Total Amount (₱)
              </label>
              <input
                type="number"
                v-model.number="filters.minAmount"
                min="0"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"
                placeholder="e.g., 10000"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                Max Total Amount (₱)
              </label>
              <input
                type="number"
                v-model.number="filters.maxAmount"
                min="0"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"
                placeholder="e.g., 50000"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <!-- Supplier -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                Supplier
              </label>
              <select
                v-model="filters.supplierId"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"
              >
                <option value="">All suppliers</option>
                <option
                  v-for="supplier in supplierOptions"
                  :key="supplier.id"
                  :value="supplier.id"
                >
                  {{ supplier.name }}
                </option>
              </select>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                Status
              </label>
              <select
                v-model="filters.status"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"
              >
                <option value="">All statuses</option>
                <option value="DRAFT">Draft</option>
                <option value="PENDING">Pending / Ordered</option>
                <option value="RECEIVED">Received</option>
                <option value="CANCELLED">Cancelled</option>
              </select>
            </div>

            <!-- Text search -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                Quick Search
              </label>
              <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input
                  type="text"
                  v-model="filters.search"
                  placeholder="PO number, supplier, status..."
                  class="w-full pl-10 pr-3 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"
                />
              </div>
            </div>
          </div>

          <p class="text-xs text-gray-500 mt-2">
            Filters are applied on the currently loaded page of purchase orders.
          </p>
        </div>
      </div>

      <!-- Purchase Orders Table -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-indigo-600 text-white px-6 py-4 flex justify-between items-center">
          <h5 class="text-xl font-semibold">
            <i class="fas fa-list mr-2"></i> Purchase Orders
          </h5>
          <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-sm font-semibold">
            {{ filteredPurchaseOrders.length }} shown
          </span>
        </div>
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                    PO Number
                  </th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                    Supplier
                  </th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                    Items
                  </th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                    Total Amount
                  </th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                    Status
                  </th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                    Order Date
                  </th>
                  <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="po in filteredPurchaseOrders"
                  :key="po.PurchaseOrderID"
                  class="hover:bg-gray-50 transition-colors"
                >
                  <td class="px-4 py-3">
                    <div class="font-semibold">{{ po.PONumber }}</div>
                  </td>
                  <td class="px-4 py-3">
                    {{ po.supplier?.SupplierName || 'N/A' }}
                  </td>
                  <td class="px-4 py-3">
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                      {{ po.items?.length || 0 }} item(s)
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="font-semibold">
                      ₱{{ formatPrice(po.TotalAmount) }}
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <span
                      :class="`px-3 py-1 rounded-full text-sm font-semibold text-white ${
                        po.Status === 'DRAFT'
                          ? 'bg-gray-600'
                          : po.Status === 'PENDING' || po.Status === 'ORDERED'
                          ? 'bg-blue-600'
                          : po.Status === 'RECEIVED'
                          ? 'bg-green-600'
                          : 'bg-red-600'
                      }`"
                    >
                      {{ po.Status }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    {{ formatDate(po.OrderDate) }}
                  </td>
                  <td class="px-4 py-3">
                    <NuxtLink
                      :to="`/purchase-orders/${po.PurchaseOrderID}`"
                      class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-semibold"
                    >
                      <i class="fas fa-eye mr-1"></i>
                      View
                    </NuxtLink>
                  </td>
                </tr>
                <tr v-if="filteredPurchaseOrders.length === 0">
                  <td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">
                    No purchase orders match the selected filters.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <Pagination
            v-if="pagination && !loading"
            :pagination="pagination"
            @page-change="goToPage"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

// State
const purchaseOrders = ref<any[]>([])
const loading = ref(true)
const message = ref('')
const messageType = ref<'success' | 'error'>('success')
const pagination = ref<any>(null)

// Advanced filter state
const advancedFiltersOpen = ref(false)
const filters = ref<{
  dateFrom: string | null
  dateTo: string | null
  minAmount: number | null
  maxAmount: number | null
  supplierId: string | number | ''
  status: string
  search: string
}>({
  dateFrom: null,
  dateTo: null,
  minAmount: null,
  maxAmount: null,
  supplierId: '',
  status: '',
  search: ''
})

const supplierOptions = ref<{ id: number; name: string }[]>([])

// Derived filtered list
const filteredPurchaseOrders = computed(() => {
  let list = [...purchaseOrders.value]

  // Date range
  if (filters.value.dateFrom) {
    const from = new Date(filters.value.dateFrom)
    list = list.filter((po) => {
      if (!po.OrderDate) return false
      return new Date(po.OrderDate) >= from
    })
  }

  if (filters.value.dateTo) {
    const to = new Date(filters.value.dateTo)
    // Include entire end day
    to.setHours(23, 59, 59, 999)
    list = list.filter((po) => {
      if (!po.OrderDate) return false
      return new Date(po.OrderDate) <= to
    })
  }

  // Amount range
  if (filters.value.minAmount !== null && filters.value.minAmount !== undefined) {
    list = list.filter((po) => (po.TotalAmount || 0) >= filters.value.minAmount!)
  }

  if (filters.value.maxAmount !== null && filters.value.maxAmount !== undefined) {
    list = list.filter((po) => (po.TotalAmount || 0) <= filters.value.maxAmount!)
  }

  // Supplier
  if (filters.value.supplierId) {
    const idNum = Number(filters.value.supplierId)
    list = list.filter((po) => po.SupplierID === idNum || po.supplier?.SupplierID === idNum)
  }

  // Status
  if (filters.value.status) {
    list = list.filter((po) => {
      if (!po.Status) return false
      const status = String(po.Status).toUpperCase()
      if (filters.value.status === 'PENDING') {
        // Group PENDING / ORDERED
        return status === 'PENDING' || status === 'ORDERED'
      }
      return status === filters.value.status
    })
  }

  // Text search
  if (filters.value.search.trim()) {
    const term = filters.value.search.toLowerCase().trim()
    list = list.filter((po) => {
      const poNumber = (po.PONumber || '').toString().toLowerCase()
      const supplierName = (po.supplier?.SupplierName || '').toLowerCase()
      const status = (po.Status || '').toString().toLowerCase()
      return poNumber.includes(term) || supplierName.includes(term) || status.includes(term)
    })
  }

  return list
})

// Methods
const fetchPurchaseOrders = async (page: number = 1) => {
  try {
    loading.value = true
    message.value = ''
    const response = await api(`/purchase-orders?page=${page}`) as any
    
    // Handle paginated response
    if (response.data && Array.isArray(response.data)) {
      purchaseOrders.value = response.data
      pagination.value = {
        current_page: response.current_page || page,
        last_page: response.last_page || 1,
        per_page: response.per_page || 15,
        total: response.total || 0
      }
    } else if (Array.isArray(response)) {
      purchaseOrders.value = response
      pagination.value = null
    } else if (response.purchase_orders && response.purchase_orders.data) {
      purchaseOrders.value = response.purchase_orders.data
      pagination.value = {
        current_page: response.purchase_orders.current_page || page,
        last_page: response.purchase_orders.last_page || 1,
        per_page: response.purchase_orders.per_page || 15,
        total: response.purchase_orders.total || 0
      }
    } else {
      purchaseOrders.value = []
      pagination.value = null
    }

    // Build supplier options from current page data
    const map = new Map<number, string>()
    purchaseOrders.value.forEach((po: any) => {
      const id = po.SupplierID || po.supplier?.SupplierID
      const name = po.supplier?.SupplierName
      if (id && name && !map.has(id)) {
        map.set(id, name)
      }
    })
    supplierOptions.value = Array.from(map.entries()).map(([id, name]) => ({ id, name }))
  } catch (err: any) {
    console.error('Error fetching purchase orders:', err)
    const errorMessage = err.data?.message || err.message || 'Failed to load purchase orders.'
    message.value = errorMessage + ' Please try again or refresh the page.'
    messageType.value = 'error'
    purchaseOrders.value = [] // Clear on error
  } finally {
    loading.value = false
  }
}

const goToPage = (page: number) => {
  if (!pagination.value) return
  if (page < 1 || page > pagination.value.last_page) return
  fetchPurchaseOrders(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const clearFilters = () => {
  filters.value = {
    dateFrom: null,
    dateTo: null,
    minAmount: null,
    maxAmount: null,
    supplierId: '',
    status: '',
    search: ''
  }
}

const applyPreset = (preset: 'thisMonth' | 'last30' | 'highValue') => {
  const now = new Date()

  if (preset === 'thisMonth') {
    const start = new Date(now.getFullYear(), now.getMonth(), 1)
    const end = new Date(now.getFullYear(), now.getMonth() + 1, 0)
    filters.value.dateFrom = start.toISOString().slice(0, 10)
    filters.value.dateTo = end.toISOString().slice(0, 10)
    filters.value.minAmount = null
    filters.value.maxAmount = null
  } else if (preset === 'last30') {
    const start = new Date()
    start.setDate(start.getDate() - 30)
    filters.value.dateFrom = start.toISOString().slice(0, 10)
    filters.value.dateTo = now.toISOString().slice(0, 10)
    filters.value.minAmount = null
    filters.value.maxAmount = null
  } else if (preset === 'highValue') {
    filters.value.minAmount = 50000
    filters.value.maxAmount = null
  }
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price || 0)
}

const formatDate = (date: string | Date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

// Lifecycle
onMounted(() => {
  fetchPurchaseOrders()
})
</script>
