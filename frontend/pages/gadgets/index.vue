<template>
  <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
    <!-- Success/Error Messages -->
    <div 
      v-if="message" 
      :class="[
        'mb-4 p-4 rounded-lg flex items-center justify-between',
        messageType === 'success' 
          ? 'bg-green-50 text-green-800 border border-green-200' 
          : 'bg-red-50 text-red-800 border border-red-200'
      ]" 
      role="alert"
    >
      <div class="flex items-center">
        <i :class="[messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle', 'mr-2']"></i>
        <span>{{ message }}</span>
      </div>
      <button type="button" class="text-gray-500 hover:text-gray-700" @click="message = ''">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl px-4 sm:px-8 py-6 sm:py-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="mb-2 md:mb-0">
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-1 sm:mb-2">
            <i class="fas fa-mobile-alt mr-2"></i> Gadgets Management
          </h1>
          <p class="text-sm sm:text-base md:text-lg opacity-90">
            View and manage all your products and inventory items
          </p>
        </div>
        <button 
          @click="openAddGadgetModal" 
          class="w-full sm:w-auto justify-center px-5 py-2.5 sm:px-6 sm:py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md relative z-10 flex items-center text-sm sm:text-base"
          :class="{ 'opacity-50 pointer-events-none': showAddModal }"
        >
          <i class="fas fa-plus mr-2"></i> Add New Gadget
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="stats-card primary">
        <div class="text-center p-6">
          <div class="icon-wrapper">
            <i class="fas fa-microchip"></i>
          </div>
          <h3 class="text-3xl font-bold mb-2">
            <AnimatedNumber :value="gadgets.length" />
          </h3>
          <p class="text-gray-600 font-medium">Total Gadgets</p>
        </div>
      </div>
      <div class="stats-card success">
        <div class="text-center p-6">
          <div class="icon-wrapper">
            <i class="fas fa-layer-group"></i>
          </div>
          <h3 class="text-3xl font-bold mb-2">
            <AnimatedNumber :value="uniqueCategories" />
          </h3>
          <p class="text-gray-600 font-medium">Categories</p>
        </div>
      </div>
      <div class="stats-card warning">
        <div class="text-center p-6">
          <div class="icon-wrapper">
            <i class="fas fa-award"></i>
          </div>
          <h3 class="text-3xl font-bold mb-2">
            <AnimatedNumber :value="uniqueBrands" />
          </h3>
          <p class="text-gray-600 font-medium">Brands</p>
        </div>
      </div>
      <div class="stats-card info">
        <div class="text-center p-6">
          <div class="icon-wrapper">
            <i class="fas fa-warehouse"></i>
          </div>
          <h3 class="text-3xl font-bold mb-2">
            <AnimatedNumber :value="totalStock" />
          </h3>
          <p class="text-gray-600 font-medium">Total Stock Entries</p>
        </div>
      </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="search-filter-section mb-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex-1 lg:max-w-md">
          <div class="relative">
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
              <i class="fas fa-search"></i>
            </span>
            <input 
              type="text" 
              class="search-box w-full pl-12 pr-4 py-3"
              v-model="searchTerm"
              placeholder="Search gadgets by name, category, or brand..."
            >
          </div>
        </div>
        <div class="flex flex-wrap gap-2">
          <button 
            class="filter-btn" 
            :class="{ 'active': activeFilter === 'all' }"
            @click="setFilter('all')"
          >
            <i class="fas fa-th-large mr-1"></i> All
          </button>
          <button 
            v-for="category in availableCategories" 
            :key="category"
            class="filter-btn"
            :class="{ 'active': activeFilter === category }"
            @click="setFilter(category)"
          >
            <i class="fas fa-tag mr-1"></i> {{ category }}
          </button>
        </div>
      </div>
      <div v-if="filteredGadgets.length !== gadgets.length" class="text-gray-500 text-sm mt-3">
        {{ filteredGadgets.length }} gadget{{ filteredGadgets.length !== 1 ? 's' : '' }} found
      </div>
    </div>

    <!-- Gadgets Grid -->
    <div
      ref="gadgetsGridRef"
      v-if="!loading"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6"
    >
      <div 
        v-for="gadget in paginatedGadgets" 
        :key="gadget.GadgetID"
        class="gadget-card"
        @click="viewGadget(gadget.GadgetID)"
      >
        <div class="relative">
          <div class="gadget-image">
            <i :class="getCategoryIcon(gadget.category?.CategoryName)"></i>
          </div>
          <div 
            class="stock-badge"
            :class="getStockBadgeClass(getTotalStock(gadget))"
          >
            <i :class="getStockIcon(getTotalStock(gadget))" class="mr-1"></i>
            {{ getStockStatus(getTotalStock(gadget)) }}
          </div>
        </div>
        <div class="p-4 sm:p-6">
          <h5 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-800 break-words">
            {{ gadget.GadgetName }}
          </h5>
          
          <div class="mb-4">
            <span class="category-badge">
              <i class="fas fa-tag mr-1"></i> {{ gadget.category?.CategoryName || 'No Category' }}
            </span>
            <span class="brand-badge">
              <i class="fas fa-star mr-1"></i> {{ gadget.brand?.BrandName || 'No Brand' }}
            </span>
          </div>

          <div class="stats-row">
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
              <div class="stat-item primary text-center">
                <h6 class="text-xs sm:text-sm text-gray-600 mb-0.5 sm:mb-1">Stock</h6>
                <h5 class="text-base sm:text-lg font-bold text-indigo-600">
                  {{ getTotalStock(gadget) }}
                </h5>
              </div>
              <div class="stat-item success text-center">
                <h6 class="text-xs sm:text-sm text-gray-600 mb-0.5 sm:mb-1">Cost Price</h6>
                <h5 class="text-base sm:text-lg font-bold text-green-600">
                  {{ formatPrice(getCostPrice(gadget)) }}
                </h5>
              </div>
            </div>
          </div>

          <div class="action-buttons mt-3 sm:mt-4">
            <NuxtLink 
              :to="`/gadgets/${gadget.GadgetID}`"
              class="btn-action btn-view"
              @click.stop
            >
              <i class="fas fa-eye mr-1"></i> View
            </NuxtLink>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-if="filteredGadgets.length === 0" class="col-span-full">
        <div class="empty-state">
          <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
          <h4 class="text-xl font-semibold text-gray-600 mb-2">No gadgets found</h4>
          <p class="text-gray-500 mb-6">Start building your inventory by adding your first gadget.</p>
          <button 
            @click="openAddGadgetModal" 
            class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors relative z-10"
            :class="{ 'opacity-50 pointer-events-none': showAddModal }"
          >
            <i class="fas fa-plus mr-2"></i> Add First Gadget
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading gadgets...</p>
    </div>

    <!-- Add Gadget Modal -->
    <AddGadgetModal 
      v-if="showAddModal"
      :show="showAddModal"
      @close="showAddModal = false"
      @created="handleGadgetCreated"
    />

    <!-- Gadget Details Modal -->
    <GadgetDetailsModal
      v-if="selectedGadgetId"
      :gadget-id="selectedGadgetId"
      @close="selectedGadgetId = null"
    />

    <!-- Pagination -->
    <Pagination 
      v-if="pagination && !loading"
      :pagination="pagination"
      @page-change="goToPage"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
