<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Success Notification -->
    <SuccessNotification 
      :show="showSuccess" 
      message="Stock updated successfully!"
    />
    
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading stock data...</p>
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
        to="/stocks" 
        class="ml-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
      >
        <i class="fas fa-arrow-left mr-1"></i>Back to Stocks
      </NuxtLink>
    </div>

    <!-- Edit Form -->
    <div v-else-if="stock">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
        <h1 class="text-4xl font-bold mb-2">
          <i class="fas fa-edit mr-2"></i> Edit Stock
        </h1>
        <p class="text-lg opacity-90">Update stock information</p>
      </div>

      <!-- Form -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col">
              <label for="GadgetID" class="block text-sm font-semibold text-gray-700 mb-2.5">
                <i class="fas fa-mobile-alt mr-2 text-indigo-600"></i>Gadget <span class="text-red-500">*</span>
              </label>
              <select 
                class="form-select w-full"
                id="GadgetID"
                v-model="form.GadgetID" 
                required
              >
                <option value="">Select Gadget</option>
                <option v-for="gadget in gadgets" :key="gadget.GadgetID" :value="gadget.GadgetID">
                  {{ gadget.GadgetName }} ({{ gadget.category?.CategoryName || 'N/A' }} - {{ gadget.brand?.BrandName || 'N/A' }})
                </option>
              </select>
            </div>

            <div class="flex flex-col">
              <label for="SupplierID" class="block text-sm font-semibold text-gray-700 mb-2.5">
                <i class="fas fa-truck mr-2 text-indigo-600"></i>Supplier <span class="text-red-500">*</span>
              </label>
              <select 
                class="form-select w-full"
                id="SupplierID"
                v-model="form.SupplierID" 
                required
              >
                <option value="">Select Supplier</option>
                <option v-for="supplier in suppliers" :key="supplier.SupplierID" :value="supplier.SupplierID">
                  {{ supplier.SupplierName }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col">
              <label for="CostPrice" class="block text-sm font-semibold text-gray-700 mb-2.5">
                <i class="fas fa-peso-sign mr-2 text-indigo-600"></i>Cost Price <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                <input 
                  type="number" 
                  class="form-input w-full pl-8"
                  id="CostPrice"
                  v-model.number="form.CostPrice"
                  step="0.01"
                  min="0" 
                  placeholder="0.00"
                  required
                >
              </div>
            </div>

            <div class="flex flex-col">
              <label for="PurchaseDate" class="block text-sm font-semibold text-gray-700 mb-2.5">
                <i class="fas fa-calendar mr-2 text-indigo-600"></i>Purchase Date <span class="text-red-500">*</span>
              </label>
              <input 
                type="date" 
                class="form-input w-full"
                id="PurchaseDate"
                v-model="form.PurchaseDate"
                required
              >
            </div>
          </div>

          <div class="flex flex-col">
            <label for="QuantityAdded" class="block text-sm font-semibold text-gray-700 mb-2.5">
              <i class="fas fa-boxes mr-2 text-indigo-600"></i>Current Quantity
            </label>
            <input 
              type="number" 
              class="form-input w-full bg-gray-100"
              id="QuantityAdded"
              :value="stock.QuantityAdded"
              readonly
              disabled
            >
            <small class="text-gray-500 text-sm mt-2.5 block leading-relaxed">
              <i class="fas fa-info-circle mr-1.5"></i> To change quantity, use <NuxtLink to="/stock-adjustments/create" class="text-indigo-600 hover:underline">Stock Adjustments</NuxtLink> for proper audit trail.
            </small>
          </div>

          <div v-if="submitError" class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ submitError }}
          </div>

          <!-- Footer -->
          <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
            <button 
              type="button" 
              class="px-5 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-semibold text-sm flex items-center"
              @click="handleCancel"
            >
              <i class="fas fa-arrow-left mr-1.5"></i> Cancel
            </button>
            <button 
              type="submit" 
              class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
              :disabled="submitting"
            >
              <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
              <i v-else class="fas fa-save mr-2"></i> Update Stock
            </button>
          </div>
        </form>
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
const route = useRoute()
// @ts-ignore - Nuxt auto-imports
const router = useRouter()
// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

// State
const stock = ref<any>(null)
const gadgets = ref<any[]>([])
const suppliers = ref<any[]>([])
const loading = ref(true)
const error = ref('')
const submitting = ref(false)
const submitError = ref('')
const showSuccess = ref(false)

const form = ref({
  GadgetID: '',
  SupplierID: '',
  CostPrice: 0,
  PurchaseDate: ''
})

// Methods
const fetchStockData = async () => {
  const id = route.params.id as string
  
  try {
    loading.value = true
    error.value = ''
    
    const response = await api(`/stocks/${id}/edit`) as any
    
    if (response.stock) {
      stock.value = response.stock
      form.value = {
        GadgetID: response.stock.GadgetID ? Number(response.stock.GadgetID) : '',
        SupplierID: response.stock.SupplierID ? Number(response.stock.SupplierID) : '',
        CostPrice: parseFloat(response.stock.CostPrice) || 0,
        PurchaseDate: response.stock.PurchaseDate ? new Date(response.stock.PurchaseDate).toISOString().split('T')[0] : ''
      }
      
      gadgets.value = response.gadgets || []
      suppliers.value = response.suppliers || []
    } else {
      error.value = 'Stock not found.'
    }
  } catch (err: any) {
    console.error('Error fetching stock data:', err)
    if (err.status === 404 || err.statusCode === 404) {
      error.value = 'Stock not found.'
    } else {
      error.value = err.data?.message || err.message || 'Failed to load stock data.'
    }
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  const id = route.params.id as string
  
  try {
    submitting.value = true
    submitError.value = ''

    const submitData = {
      GadgetID: form.value.GadgetID ? Number(form.value.GadgetID) : null,
      SupplierID: form.value.SupplierID ? Number(form.value.SupplierID) : null,
      CostPrice: form.value.CostPrice ? parseFloat(form.value.CostPrice.toString()) : null,
      PurchaseDate: form.value.PurchaseDate || null
    }

    await api(`/stocks/${id}`, {
      method: 'PUT',
      body: submitData
    })

    // Show success notification
    showSuccess.value = true
    
    // Navigate after a short delay to show the notification
    setTimeout(() => {
      // @ts-ignore - Nuxt auto-import
      navigateTo('/stocks')
    }, 1500)
  } catch (err: any) {
    console.error('Error updating stock:', err)
    if (err.data?.errors) {
      const errors = err.data.errors
      submitError.value = Object.values(errors).flat().join(', ')
    } else {
      submitError.value = err.data?.message || err.message || 'Failed to update stock. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

const handleCancel = () => {
  // @ts-ignore - Nuxt auto-import
  navigateTo('/stocks')
}

// Lifecycle
onMounted(() => {
  fetchStockData()
})
</script>

<style scoped>
.form-input,
.form-select {
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
  transition: all 0.3s;
  font-size: 1rem;
}

.form-input:focus,
.form-select:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
  outline: none;
}

.form-select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
  appearance: none;
}
</style>

