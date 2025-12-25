<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Success Notification -->
    <SuccessNotification 
      :show="showSuccess" 
      message="Gadget updated successfully!"
    />
    
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading gadget details...</p>
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
        to="/gadgets" 
        class="ml-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
      >
        <i class="fas fa-arrow-left mr-1"></i>Back to Gadgets
      </NuxtLink>
    </div>

    <!-- Gadget Details -->
    <div v-else-if="gadget">
      <!-- Header Section -->
      <div class="gadget-header mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="md:w-2/3 mb-4 md:mb-0">
            <h1 class="text-4xl font-bold mb-3">
              <i class="fas fa-mobile-alt mr-2"></i>{{ gadget.GadgetName }}
            </h1>
            <div class="flex flex-wrap gap-3 mb-3">
              <span class="badge-custom">
                <i class="fas fa-tag mr-1"></i>{{ gadget.category?.CategoryName || 'No Category' }}
              </span>
              <span class="badge-custom">
                <i class="fas fa-star mr-1"></i>{{ gadget.brand?.BrandName || 'No Brand' }}
              </span>
            </div>
            <p class="opacity-75">Gadget ID: {{ gadget.GadgetID }}</p>
          </div>
          <div class="md:w-1/3 md:text-right">
            <div class="flex gap-2 justify-start md:justify-end relative z-10" :class="{ 'pointer-events-none': showEditModal }">
              <button 
                type="button"
                @click="openEditModal"
                class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-50 transition-colors font-semibold shadow-sm border border-indigo-200 cursor-pointer relative z-10"
                :class="{ 'opacity-50': showEditModal }"
              >
                <i class="fas fa-edit mr-2"></i>Edit
              </button>
              <button
                type="button"
                @click="goBack"
                class="inline-flex items-center px-4 py-2 bg-indigo-400 text-white rounded-lg hover:bg-indigo-500 transition-colors font-semibold shadow-sm cursor-pointer relative z-10"
                :class="{ 'opacity-50': showEditModal }"
              >
                <i class="fas fa-arrow-left mr-2"></i>Back
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="info-card">
          <div class="text-center p-6">
            <i class="fas fa-boxes text-4xl text-indigo-600 mb-3"></i>
            <h4 class="text-2xl font-bold mb-2">{{ totalStock }}</h4>
            <p class="text-gray-600 font-medium">Total Stock</p>
          </div>
        </div>
        <div class="info-card">
          <div class="text-center p-6">
            <i class="fas fa-peso-sign text-4xl text-green-600 mb-3"></i>
            <h4 class="text-2xl font-bold mb-2">{{ formatPrice(totalValue) }}</h4>
            <p class="text-gray-600 font-medium">Total Value</p>
          </div>
        </div>
        <div class="info-card">
          <div class="text-center p-6">
            <i class="fas fa-warehouse text-4xl text-blue-600 mb-3"></i>
            <h4 class="text-2xl font-bold mb-2">{{ stockEntries }}</h4>
            <p class="text-gray-600 font-medium">Stock Entries</p>
          </div>
        </div>
        <div class="info-card">
          <div class="text-center p-6">
            <i class="fas fa-truck text-4xl text-yellow-600 mb-3"></i>
            <h4 class="text-2xl font-bold mb-2">{{ uniqueSuppliers }}</h4>
            <p class="text-gray-600 font-medium">Suppliers</p>
          </div>
        </div>
      </div>

      <!-- Reorder Point Alert -->
      <div 
        v-if="totalStock <= (gadget.ReorderPoint || 10) && !alertDismissed" 
        class="mb-6 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg flex items-start justify-between"
        role="alert"
      >
        <div class="flex items-start">
          <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
          <div>
            <strong>Low Stock Alert!</strong> This gadget has {{ totalStock }} units remaining (Alert level: {{ gadget.ReorderPoint || 10 }}).
            <NuxtLink :to="`/purchase-orders/create?gadget=${gadget.GadgetID}`" class="text-yellow-900 underline ml-1">Order now</NuxtLink>
            <span class="text-gray-500 mx-1">or</span>
            <NuxtLink :to="`/stock-adjustments/create?gadget=${gadget.GadgetID}`" class="text-yellow-900 underline">Adjust stock</NuxtLink>
          </div>
        </div>
        <button 
          type="button" 
          class="text-yellow-600 hover:text-yellow-800 ml-4"
          @click="dismissAlert"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Stock Information -->
      <div class="info-card">
        <div class="card-header">
          <h5 class="text-lg font-semibold mb-0">
            <i class="fas fa-warehouse mr-2"></i>Stock Information
          </h5>
        </div>
        <div class="p-6">
          <div v-if="gadget.stocks && gadget.stocks.length > 0">
            <div 
              v-for="stock in gadget.stocks" 
              :key="stock.StockID"
              class="stock-item mb-4 last:mb-0"
            >
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <h6 class="font-semibold mb-1">
                    <i class="fas fa-boxes mr-1 text-indigo-600"></i>
                    Quantity: <span class="text-indigo-600 font-bold">{{ stock.QuantityAdded || 0 }}</span>
                  </h6>
                  <small class="text-gray-500">Stock ID: {{ stock.StockID }}</small>
                </div>
                <div>
                  <h6 class="font-semibold mb-1">
                    <i class="fas fa-tag mr-1 text-green-600"></i>
                    Cost Price: <span class="text-green-600 font-bold">{{ formatPrice(parseFloat(stock.CostPrice) || 0) }}</span>
                  </h6>
                  <small class="text-gray-500">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ stock.PurchaseDate ? formatDate(stock.PurchaseDate) : 'N/A' }}
                  </small>
                </div>
                <div>
                  <h6 class="font-semibold mb-1">
                    <i class="fas fa-calculator mr-1 text-blue-600"></i>
                    Total Value: <span class="text-blue-600 font-bold">{{ formatPrice((stock.QuantityAdded || 0) * (parseFloat(stock.CostPrice) || 0)) }}</span>
                  </h6>
                  <small class="text-gray-500">
                    <i class="fas fa-truck mr-1"></i>
                    {{ stock.supplier?.SupplierName || 'Unknown Supplier' }}
                  </small>
                </div>
                <div class="text-right md:text-left">
                  <span 
                    class="inline-block px-4 py-2 rounded-full text-sm font-semibold"
                    :class="{
                      'bg-green-100 text-green-800': (stock.QuantityAdded || 0) > 10,
                      'bg-yellow-100 text-yellow-800': (stock.QuantityAdded || 0) > 0 && (stock.QuantityAdded || 0) <= 10,
                      'bg-red-100 text-red-800': (stock.QuantityAdded || 0) === 0
                    }"
                  >
                    <i 
                      :class="{
                        'fas fa-check-circle': (stock.QuantityAdded || 0) > 10,
                        'fas fa-exclamation-triangle': (stock.QuantityAdded || 0) > 0 && (stock.QuantityAdded || 0) <= 10,
                        'fas fa-times-circle': (stock.QuantityAdded || 0) === 0
                      }"
                      class="mr-1"
                    ></i>
                    {{ (stock.QuantityAdded || 0) > 10 ? 'In Stock' : ((stock.QuantityAdded || 0) > 0 ? 'Low Stock' : 'Out of Stock') }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12">
            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
            <h5 class="text-xl font-semibold text-gray-600 mb-2">No stock information available</h5>
            <p class="text-gray-500 mb-6">This gadget hasn't been added to stock yet.</p>
            <NuxtLink 
              :to="`/purchase-orders/create?gadget=${gadget.GadgetID}`" 
              class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
            >
              <i class="fas fa-shopping-cart mr-2"></i>Create Purchase Order
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Gadget Modal -->
    <EditGadgetModal
      v-if="gadget"
      :show="showEditModal"
      :gadget-id="gadget.GadgetID"
      @close="closeEditModal"
      @updated="handleGadgetUpdated"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

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
const gadget = ref<any>(null)
const loading = ref(true)
const error = ref('')
const alertDismissed = ref(false)
const showEditModal = ref(false)
const showSuccess = ref(false)

// Computed
const totalStock = computed(() => {
  if (!gadget.value?.stocks) return 0
  return gadget.value.stocks.reduce((sum: number, stock: any) => {
    return sum + (parseInt(stock.QuantityAdded) || 0)
  }, 0)
})

const totalValue = computed(() => {
  if (!gadget.value?.stocks) return 0
  return gadget.value.stocks.reduce((sum: number, stock: any) => {
    return sum + ((parseInt(stock.QuantityAdded) || 0) * (parseFloat(stock.CostPrice) || 0))
  }, 0)
})

const stockEntries = computed(() => {
  return gadget.value?.stocks?.length || 0
})

const uniqueSuppliers = computed(() => {
  if (!gadget.value?.stocks) return 0
  const suppliers = new Set(
    gadget.value.stocks
      .map((s: any) => s.supplier?.SupplierName)
      .filter(Boolean)
  )
  return suppliers.size
})

// Methods
const fetchGadget = async () => {
  const id = route.params.id as string
  
  try {
    loading.value = true
    error.value = ''
    gadget.value = await api(`/gadgets/${id}`)
  } catch (err: any) {
    console.error('Error fetching gadget:', err)
    if (err.status === 404 || err.statusCode === 404) {
      error.value = 'Gadget not found.'
    } else {
      error.value = err.data?.message || err.message || 'Failed to load gadget details.'
    }
  } finally {
    loading.value = false
  }
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP'
  }).format(price)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const dismissAlert = () => {
  alertDismissed.value = true
}

