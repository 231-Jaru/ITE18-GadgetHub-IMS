<template>
  <div class="w-full max-w-4xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
          <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-sliders mr-2"></i> Create Stock Adjustment
          </h1>
          <p class="text-lg opacity-90">Add, remove, or set stock quantity</p>
        </div>
        <div class="flex gap-3">
          <NuxtLink 
            to="/stock-adjustments" 
            class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
          >
            <i class="fas fa-history mr-2"></i> View Adjustments
          </NuxtLink>
          <NuxtLink 
            to="/stocks" 
            class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
          >
            <i class="fas fa-arrow-left mr-2"></i> Back to Stocks
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6">
      <h6 class="font-semibold mb-2">
        <i class="fas fa-info-circle mr-2 text-blue-600"></i>What is a Stock Adjustment?
      </h6>
      <p class="text-sm text-gray-700">
        <strong>Increase:</strong> Add stock (found items, returns)<br>
        <strong>Decrease:</strong> Remove stock (damage, theft, write-offs)<br>
        <strong>Set:</strong> Set to exact number (corrections after physical count)
      </p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <form @submit.prevent="handleSubmit">
        <!-- Gadget Selection -->
        <div class="mb-6">
          <label for="GadgetID" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-mobile-alt mr-2 text-indigo-600"></i>Gadget <span class="text-red-500">*</span>
          </label>
          <select 
            id="GadgetID"
            v-model="form.GadgetID"
            @change="onGadgetChange"
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            required
          >
            <option value="">Select a gadget</option>
            <option v-for="gadget in gadgets" :key="gadget.GadgetID" :value="gadget.GadgetID">
              {{ gadget.GadgetName }}
              <span v-if="gadget.category"> - {{ gadget.category.CategoryName }}</span>
              (Current Stock: {{ getCurrentStock(gadget) }})
            </option>
          </select>
        </div>

        <!-- Adjustment Type -->
        <div class="mb-6">
          <label for="AdjustmentType" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-exchange-alt mr-2 text-indigo-600"></i>Adjustment Type <span class="text-red-500">*</span>
          </label>
          <select 
            id="AdjustmentType"
            v-model="form.AdjustmentType"
            @change="onTypeChange"
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            required
          >
            <option value="">Select type</option>
            <option value="INCREASE">Increase (Add Stock)</option>
            <option value="DECREASE">Decrease (Remove Stock)</option>
            <option value="SET">Set (Set to Exact Number)</option>
          </select>
        </div>

        <!-- Quantity -->
        <div class="mb-6">
          <label for="QuantityChanged" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-hashtag mr-2 text-indigo-600"></i>
            <span v-if="form.AdjustmentType === 'SET'">New Quantity</span>
            <span v-else>Quantity</span>
            <span class="text-red-500">*</span>
          </label>
          <input 
            type="number" 
            id="QuantityChanged"
            v-model.number="form.QuantityChanged"
            min="0"
            step="1"
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            :placeholder="form.AdjustmentType === 'SET' ? 'Enter new total quantity' : 'Enter quantity to adjust'"
            required
          >
          <p v-if="selectedGadget && form.AdjustmentType !== 'SET'" class="mt-2 text-sm text-gray-600">
            Current stock: <strong>{{ getCurrentStock(selectedGadget) }}</strong>
            <span v-if="form.AdjustmentType === 'INCREASE'">
              → After: <strong>{{ getCurrentStock(selectedGadget) + (form.QuantityChanged || 0) }}</strong>
            </span>
            <span v-else-if="form.AdjustmentType === 'DECREASE'">
              → After: <strong>{{ Math.max(0, getCurrentStock(selectedGadget) - (form.QuantityChanged || 0)) }}</strong>
            </span>
          </p>
        </div>

        <!-- Reason -->
        <div class="mb-6">
          <label for="Reason" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-comment-alt mr-2 text-indigo-600"></i>Reason <span class="text-red-500">*</span>
          </label>
          <input 
            type="text" 
            id="Reason"
            v-model="form.Reason"
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            placeholder="e.g., Physical count correction, Found inventory, Damaged items"
            required
          >
        </div>

        <!-- Notes -->
        <div class="mb-6">
          <label for="Notes" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-sticky-note mr-2 text-indigo-600"></i>Notes (Optional)
          </label>
          <textarea 
            id="Notes"
            v-model="form.Notes"
            rows="3"
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            placeholder="Additional details about this adjustment..."
          ></textarea>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
          <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
          <NuxtLink 
            to="/stock-adjustments" 
            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
          >
            <i class="fas fa-times mr-2"></i> Cancel
          </NuxtLink>
          <button 
            type="submit" 
            :disabled="submitting"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            <i v-else class="fas fa-save mr-2"></i> Create Adjustment
          </button>
        </div>
      </form>
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
const gadgets = ref<any[]>([])
const loading = ref(true)
const submitting = ref(false)
const error = ref('')
const form = ref({
  GadgetID: '',
  StockID: '',
  AdjustmentType: '',
  QuantityChanged: 0,
  Reason: '',
  Notes: '',
  AdjustmentDate: new Date().toISOString().split('T')[0]
})
const selectedGadget = ref<any>(null)

