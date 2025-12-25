<template>
  <div class="w-full max-w-5xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
          <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-shopping-cart mr-2"></i> Create Purchase Order
          </h1>
          <p class="text-lg opacity-90">Add items to order from supplier</p>
        </div>
        <NuxtLink 
          to="/purchase-orders" 
          class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
        >
          <i class="fas fa-arrow-left mr-2"></i> Back
        </NuxtLink>
      </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <form @submit.prevent="handleSubmit" novalidate>
        <!-- Supplier and Order Date -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="SupplierID" class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-truck mr-2 text-indigo-600"></i>Supplier <span class="text-red-500">*</span>
            </label>
            <select 
              id="SupplierID"
              v-model="form.SupplierID"
              class="w-full px-4 py-3 border-2 rounded-lg focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
              :class="fieldErrors.SupplierID ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500'"
              required
            >
              <option value="">Select Supplier</option>
              <option v-for="supplier in suppliers" :key="supplier.SupplierID" :value="supplier.SupplierID">
                {{ supplier.SupplierName }}
              </option>
            </select>
            <p v-if="fieldErrors.SupplierID" class="mt-1 text-sm text-red-600">{{ fieldErrors.SupplierID }}</p>
            <small class="text-gray-500 text-xs mt-1 block">
              <i class="fas fa-info-circle mr-1"></i>This supplier will be linked to all items in this purchase order.
            </small>
          </div>
          <div>
            <label for="OrderDate" class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-calendar mr-2 text-indigo-600"></i>Order Date
            </label>
            <input 
              type="date" 
              id="OrderDate"
              v-model="form.OrderDate"
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            >
          </div>
        </div>

        <!-- Expected Delivery Date and Notes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="ExpectedDeliveryDate" class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-truck-fast mr-2 text-indigo-600"></i>Expected Delivery Date
            </label>
            <input 
              type="date" 
              id="ExpectedDeliveryDate"
              v-model="form.ExpectedDeliveryDate"
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            >
          </div>
          <div>
            <label for="Notes" class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-sticky-note mr-2 text-indigo-600"></i>Notes (Optional)
            </label>
            <textarea 
              id="Notes"
              v-model="form.Notes"
              rows="2"
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
            ></textarea>
          </div>
        </div>

        <hr class="my-6">

        <!-- Items Section -->
        <div class="mb-6">
          <div class="flex justify-between items-center mb-2">
            <h3 class="text-xl font-bold">
              <i class="fas fa-list mr-2 text-indigo-600"></i>Items
            </h3>
            <button 
              type="button" 
              @click="addItem"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
            >
              <i class="fas fa-plus mr-2"></i> Add Item
            </button>
          </div>
          <small class="text-gray-500 text-xs mb-3 block">
            <i class="fas fa-info-circle mr-1"></i>Add one or more gadgets with quantity and unit cost. These will be converted to stock when the order is received.
          </small>

          <p v-if="fieldErrors.items" class="mb-3 text-sm text-red-600">
            <i class="fas fa-exclamation-circle mr-1"></i>{{ fieldErrors.items }}
          </p>

          <div v-if="form.items.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
            <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No items added yet. Click "Add Item" to get started.</p>
          </div>

          <div v-else class="space-y-4">
            <div 
              v-for="(item, index) in form.items" 
              :key="index"
              class="border-l-4 border-indigo-500 bg-gray-50 rounded-lg p-4"
            >
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label :for="`gadget-${index}`" class="block text-sm font-semibold text-gray-700 mb-2">
                    Gadget <span class="text-red-500">*</span>
                  </label>
                  <select 
                    :id="`gadget-${index}`"
                    v-model="item.GadgetID"
                    @change="updateItemTotal(index)"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                    required
                  >
                    <option value="">Select Gadget</option>
                    <option v-for="gadget in gadgets" :key="gadget.GadgetID" :value="gadget.GadgetID">
                      {{ gadget.GadgetName }} ({{ gadget.category?.CategoryName || 'N/A' }})
                    </option>
                  </select>
                </div>
                <div>
                  <label :for="`quantity-${index}`" class="block text-sm font-semibold text-gray-700 mb-2">
                    Quantity <span class="text-red-500">*</span>
                  </label>
                  <input 
                    type="number" 
                    :id="`quantity-${index}`"
                    v-model.number="item.Quantity"
                    @input="updateItemTotal(index)"
                    min="1"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                    required
                  >
                </div>
                <div>
                  <label :for="`unitCost-${index}`" class="block text-sm font-semibold text-gray-700 mb-2">
                    Unit Cost <span class="text-red-500">*</span>
                  </label>
                  <input 
                    type="number" 
                    :id="`unitCost-${index}`"
                    v-model.number="item.UnitCost"
                    @input="updateItemTotal(index)"
                    step="0.01"
                    min="0"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                    required
                  >
                </div>
                <div class="flex items-end">
                  <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Cost</label>
                    <p class="px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-semibold">
                      ₱{{ formatPrice(item.TotalCost) }}
                    </p>
                  </div>
                  <button 
                    type="button" 
                    @click="removeItem(index)"
                    class="ml-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Amount -->
        <div v-if="form.items.length > 0" class="mb-6 p-4 bg-indigo-50 rounded-lg">
          <div class="flex justify-between items-center">
            <span class="text-xl font-bold">Total Amount:</span>
            <span class="text-2xl font-bold text-indigo-600">₱{{ formatPrice(totalAmount) }}</span>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
          <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
          <NuxtLink 
            to="/purchase-orders" 
            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
          >
            <i class="fas fa-times mr-2"></i> Cancel
          </NuxtLink>
          <button 
            type="submit" 
            :disabled="submitting || form.items.length === 0"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            <i v-else class="fas fa-save mr-2"></i> Create Purchase Order
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick, watch } from 'vue'

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
const suppliers = ref<any[]>([])
const gadgets = ref<any[]>([])
const loading = ref(true)
const submitting = ref(false)
const error = ref('')
const form = ref({
  SupplierID: '',
  OrderDate: new Date().toISOString().split('T')[0],
  ExpectedDeliveryDate: '',
  Notes: '',
  items: [] as any[]
})
const fieldErrors = ref<{
  SupplierID?: string
  items?: string
}>({})