const openEditModal = () => {
  if (gadget.value) {
    showEditModal.value = true
  }
}

const closeEditModal = () => {
  showEditModal.value = false
}

const handleGadgetUpdated = () => {
  showEditModal.value = false
  // Show success notification
  showSuccess.value = true
  // Refresh gadget data after update
  fetchGadget()
}

const goBack = () => {
  router.push('/gadgets')
}

// Lifecycle
onMounted(() => {
  fetchGadget()
})
</script>

<style scoped>
.gadget-header {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  border-radius: 16px;
  color: white;
  padding: 2.5rem 2rem;
  position: relative;
  overflow: hidden;
}

.gadget-header > div {
  position: relative;
  z-index: 2;
}

.gadget-header::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 300px;
  height: 300px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  z-index: 1;
  pointer-events: none;
}

.badge-custom {
  border-radius: 20px;
  padding: 8px 16px;
  font-weight: 500;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  display: inline-block;
}

.info-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
  height: 100%;
}

.info-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-header {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white;
  border: none;
  border-radius: 12px 12px 0 0;
  padding: 1rem 1.5rem;
  font-weight: 600;
}

.stock-item {
  border-left: 4px solid #4f46e5;
  background: #f8fafc;
  border-radius: 10px;
  padding: 1.25rem;
  transition: all 0.3s ease;
}

.stock-item:hover {
  background: #f1f5f9;
  transform: translateX(5px);
}
</style>