const { api } = useApi()
// @ts-ignore - Nuxt auto-imports
const router = useRouter()

// State
const gadgets = ref<any[]>([])
const loading = ref(true)
const searchTerm = ref('')
const activeFilter = ref('all')
const message = ref('')
const messageType = ref<'success' | 'error'>('success')
const showAddModal = ref(false)
const selectedGadgetId = ref<number | null>(null)
const currentPage = ref(1)
const itemsPerPage = ref(12)
const gadgetsGridRef = ref<HTMLElement | null>(null)

// Computed
const uniqueCategories = computed(() => {
  const categories = gadgets.value
    .map(g => g.category?.CategoryName)
    .filter(Boolean)
  return new Set(categories).size
})

const uniqueBrands = computed(() => {
  const brands = gadgets.value
    .map(g => g.brand?.BrandName)
    .filter(Boolean)
  return new Set(brands).size
})

const totalStock = computed(() => {
  // Count total number of stock entries (not sum of quantities)
  // This matches the "Total Stock Entries" count on the stocks page
  // Eloquent relationships automatically exclude soft-deleted records
  return gadgets.value.reduce((count, gadget) => {
    const stocks = gadget.stocks || []
    return count + stocks.length
  }, 0)
})

const availableCategories = computed(() => {
  const categories = gadgets.value
    .map(g => g.category?.CategoryName)
    .filter(Boolean)
  return Array.from(new Set(categories)).sort()
})