// Methods
const fetchData = async () => {
  try {
    loading.value = true
    const response = await api('/gadgets?all=1') // Request all gadgets for dropdown
    
    if (response.data) {
      gadgets.value = response.data
    } else if (Array.isArray(response)) {
      gadgets.value = response
    } else {
      gadgets.value = []
    }
    
    // Check for selected gadget from query
    if (route.query.gadget) {
      const gadgetId = String(route.query.gadget)
      form.value.GadgetID = gadgetId
      selectedGadget.value = gadgets.value.find((g: any) => String(g.GadgetID) === gadgetId)
    }
  } catch (err: any) {
    console.error('Error fetching gadgets:', err)
    error.value = 'Failed to load gadgets.'
  } finally {
    loading.value = false
  }
}

const onGadgetChange = () => {
  const gadget = gadgets.value.find((g: any) => g.GadgetID === Number(form.value.GadgetID))
  selectedGadget.value = gadget || null
  
  // Get the first stock entry for this gadget
  if (gadget && gadget.stocks && gadget.stocks.length > 0) {
    form.value.StockID = gadget.stocks[0].StockID
  }
}

const onTypeChange = () => {
  // Reset quantity when type changes
  form.value.QuantityChanged = 0
}

const getCurrentStock = (gadget: any) => {
  if (!gadget || !gadget.stocks) return 0
  return gadget.stocks.reduce((sum: number, stock: any) => sum + (parseInt(stock.QuantityAdded) || 0), 0)
}

const handleSubmit = async () => {
  try {
    submitting.value = true
    error.value = ''

    const submitData = {
      GadgetID: Number(form.value.GadgetID),
      StockID: form.value.StockID ? Number(form.value.StockID) : null,
      AdjustmentType: form.value.AdjustmentType,
      QuantityChanged: Number(form.value.QuantityChanged),
      Reason: form.value.Reason,
      Notes: form.value.Notes || null,
      AdjustmentDate: form.value.AdjustmentDate
    }

    const response = await api('/stock-adjustments', {
      method: 'POST',
      body: submitData
    })

    router.push(`/stock-adjustments/${response.adjustment?.AdjustmentID || response.AdjustmentID}`)
  } catch (err: any) {
    console.error('Error creating stock adjustment:', err)
    console.error('Error details:', {
      status: err.status || err.statusCode,
      data: err.data,
      message: err.message,
      response: err.response
    })
    
    if (err.data?.errors) {
      const errors = err.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else if (err.data?.message) {
      error.value = err.data.message
    } else if (err.message) {
      error.value = err.message
    } else {
      error.value = 'Failed to create stock adjustment. Please check the console for details.'
    }
  } finally {
    submitting.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchData()
})
</script>