// Computed
const totalAmount = computed(() => {
  return form.value.items.reduce((sum, item) => sum + (item.TotalCost || 0), 0)
})

// Methods
const fetchData = async () => {
  try {
    loading.value = true
    const [suppliersRes, gadgetsRes] = await Promise.all([
      api('/suppliers'),
      api('/gadgets?all=1') // Request all gadgets for dropdown
    ])
    
    suppliers.value = Array.isArray(suppliersRes) ? suppliersRes : (suppliersRes.data || [])
    gadgets.value = Array.isArray(gadgetsRes) ? gadgetsRes : (gadgetsRes.data || [])
    
    // Check if gadget is pre-selected via query parameter
    const gadgetId = route.query.gadget ? Number(route.query.gadget) : null
    const supplierId = route.query.supplier ? Number(route.query.supplier) : null
    
    if (supplierId) {
      form.value.SupplierID = supplierId.toString()
    }
    
    if (gadgetId) {
      // Pre-add the gadget as an item after data is loaded
      await nextTick()
      addItem()
      await nextTick()
      // Set the gadget ID after select is rendered
      if (form.value.items.length > 0) {
        form.value.items[0].GadgetID = gadgetId.toString()
      }
    }
  } catch (err: any) {
    console.error('Error fetching data:', err)
    error.value = 'Failed to load suppliers and gadgets.'
  } finally {
    loading.value = false
  }
}

const addItem = () => {
  form.value.items.push({
    GadgetID: '',
    Quantity: 1,
    UnitCost: 0,
    TotalCost: 0
  })
}

const removeItem = (index: number) => {
  form.value.items.splice(index, 1)
}

const updateItemTotal = (index: number) => {
  const item = form.value.items[index]
  item.TotalCost = (item.Quantity || 0) * (item.UnitCost || 0)
}

const handleSubmit = async () => {
  fieldErrors.value = {}
  error.value = ''

  // Frontend validation
  const errs: typeof fieldErrors.value = {}

  if (!form.value.SupplierID) {
    errs.SupplierID = 'Please select a supplier for this purchase order.'
  }

  if (form.value.items.length === 0) {
    errs.items = 'Please add at least one item to the purchase order.'
  } else {
    const invalidItem = form.value.items.find(
      (item) =>
        !item.GadgetID ||
        !item.Quantity ||
        item.Quantity < 1 ||
        item.UnitCost === null ||
        item.UnitCost === undefined ||
        item.UnitCost < 0
    )
    if (invalidItem) {
      errs.items = 'Each item must have a gadget, quantity (≥ 1), and a non-negative unit cost.'
    }
  }

  fieldErrors.value = errs

  if (Object.keys(errs).length > 0) {
    return
  }

  try {
    submitting.value = true

    const submitData = {
      SupplierID: Number(form.value.SupplierID),
      OrderDate: form.value.OrderDate || null,
      ExpectedDeliveryDate: form.value.ExpectedDeliveryDate || null,
      Notes: form.value.Notes || null,
      items: form.value.items.map(item => ({
        GadgetID: Number(item.GadgetID),
        Quantity: Number(item.Quantity),
        UnitCost: Number(item.UnitCost)
      }))
    }

    const response = await api('/purchase-orders', {
      method: 'POST',
      body: submitData
    })

    router.push(`/purchase-orders/${response.purchase_order?.PurchaseOrderID || response.PurchaseOrderID}`)
  } catch (err: any) {
    console.error('Error creating purchase order:', err)
    if (err.data?.errors) {
      const errors = err.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else {
      error.value = err.data?.message || err.message || 'Failed to create purchase order. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price || 0)
}

// Lifecycle
onMounted(() => {
  fetchData()
})
</script>

