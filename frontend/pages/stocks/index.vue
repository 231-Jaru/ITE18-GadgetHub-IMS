<template>
  <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl px-4 sm:px-8 py-6 sm:py-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="mb-2 md:mb-0">
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-1 sm:mb-2">
            <i class="fas fa-boxes mr-2"></i> Stock Management
          </h1>
          <p class="text-sm sm:text-base md:text-lg opacity-90">
            Comprehensive stock tracking and inventory management system
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
          <NuxtLink 
            to="/stock-adjustments/create" 
            class="w-full sm:w-auto text-center px-5 py-2.5 sm:px-6 sm:py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors font-semibold shadow-md text-sm sm:text-base flex items-center justify-center"
          >
            <i class="fas fa-sliders mr-2"></i> Create Adjustment
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Delete Success Notification -->
    <DeleteSuccessNotification 
      :show="showDeleteSuccess" 
      message="Stock record deleted successfully!"
    />

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal
      :show="showDeleteModal"
      :deleting="deleting"
      item-type="stock record"
      message="Are you sure you want to delete this stock record?"
      warning="This action cannot be undone. All associated data will be permanently removed."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />

    <!-- Success/Error Messages -->
    <div v-if="message && !showDeleteSuccess" :class="`mb-4 p-4 rounded-lg flex items-center justify-between ${messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'}`">
      <div class="flex items-center">
        <i :class="`fas ${messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2`"></i>
        <span>{{ message }}</span>
      </div>
      <button @click="message = ''" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <div class="stats-card primary">
        <div class="text-center py-4 px-3 sm:p-6">
          <div class="icon-wrapper">
            <i class="fas fa-boxes"></i>
          </div>
          <h4 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">
            <AnimatedNumber :value="stats.total_stocks || 0" />
          </h4>
          <p class="text-gray-600 font-medium text-xs sm:text-sm">Total Stock Entries</p>
        </div>
      </div>
      <div class="stats-card success">
        <div class="text-center py-4 px-3 sm:p-6">
          <div class="icon-wrapper">
            <i class="fas fa-check-circle"></i>
          </div>
          <h4 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">
            <AnimatedNumber :value="stats.well_stocked || 0" />
          </h4>
          <p class="text-gray-600 font-medium text-xs sm:text-sm">Well Stocked</p>
        </div>
      </div>
      <div class="stats-card warning">
        <div class="text-center py-4 px-3 sm:p-6">
          <div class="icon-wrapper">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h4 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">
            <AnimatedNumber :value="stats.low_stock || 0" />
          </h4>
          <p class="text-gray-600 font-medium text-xs sm:text-sm">Low Stock</p>
        </div>
      </div>
      <div class="stats-card info">
        <div class="text-center py-4 px-3 sm:p-6">
          <div class="icon-wrapper">
            <i class="fas fa-mobile-alt"></i>
          </div>
          <h4 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">
            <AnimatedNumber :value="stats.unique_gadgets || 0" />
          </h4>
          <p class="text-gray-600 font-medium text-xs sm:text-sm">Unique Gadgets</p>
        </div>
      </div>
    </div>

    <!-- Main Stocks Table -->
    <div ref="stockInventoryRef" class="bg-white rounded-xl shadow-lg mb-6">
      <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-xl">
        <h5 class="text-xl font-semibold">
          <i class="fas fa-list mr-2"></i> Stock Inventory
        </h5>
      </div>
      <div class="p-6">
        <!-- Search and Filter -->
        <div class="mb-4 flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <input 
              type="text" 
              v-model="searchTerm"
              placeholder="Search by gadget name, category, brand, or supplier..."
              class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all"
            >
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
          <p class="mt-4 text-gray-600">Loading stocks...</p>
        </div>

        <!-- Stocks Table -->
        <div v-else class="overflow-x-auto -mx-6 px-6">
          <table class="w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cost Price</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Purchase Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="stock in filteredStocks" 
                :key="stock.StockID"
                class="hover:bg-gray-50 transition-colors"
                :class="{ 'bg-yellow-50 border-l-4 border-yellow-400': isPotentialDuplicate(stock) }"
              >
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <span>{{ stock.StockID }}</span>
                    <span v-if="isPotentialDuplicate(stock)" 
                      class="px-2 py-0.5 bg-yellow-200 text-yellow-800 rounded text-xs font-semibold"
                      title="Potential duplicate entry"
                    >
                      <i class="fas fa-exclamation-triangle mr-1"></i>Duplicate
                    </span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="font-semibold">{{ stock.gadget?.GadgetName || 'N/A' }}</div>
                  <div v-if="stock.gadget" class="text-sm text-gray-500">
                    {{ stock.gadget.category?.CategoryName || 'N/A' }} - {{ stock.gadget.brand?.BrandName || 'N/A' }}
                  </div>
                </td>
                <td class="px-4 py-3">{{ stock.supplier?.SupplierName || 'N/A' }}</td>
                <td class="px-4 py-3">
                  <span :class="`px-3 py-1 rounded-full text-sm font-semibold ${
                    stock.QuantityAdded === 0 ? 'bg-red-100 text-red-800' :
                    stock.QuantityAdded <= 10 ? 'bg-yellow-100 text-yellow-800' :
                    'bg-green-100 text-green-800'
                  }`">
                    {{ stock.QuantityAdded }}
                  </span>
                </td>
                <td class="px-4 py-3">₱{{ formatPrice(stock.CostPrice) }}</td>
                <td class="px-4 py-3">{{ formatDate(stock.PurchaseDate) }}</td>
                <td class="px-4 py-3" @click.stop.prevent>
                  <div class="dropdown relative" @click.stop.prevent>
                    <button 
                      @click.stop.prevent="toggleActionsMenu(stock.StockID, $event)"
                      type="button"
                      class="px-3 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors"
                      :aria-label="`Actions menu for stock ${stock.StockID}`"
                      :aria-expanded="activeActionsMenu === stock.StockID"
                    >
                      <i class="fas fa-ellipsis-v text-gray-600"></i>
                    </button>
                    <div 
                      v-if="activeActionsMenu === stock.StockID"
                      :class="[
                        'absolute right-0 w-40 bg-white border border-gray-200 rounded-lg shadow-xl z-[9999] dropdown-menu',
                        dropdownPosition === 'above' ? 'bottom-full mb-1' : 'top-full mt-1'
                      ]"
                      @click.stop
                      role="menu"
                    >
                      <button 
                        @click.stop="handleEdit(stock.StockID)"
                        type="button"
                        class="w-full text-left px-4 py-2.5 text-indigo-600 hover:bg-gray-50 transition-colors flex items-center dropdown-item"
                        role="menuitem"
                      >
                        <i class="fas fa-edit mr-2"></i> Edit
                      </button>
                      <hr class="my-1 border-gray-200">
                      <button 
                        @click.stop.prevent="deleteStock(stock.StockID)"
                        type="button"
                        class="w-full text-left px-4 py-2.5 text-red-600 hover:bg-gray-50 transition-colors flex items-center dropdown-item"
                        role="menuitem"
                      >
                        <i class="fas fa-trash mr-2"></i> Delete
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredStocks.length === 0">
                <td colspan="7" class="px-4 py-12 text-center">
                  <i class="fas fa-boxes text-6xl text-gray-300 mb-4"></i>
                  <p class="text-gray-500">No stock records found.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 pb-6">
          <Pagination 
            v-if="pagination && !loading"
            :pagination="pagination"
            @page-change="goToPage"
          />
        </div>
      </div>
    </div>

    <!-- Gadgets Without Stock Section -->
    <div v-if="gadgetsWithoutStock.length > 0" class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
      <div class="bg-yellow-500 text-white px-6 py-4 flex justify-between items-center">
        <h5 class="text-xl font-semibold">
          <i class="fas fa-exclamation-triangle mr-2"></i> Gadgets Without Stock
        </h5>
        <span class="bg-gray-800 text-white px-3 py-1 rounded-full text-sm font-semibold">
          {{ gadgetsWithoutStock.length }} gadgets
        </span>
      </div>
      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-yellow-50">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Brand</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="gadget in gadgetsWithoutStock" 
                :key="gadget.GadgetID"
                :class="selectedGadgetId === gadget.GadgetID ? 'bg-yellow-50' : ''"
              >
                <td class="px-4 py-3">
                  <div class="font-semibold">{{ gadget.GadgetName }}</div>
                  <span v-if="selectedGadgetId === gadget.GadgetID" class="inline-block mt-1 px-2 py-1 bg-yellow-500 text-gray-800 rounded text-xs font-semibold">
                    Selected
                  </span>
                </td>
                <td class="px-4 py-3">{{ gadget.category?.CategoryName || 'N/A' }}</td>
                <td class="px-4 py-3">{{ gadget.brand?.BrandName || 'N/A' }}</td>
                <td class="px-4 py-3">
                  <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                    <i class="fas fa-times-circle mr-1"></i> No Stock
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex flex-col gap-1.5">
                    <NuxtLink 
                      :to="`/purchase-orders/create?gadget=${gadget.GadgetID}`"
                      class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors text-xs sm:text-sm font-semibold"
                    >
                      <i class="fas fa-shopping-cart"></i>
                      <span>Order</span>
                    </NuxtLink>
                    <NuxtLink 
                      :to="`/stock-adjustments/create?gadget=${gadget.GadgetID}`"
                      class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors text-xs sm:text-sm font-semibold"
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
      </div>
    </div>

    <!-- Low Stock Items Section -->
    <div v-if="lowStockItems.length > 0" class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
      <div class="bg-yellow-500 text-white px-6 py-4 flex justify-between items-center">
        <h5 class="text-xl font-semibold">
          <i class="fas fa-exclamation-triangle mr-2"></i> Low Stock Items (≤10 units)
        </h5>
        <span class="bg-white text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
          {{ lowStockItems.length }} items
        </span>
      </div>
      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-yellow-50">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Brand</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Current Stock</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Last Purchase</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="stock in lowStockItems" 
                :key="stock.StockID"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-3">
                  <div class="font-semibold">{{ stock.gadget?.GadgetName || 'N/A' }}</div>
                  <div v-if="stock.gadget" class="text-sm text-gray-500">
                    {{ stock.gadget.category?.CategoryName || 'N/A' }} - {{ stock.gadget.brand?.BrandName || 'N/A' }}
                  </div>
                </td>
                <td class="px-4 py-3">{{ stock.gadget?.category?.CategoryName || 'N/A' }}</td>
                <td class="px-4 py-3">{{ stock.gadget?.brand?.BrandName || 'N/A' }}</td>
                <td class="px-4 py-3">
                  <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                    {{ stock.QuantityAdded }}
                  </span>
                </td>
                <td class="px-4 py-3">{{ stock.supplier?.SupplierName || 'N/A' }}</td>
                <td class="px-4 py-3">{{ formatDate(stock.PurchaseDate) }}</td>
                <td class="px-4 py-3" @click.stop>
                  <div class="flex flex-col gap-1.5">
                    <NuxtLink 
                      :to="`/purchase-orders/create?gadget=${stock.gadget?.GadgetID}&supplier=${stock.supplier?.SupplierID || ''}`"
                      class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors text-xs sm:text-sm font-medium"
                    >
                      <i class="fas fa-shopping-cart"></i>
                      <span>Order</span>
                    </NuxtLink>
                    <NuxtLink 
                      :to="`/stock-adjustments/create?gadget=${stock.gadget?.GadgetID}`"
                      class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors text-xs sm:text-sm font-medium"
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
      </div>
    </div>

    <!-- Out of Stock Items Section -->
    <div v-if="outOfStockItems.length > 0" class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
      <div class="bg-red-600 text-white px-6 py-4 flex justify-between items-center">
        <h5 class="text-xl font-semibold">
          <i class="fas fa-times-circle mr-2"></i> Out of Stock Items (0 units)
        </h5>
        <span class="bg-white text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
          {{ outOfStockItems.length }} items
        </span>
      </div>
      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-red-50">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Brand</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Current Stock</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Last Purchase</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="stock in outOfStockItems" 
                :key="stock.StockID"
                class="hover:bg-gray-50"
              >
                <td class="px-4 py-3">
                  <div class="font-semibold">{{ stock.gadget?.GadgetName || 'N/A' }}</div>
                  <div v-if="stock.gadget" class="text-sm text-gray-500">
                    {{ stock.gadget.category?.CategoryName || 'N/A' }} - {{ stock.gadget.brand?.BrandName || 'N/A' }}
                  </div>
                </td>
                <td class="px-4 py-3">{{ stock.gadget?.category?.CategoryName || 'N/A' }}</td>
                <td class="px-4 py-3">{{ stock.gadget?.brand?.BrandName || 'N/A' }}</td>
                <td class="px-4 py-3">
                  <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                    {{ stock.QuantityAdded }}
                  </span>
                </td>
                <td class="px-4 py-3">{{ stock.supplier?.SupplierName || 'N/A' }}</td>
                <td class="px-4 py-3">{{ formatDate(stock.PurchaseDate) }}</td>
                <td class="px-4 py-3">
                  <div class="flex flex-col gap-1.5">
                    <NuxtLink 
                      :to="`/purchase-orders/create?gadget=${stock.gadget?.GadgetID}&supplier=${stock.supplier?.SupplierID || ''}`"
                      class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors text-xs sm:text-sm font-medium"
                    >
                      <i class="fas fa-shopping-cart"></i>
                      <span>Order</span>
                    </NuxtLink>
                    <NuxtLink 
                      :to="`/stock-adjustments/create?gadget=${stock.gadget?.GadgetID}`"
                      class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors text-xs sm:text-sm font-medium"
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
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
const route = useRoute()
// @ts-ignore - Nuxt auto-imports
const router = useRouter()
// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

// State
const stocks = ref<any[]>([])
const stats = ref<any>({})
const gadgetsWithoutStock = ref<any[]>([])
const lowStockItems = ref<any[]>([])
const outOfStockItems = ref<any[]>([])
const suppliers = ref<any[]>([])
const loading = ref(true)
const searchTerm = ref('')
const message = ref('')
const messageType = ref<'success' | 'error'>('success')
const pagination = ref<any>(null)
const activeActionsMenu = ref<number | null>(null)
const dropdownPosition = ref<'above' | 'below'>('below')
const showDeleteModal = ref(false)
const showDeleteSuccess = ref(false)
const deleting = ref(false)
const stockToDelete = ref<number | null>(null)
const selectedGadgetId = ref<number | null>(null)
const stockInventoryRef = ref<HTMLElement | null>(null)

// Computed
const filteredStocks = computed(() => {
  if (!searchTerm.value) {
    return stocks.value
  }
  const term = searchTerm.value.toLowerCase()
  return stocks.value.filter(stock => {
    const gadgetName = (stock.gadget?.GadgetName || '').toLowerCase()
    const category = (stock.gadget?.category?.CategoryName || '').toLowerCase()
    const brand = (stock.gadget?.brand?.BrandName || '').toLowerCase()
    const supplier = (stock.supplier?.SupplierName || '').toLowerCase()
    return gadgetName.includes(term) || category.includes(term) || brand.includes(term) || supplier.includes(term)
  })
})

// Methods
const fetchStocks = async (page: number = 1) => {
  try {
    loading.value = true
    const response = await api(`/stocks?page=${page}`) as any
    
    // Handle paginated response and remove duplicates
    let fetchedStocks: any[] = []
    if (response.stocks && response.stocks.data) {
      fetchedStocks = response.stocks.data
      pagination.value = {
        current_page: response.stocks.current_page || page,
        last_page: response.stocks.last_page || 1,
        per_page: response.stocks.per_page || 15,
        total: response.stocks.total || 0
      }
    } else if (response.stocks && Array.isArray(response.stocks)) {
      fetchedStocks = response.stocks
      pagination.value = null
    } else if (Array.isArray(response)) {
      fetchedStocks = response
      pagination.value = null
    } else {
      fetchedStocks = response.stocks?.data || response.data || []
      pagination.value = null
    }
    
    // Remove duplicates based on StockID
    stocks.value = fetchedStocks.filter((stock: any, index: number, self: any[]) => 
      index === self.findIndex((s: any) => s.StockID === stock.StockID)
    )
    
    // Use stats from backend (calculated from ALL stocks, not just paginated)
    stats.value = {
      total_stocks: response.stats?.total_stocks || 0,
      well_stocked: response.stats?.well_stocked || 0,
      low_stock: response.stats?.low_stock || 0,
      unique_gadgets: response.stats?.unique_gadgets || 0
    }
    
    // Use backend-provided lists to avoid duplicates and remove any duplicates
    // Ensure it's always an array
    const gadgetsWithoutStockList = Array.isArray(response.gadgets_without_stock) 
      ? response.gadgets_without_stock 
      : (response.gadgets_without_stock?.data || [])
    gadgetsWithoutStock.value = Array.isArray(gadgetsWithoutStockList)
      ? gadgetsWithoutStockList.filter((gadget: any, index: number, self: any[]) => 
          index === self.findIndex((g: any) => g.GadgetID === gadget.GadgetID)
        )
      : []
    
    const lowStockList = Array.isArray(response.low_stock_items) 
      ? response.low_stock_items 
      : (response.low_stock_items?.data || [])
    lowStockItems.value = Array.isArray(lowStockList)
      ? lowStockList.filter((stock: any, index: number, self: any[]) => 
          index === self.findIndex((s: any) => s.StockID === stock.StockID)
        )
      : []
    
    const outOfStockList = Array.isArray(response.out_of_stock_items) 
      ? response.out_of_stock_items 
      : (response.out_of_stock_items?.data || [])
    outOfStockItems.value = Array.isArray(outOfStockList)
      ? outOfStockList.filter((stock: any, index: number, self: any[]) => 
          index === self.findIndex((s: any) => s.StockID === stock.StockID)
        )
      : []
    
    const suppliersList = response.suppliers || []
    suppliers.value = suppliersList.filter((supplier: any, index: number, self: any[]) => 
      index === self.findIndex((s: any) => s.SupplierID === supplier.SupplierID)
    )
    
    // Check for selected gadget from query (for highlighting)
    if (route.query.gadget) {
      selectedGadgetId.value = Number(route.query.gadget)
    }
  } catch (err: any) {
    console.error('Error fetching stocks:', err)
    const errorMessage = err.data?.message || err.message || 'Failed to load stocks.'
    message.value = errorMessage + ' Please try again or refresh the page.'
    messageType.value = 'error'
    stocks.value = [] // Clear on error
  } finally {
    loading.value = false
  }
}

const viewStock = (id: number) => {
  router.push(`/stocks/${id}`)
}

const toggleActionsMenu = async (id: number, event?: Event) => {
  const wasOpen = activeActionsMenu.value === id
  activeActionsMenu.value = wasOpen ? null : id
  
  if (!wasOpen && event) {
    await nextTick()
    // Calculate optimal dropdown position based on available space
    const button = (event.target as HTMLElement).closest('button') as HTMLElement
    if (!button) return
    
    const buttonRect = button.getBoundingClientRect()
    const viewportHeight = window.innerHeight
    const viewportWidth = window.innerWidth
    const spaceBelow = viewportHeight - buttonRect.bottom
    const spaceAbove = buttonRect.top
    const spaceRight = viewportWidth - buttonRect.right
    
    // Approximate dropdown dimensions (2 buttons + padding)
    const dropdownHeight = 110 // Edit + Delete buttons + padding
    const dropdownWidth = 160 // w-40 = 10rem = 160px
    const buffer = 20 // Extra buffer space
    
    // Check if dropdown would fit below
    const fitsBelow = spaceBelow >= (dropdownHeight + buffer)
    
    // Check if dropdown would fit above
    const fitsAbove = spaceAbove >= (dropdownHeight + buffer)
    
    // Determine position:
    // 1. If fits below, show below (preferred for top/middle rows)
    // 2. If doesn't fit below but fits above, show above (bottom rows)
    // 3. If neither fits perfectly, choose the side with more space
    if (fitsBelow) {
      dropdownPosition.value = 'below'
    } else if (fitsAbove) {
      dropdownPosition.value = 'above'
    } else {
      // Choose side with more space as fallback
      dropdownPosition.value = spaceAbove > spaceBelow ? 'above' : 'below'
    }
  }
}

const handleEdit = (id: number) => {
  activeActionsMenu.value = null
  router.push(`/stocks/${id}/edit`)
}

const deleteStock = (id: number) => {
  stockToDelete.value = id
  showDeleteModal.value = true
  activeActionsMenu.value = null
}

const confirmDelete = async () => {
  if (!stockToDelete.value) return
  
  try {
    deleting.value = true
    await api(`/stocks/${stockToDelete.value}`, { method: 'DELETE' })
    
    showDeleteModal.value = false
    showDeleteSuccess.value = true
    
    // Refresh stocks - maintain current page if possible
    const currentPage = pagination.value?.current_page || 1
    await fetchStocks(currentPage)
    
    // If current page is empty after deletion, go to previous page
    if (pagination.value && stocks.value.length === 0 && pagination.value.current_page > 1) {
      await fetchStocks(pagination.value.current_page - 1)
    }
    
    stockToDelete.value = null
  } catch (err: any) {
    console.error('Error deleting stock:', err)
    const errorMessage = err.data?.message || err.message || 'Failed to delete stock record.'
    message.value = errorMessage + ' Please try again.'
    messageType.value = 'error'
    showDeleteModal.value = false
    stockToDelete.value = null
  } finally {
    deleting.value = false
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
  stockToDelete.value = null
}


// Check if a stock entry is a potential duplicate
const isPotentialDuplicate = (stock: any): boolean => {
  if (!stocks.value || stocks.value.length === 0) return false
  
  // Find other stocks with same gadget, supplier, date, price, and quantity
  const duplicates = stocks.value.filter((s: any) => 
    s.StockID !== stock.StockID &&
    s.GadgetID === stock.GadgetID &&
    s.SupplierID === stock.SupplierID &&
    s.PurchaseDate === stock.PurchaseDate &&
    parseFloat(s.CostPrice || 0) === parseFloat(stock.CostPrice || 0) &&
    parseInt(s.QuantityAdded || 0) === parseInt(stock.QuantityAdded || 0)
  )
  
  return duplicates.length > 0
}

const goToPage = (page: number) => {
  if (!pagination.value) return
  if (page < 1 || page > pagination.value.last_page) return
  if (page === pagination.value.current_page) return
  
  fetchStocks(page)
  
  // Scroll to stock inventory list section
  if (process.client) {
    setTimeout(() => {
      if (stockInventoryRef.value) {
        stockInventoryRef.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
      } else {
        // Fallback to top of page if ref not available
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }
    }, 100) // Small delay to ensure DOM is updated
  }
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

// Close actions menu when clicking outside or scrolling
onMounted(() => {
  fetchStocks()
  
  // Close menu when clicking outside
  const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement
    // Don't close if clicking inside the dropdown menu or buttons
    if (
      target.closest('.dropdown') || 
      target.closest('.dropdown-menu') ||
      target.closest('.dropdown-item') ||
      target.closest('button')
    ) {
      return
    }
      activeActionsMenu.value = null
    }
  
  // Close menu when scrolling (dropdown position might change)
  const handleScroll = () => {
    if (activeActionsMenu.value !== null) {
      activeActionsMenu.value = null
    }
  }
  
  // Close menu on window resize (viewport might change)
  const handleResize = () => {
    if (activeActionsMenu.value !== null) {
      activeActionsMenu.value = null
    }
  }
  
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('scroll', handleScroll, true)
  window.addEventListener('resize', handleResize)
  
  // Cleanup on unmount
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    window.removeEventListener('scroll', handleScroll, true)
    window.removeEventListener('resize', handleResize)
  })
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
