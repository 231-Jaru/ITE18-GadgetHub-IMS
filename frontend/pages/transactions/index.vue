<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <h1 class="text-4xl font-bold mb-2">
        <i class="fas fa-receipt mr-2"></i> Transactions
      </h1>
      <p class="text-lg opacity-90">Track all inventory stock movements and transactions</p>
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-indigo-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
        <div class="flex justify-between items-center">
          <div>
            <h4 class="text-3xl font-bold mb-1">{{ stats.totalTransactions || 0 }}</h4>
            <p class="text-sm opacity-90">Total Transactions</p>
          </div>
          <i class="fas fa-receipt text-4xl opacity-75"></i>
        </div>
      </div>
      <div class="bg-purple-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
        <div class="flex justify-between items-center">
          <div>
            <h4 class="text-3xl font-bold mb-1">₱{{ formatPrice(stats.totalCostValue || 0) }}</h4>
            <p class="text-sm opacity-90">Total Cost Value</p>
          </div>
          <i class="fas fa-dollar-sign text-4xl opacity-75"></i>
        </div>
      </div>
      <div class="bg-indigo-500 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
        <div class="flex justify-between items-center">
          <div>
            <h4 class="text-3xl font-bold mb-1">{{ stats.thisMonth || 0 }}</h4>
            <p class="text-sm opacity-90">This Month</p>
          </div>
          <i class="fas fa-calendar-alt text-4xl opacity-75"></i>
        </div>
      </div>
      <div class="bg-purple-500 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
        <div class="flex justify-between items-center">
          <div>
            <h4 class="text-3xl font-bold mb-1">{{ stats.today || 0 }}</h4>
            <p class="text-sm opacity-90">Today</p>
          </div>
          <i class="fas fa-calendar-day text-4xl opacity-75"></i>
        </div>
      </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-lg mb-6 p-6">
      <h5 class="text-lg font-semibold mb-4">
        <i class="fas fa-search mr-2 text-gray-600"></i> Search & Filter
      </h5>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
          <input 
            type="text" 
            v-model="searchTerm"
            placeholder="Search by gadget name, ID..."
            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all"
          >
        </div>
        <div>
          <select 
            v-model="filterType"
            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all"
          >
            <option value="">All Types</option>
            <option value="IN">IN (Stock In)</option>
            <option value="OUT">OUT (Sale)</option>
          </select>
        </div>
        <div>
          <button 
            @click="clearFilters"
            class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
          >
            <i class="fas fa-times mr-2"></i> Clear
          </button>
        </div>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="bg-indigo-600 text-white px-6 py-4 flex justify-between items-center">
        <h5 class="text-xl font-semibold">
          <i class="fas fa-table mr-2"></i> Transaction History
        </h5>
        <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-sm font-semibold">
          {{ filteredTransactions.length }} transactions
        </span>
      </div>
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
          <p class="mt-4 text-gray-600">Loading transactions...</p>
        </div>

        <!-- Transactions Table -->
        <div v-else-if="filteredTransactions.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Amount</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="transaction in filteredTransactions" 
                :key="transaction.TransactionID"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-3">{{ transaction.TransactionID }}</td>
                <td class="px-4 py-3">{{ formatDate(transaction.TransactionDate) }}</td>
                <td class="px-4 py-3">
                  <span :class="`px-3 py-1 rounded-full text-sm font-semibold text-white ${
                    transaction.TransactionType === 'OUT' ? 'bg-green-600' : 'bg-blue-600'
                  }`">
                    {{ transaction.TransactionType }}
                  </span>
                </td>
                <td class="px-4 py-3">{{ transaction.gadget?.GadgetName || 'N/A' }}</td>
                <td class="px-4 py-3">{{ transaction.Quantity }}</td>
                <td class="px-4 py-3">
                  <div class="font-semibold">₱{{ formatPrice(calculateTotal(transaction)) }}</div>
                </td>
                <td class="px-4 py-3">
                  <NuxtLink 
                    :to="`/transactions/${transaction.TransactionID}`"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-semibold"
                  >
                    <i class="fas fa-eye mr-1"></i> View
                  </NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
          <h5 class="text-xl font-semibold text-gray-600 mb-2">No transactions found</h5>
          <p class="text-gray-500">Transactions will appear here when stock is added or adjusted.</p>
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
import { ref, computed, onMounted } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

// State
const transactions = ref<any[]>([])
const loading = ref(true)
const searchTerm = ref('')
const filterType = ref('')
const message = ref('')
const messageType = ref<'success' | 'error'>('success')
const pagination = ref<any>(null)
const stats = ref({
  totalTransactions: 0,
  totalCostValue: 0,
  thisMonth: 0,
  today: 0
})

// Computed
const filteredTransactions = computed(() => {
  let filtered = transactions.value

  // Filter by type
  if (filterType.value) {
    filtered = filtered.filter(t => t.TransactionType === filterType.value)
  }

  // Filter by search term
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(t => {
      const id = t.TransactionID.toString()
      const gadgetName = (t.gadget?.GadgetName || '').toLowerCase()
      return id.includes(term) || gadgetName.includes(term)
    })
  }

  return filtered
})

// Methods
const fetchTransactions = async () => {
  try {
    loading.value = true
    const response = await api('/transactions')
    
    // Handle paginated response
    if (response.data) {
      transactions.value = response.data
      pagination.value = {
        current_page: response.current_page,
        last_page: response.last_page,
        per_page: response.per_page,
        total: response.total
      }
    } else if (Array.isArray(response)) {
      transactions.value = response
    } else {
      transactions.value = []
    }
    
    // Calculate stats
    const now = new Date()
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1)
    const startOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    
    stats.value = {
      totalTransactions: transactions.value.length,
      totalCostValue: transactions.value.reduce((sum, t) => sum + calculateTotal(t), 0),
      thisMonth: transactions.value.filter(t => {
        const date = new Date(t.TransactionDate)
        return date >= startOfMonth
      }).length,
      today: transactions.value.filter(t => {
        const date = new Date(t.TransactionDate)
        return date >= startOfDay
      }).length
    }
  } catch (err: any) {
    console.error('Error fetching transactions:', err)
    message.value = err.data?.message || err.message || 'Failed to load transactions.'
    messageType.value = 'error'
  } finally {
    loading.value = false
  }
}

const calculateTotal = (transaction: any) => {
  if (transaction.stock?.CostPrice) {
    return transaction.Quantity * transaction.stock.CostPrice
  }
  return 0
}

const clearFilters = () => {
  searchTerm.value = ''
  filterType.value = ''
}

const goToPage = (page: number) => {
  // For now, just refetch - in a real app, you'd pass page as query param
  fetchTransactions()
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price || 0)
}

const formatDate = (date: string | Date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

// Lifecycle
onMounted(() => {
  fetchTransactions()
})
</script>