const filteredGadgets = computed(() => {
  let filtered = gadgets.value

  // Apply category filter
  if (activeFilter.value !== 'all') {
    filtered = filtered.filter(g => g.category?.CategoryName === activeFilter.value)
  }

  // Apply search filter
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(g => {
      const name = (g.GadgetName || '').toLowerCase()
      const category = (g.category?.CategoryName || '').toLowerCase()
      const brand = (g.brand?.BrandName || '').toLowerCase()
      return name.includes(term) || category.includes(term) || brand.includes(term)
    })
  }

  return filtered
})

const paginatedGadgets = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredGadgets.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredGadgets.value.length / itemsPerPage.value)
})

const pagination = computed(() => {
  if (totalPages.value <= 1) return null
  return {
    current_page: currentPage.value,
    last_page: totalPages.value,
    per_page: itemsPerPage.value,
    total: filteredGadgets.value.length
  }
})

// Methods
const fetchGadgets = async () => {
  try {
    loading.value = true
    const response: any = await api('/gadgets')
    const fetchedGadgets = response.data || response
    
    // Ensure we have an array and remove duplicates by GadgetID
    if (Array.isArray(fetchedGadgets)) {
      // Remove duplicates based on GadgetID
      const uniqueGadgets = fetchedGadgets.filter((gadget: any, index: number, self: any[]) => 
        index === self.findIndex((g: any) => g.GadgetID === gadget.GadgetID)
      )
      gadgets.value = uniqueGadgets
    } else {
      gadgets.value = []
    }
  } catch (error: any) {
    console.error('Error fetching gadgets:', error)
    const errorMessage = error.data?.message || error.message || 'Failed to load gadgets.'
    message.value = errorMessage + ' Please try again or refresh the page.'
    messageType.value = 'error'
    gadgets.value = [] // Clear on error to avoid stale data
  } finally {
    loading.value = false
  }
}

const goToPage = (page: number) => {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
  // Scroll to gadgets grid section
  if (process.client) {
    setTimeout(() => {
      if (gadgetsGridRef.value) {
        gadgetsGridRef.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
      }
    }, 100)
  }
}

watch([searchTerm, activeFilter], () => {
  currentPage.value = 1 // Reset to first page when filters change
})

const setFilter = (filter: string) => {
  activeFilter.value = filter
}

const getTotalStock = (gadget: any) => {
  if (!gadget.stocks || !Array.isArray(gadget.stocks)) return 0
  return gadget.stocks.reduce((sum: number, stock: any) => {
    return sum + (parseInt(stock.QuantityAdded) || 0)
  }, 0)
}

const getCostPrice = (gadget: any) => {
  if (!gadget.stocks || !Array.isArray(gadget.stocks)) return 0
  const availableStock = gadget.stocks.find((s: any) => (parseInt(s.QuantityAdded) || 0) > 0)
  if (availableStock && availableStock.CostPrice) {
    return parseFloat(availableStock.CostPrice) || 0
  }
  if (gadget.stocks.length > 0 && gadget.stocks[0].CostPrice) {
    return parseFloat(gadget.stocks[0].CostPrice) || 0
  }
  return 0
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP'
  }).format(price)
}

