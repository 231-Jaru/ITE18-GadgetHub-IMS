<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <Transition name="fade">
      <div 
        v-if="show" 
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        @click="close"
      ></div>
    </Transition>

    <!-- Modal -->
    <Transition name="modal">
      <div 
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="close"
      >
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
          <div 
            class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl transform transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="modal-header">
              <h5 class="text-xl font-bold text-white">
                <i class="fas fa-info-circle mr-2"></i> {{ gadget?.GadgetName || 'Product Details' }}
              </h5>
              <button 
                type="button" 
                class="text-white hover:text-gray-200 transition-colors"
                @click="close"
                aria-label="Close"
              >
                <i class="fas fa-times text-xl"></i>
              </button>
            </div>

            <!-- Body -->
            <div class="p-6">
              <div v-if="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                <p class="mt-4 text-gray-600">Loading product details...</p>
              </div>

              <div v-else-if="error" class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
              </div>

              <div v-else-if="gadget">
                <div class="mb-6">
                  <div class="flex flex-wrap gap-3 mb-3">
                    <span class="px-4 py-2 rounded-full text-white font-semibold" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                      <i class="fas fa-tag mr-1"></i> {{ gadget.category?.CategoryName || 'No Category' }}
                    </span>
                    <span class="px-4 py-2 rounded-full text-white font-semibold" style="background: linear-gradient(45deg, #f093fb, #f5576c);">
                      <i class="fas fa-star mr-1"></i> {{ gadget.brand?.BrandName || 'No Brand' }}
                    </span>
                  </div>
                  <p class="text-gray-600"><strong>Gadget ID:</strong> {{ gadget.GadgetID }}</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                  <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
                    <i class="fas fa-boxes text-3xl text-indigo-600 mb-2"></i>
                    <h4 class="text-2xl font-bold mb-1">{{ totalStock }}</h4>
                    <p class="text-gray-600 text-sm">Total Stock</p>
                  </div>
                  <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
                    <i class="fas fa-dollar-sign text-3xl text-green-600 mb-2"></i>
                    <h4 class="text-2xl font-bold mb-1">{{ formatPrice(totalValue) }}</h4>
                    <p class="text-gray-600 text-sm">Total Value</p>
                  </div>
                  <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
                    <i class="fas fa-warehouse text-3xl text-blue-600 mb-2"></i>
                    <h4 class="text-2xl font-bold mb-1">{{ stockEntries }}</h4>
                    <p class="text-gray-600 text-sm">Stock Entries</p>
                  </div>
                  <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
                    <i class="fas fa-truck text-3xl text-yellow-600 mb-2"></i>
                    <h4 class="text-2xl font-bold mb-1">{{ uniqueSuppliers }}</h4>
                    <p class="text-gray-600 text-sm">Suppliers</p>
                  </div>
                </div>

                <!-- Stock Information -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                  <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4">
                    <h5 class="text-lg font-semibold mb-0">
                      <i class="fas fa-warehouse mr-2"></i> Stock Information
                    </h5>
                  </div>
                  <div class="p-6">
                    <div v-if="gadget.stocks && gadget.stocks.length > 0">
                      <div 
                        v-for="stock in gadget.stocks" 
                        :key="stock.StockID"
                        class="mb-4 p-4 border-l-4 border-indigo-600 bg-gray-50 rounded-lg last:mb-0"
                      >
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                          <div>
                            <h6 class="font-semibold mb-1">Quantity: <span class="text-indigo-600">{{ stock.QuantityAdded || 0 }}</span></h6>
                            <small class="text-gray-500">Stock ID: {{ stock.StockID }}</small>
                          </div>
                          <div>
                            <h6 class="font-semibold mb-1">Cost Price: <span class="text-green-600">{{ formatPrice(parseFloat(stock.CostPrice) || 0) }}</span></h6>
                          </div>
                          <div>
                            <h6 class="font-semibold mb-1">Total Value: <span class="text-blue-600">{{ formatPrice((parseInt(stock.QuantityAdded) || 0) * (parseFloat(stock.CostPrice) || 0)) }}</span></h6>
                            <small class="text-gray-500">Supplier: {{ stock.supplier?.SupplierName || 'Unknown' }}</small>
                          </div>
                          <div>
                            <span 
                              class="inline-block px-4 py-2 rounded-full text-sm font-semibold"
                              :class="{
                                'bg-green-100 text-green-800': (parseInt(stock.QuantityAdded) || 0) > 10,
                                'bg-yellow-100 text-yellow-800': (parseInt(stock.QuantityAdded) || 0) > 0 && (parseInt(stock.QuantityAdded) || 0) <= 10,
                                'bg-red-100 text-red-800': (parseInt(stock.QuantityAdded) || 0) === 0
                              }"
                            >
                              {{ (parseInt(stock.QuantityAdded) || 0) > 10 ? 'In Stock' : ((parseInt(stock.QuantityAdded) || 0) > 0 ? 'Low Stock' : 'Out of Stock') }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-else class="text-center py-8">
                      <i class="fas fa-box-open text-5xl text-gray-300 mb-3"></i>
                      <h5 class="text-lg font-semibold text-gray-600 mb-2">No stock information available</h5>
                      <p class="text-gray-500">This gadget hasn't been added to stock yet.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">
              <NuxtLink 
                v-if="gadget"
                :to="`/gadgets/${gadget.GadgetID}`"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
              >
                <i class="fas fa-external-link-alt mr-1"></i> View Full Page
              </NuxtLink>
              <button 
                type="button" 
                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
                @click="close"
              >
                <i class="fas fa-times mr-1"></i> Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch, onUnmounted } from 'vue'
// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

const props = defineProps<{
  gadgetId: number | null
}>()

const emit = defineEmits<{
  close: []
}>()

// State
const gadget = ref<any>(null)
const loading = ref(true)
const error = ref('')

// Computed
const show = computed(() => !!props.gadgetId)

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
  if (!props.gadgetId) return
  
  try {
    loading.value = true
    error.value = ''
    gadget.value = await api(`/gadgets/${props.gadgetId}`)
  } catch (err: any) {
    console.error('Error fetching gadget:', err)
    error.value = err.data?.message || err.message || 'Failed to load gadget details.'
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

const close = () => {
  emit('close')
}

// Lifecycle
watch(() => props.gadgetId, (newId) => {
  if (newId) {
    fetchGadget()
  } else {
    // Reset when closed
    gadget.value = null
    loading.value = true
    error.value = ''
  }
}, { immediate: true })

// Prevent body scroll when modal is open
watch(show, (newVal) => {
  if (typeof window !== 'undefined') {
    if (newVal) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
.modal-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 1.5rem;
  border-radius: 1rem 1rem 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Modal transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>