const getCategoryIcon = (categoryName?: string) => {
  const name = (categoryName || '').toLowerCase()
  if (name.includes('phone') || name.includes('smartphone')) return 'fas fa-mobile-alt'
  if (name.includes('laptop') || name.includes('computer')) return 'fas fa-laptop'
  if (name.includes('tablet')) return 'fas fa-tablet-alt'
  if (name.includes('headphone') || name.includes('audio')) return 'fas fa-headphones'
  if (name.includes('camera')) return 'fas fa-camera'
  if (name.includes('watch') || name.includes('smartwatch')) return 'fas fa-clock'
  if (name.includes('gaming') || name.includes('console')) return 'fas fa-gamepad'
  if (name.includes('tv') || name.includes('television')) return 'fas fa-tv'
  if (name.includes('speaker') || name.includes('sound')) return 'fas fa-volume-up'
  if (name.includes('keyboard') || name.includes('mouse')) return 'fas fa-keyboard'
  return 'fas fa-mobile-alt'
}

const getStockBadgeClass = (stock: number) => {
  if (stock > 10) return 'in-stock'
  if (stock > 0) return 'low-stock'
  return 'out-of-stock'
}

const getStockIcon = (stock: number) => {
  if (stock > 10) return 'fas fa-check-circle'
  if (stock > 0) return 'fas fa-exclamation-triangle'
  return 'fas fa-times-circle'
}

const getStockStatus = (stock: number) => {
  if (stock > 10) return 'In Stock'
  if (stock > 0) return 'Low Stock'
  return 'Out of Stock'
}

const openAddGadgetModal = () => {
  showAddModal.value = true
}

const viewGadget = (id: number) => {
  router.push(`/gadgets/${id}`)
}

const handleGadgetCreated = () => {
  showAddModal.value = false
  message.value = 'Gadget created successfully!'
  messageType.value = 'success'
  fetchGadgets()
  setTimeout(() => {
    message.value = ''
  }, 5000)
}

// Lifecycle
onMounted(() => {
  fetchGadgets()
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

.search-filter-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  padding: 1.5rem;
}

.search-box {
  border: 2px solid #e5e7eb;
  border-radius: 50px;
  transition: all 0.3s;
}

.search-box:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
  outline: none;
}

.filter-btn {
  border: 2px solid #e5e7eb;
  border-radius: 50px;
  padding: 0.5rem 1.25rem;
  background: white;
  color: #6b7280;
  font-weight: 500;
  transition: all 0.3s;
}

.filter-btn:hover,
.filter-btn.active {
  background: #4f46e5;
  border-color: #4f46e5;
  color: white;
  transform: translateY(-1px);
}

.gadget-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  overflow: hidden;
}

.gadget-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.gadget-image {
  height: 200px;
  background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 3rem;
  position: relative;
}

.stock-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  z-index: 10;
  border-radius: 50px;
  padding: 0.5rem 1rem;
  font-weight: 600;
  font-size: 0.875rem;
}

.stock-badge.in-stock {
  background: #10b981; /* green-500 */
  color: white;
}

.stock-badge.low-stock {
  background: #eab308; /* yellow-500 - more vibrant yellow */
  color: white;
}

.stock-badge.out-of-stock {
  background: #ef4444; /* red-500 */
  color: white;
}

.category-badge,
.brand-badge {
  border-radius: 50px;
  padding: 0.375rem 0.875rem;
  font-size: 0.875rem;
  font-weight: 500;
  margin-right: 0.5rem;
  margin-bottom: 0.25rem;
  display: inline-block;
}

.category-badge {
  background: linear-gradient(135deg, #4f46e5, #3730a3);
  color: white;
}

.brand-badge {
  background: linear-gradient(135deg, #06b6d4, #0891b2);
  color: white;
}

.stats-row {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1rem;
  margin: 1rem 0;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn-action {
  flex: 1;
  min-width: 100px;
  border-radius: 50px;
  padding: 0.5rem 1rem;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.3s;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-view {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
}

.btn-view:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  color: white;
  transform: translateY(-1px);
}

.btn-edit {
  background: #f59e0b;
  color: white;
  border: none;
}

.btn-edit:hover {
  background: #d97706;
  color: white;
  transform: translateY(-1px);
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>
