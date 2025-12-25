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
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-2xl flex justify-between items-center">
              <h5 class="text-lg font-semibold">
                <i class="fas fa-plus-circle mr-2"></i>Add Stock to Gadget
              </h5>
              <button @click="close" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
              </button>
            </div>

        <!-- Body -->
        <form @submit.prevent="handleSubmit" class="p-6">
          <!-- Gadget Name -->
          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-mobile-alt text-indigo-600 mr-2"></i>Gadget
            </label>
            <input
              type="text"
              id="gadgetName"
              name="gadgetName"
              :value="gadgetName"
              readonly
              class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
            />
            <small class="text-gray-500 text-xs mt-1 block">
              <i class="fas fa-info-circle mr-1"></i>Stock will be added to this selected gadget.
            </small>
          </div>

          <!-- Supplier -->
          <div class="mb-4">
            <label for="supplierId" class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-truck text-indigo-600 mr-2"></i>Supplier <span class="text-red-500">*</span>
            </label>
            <select
              id="supplierId"
              v-model="form.SupplierID"
              class="w-full px-4 py-2 border-2 rounded-lg focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
              :class="fieldErrors.SupplierID ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-600'"
              required
            >
              <option value="">Select Supplier</option>
              <option v-for="supplier in suppliers" :key="supplier.SupplierID" :value="supplier.SupplierID">
                {{ supplier.SupplierName }}
              </option>
            </select>
            <p v-if="fieldErrors.SupplierID" class="mt-1 text-sm text-red-600">{{ fieldErrors.SupplierID }}</p>
            <small class="text-gray-500 text-xs mt-1 block">
              <i class="fas fa-info-circle mr-1"></i>This supplier will be linked to this stock entry.
            </small>
          </div>

          <!-- Quantity and Purchase Date -->
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label for="quantityAdded" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-boxes text-indigo-600 mr-2"></i>Quantity <span class="text-red-500">*</span>
              </label>
              <input
                id="quantityAdded"
                name="quantityAdded"
                v-model.number="form.QuantityAdded"
                type="number"
                min="1"
                required
                class="w-full px-4 py-2 border-2 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                :class="fieldErrors.QuantityAdded ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-600'"
              />
              <p v-if="fieldErrors.QuantityAdded" class="mt-1 text-sm text-red-600">{{ fieldErrors.QuantityAdded }}</p>
            </div>
            <div>
              <label for="purchaseDate" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-calendar text-indigo-600 mr-2"></i>Purchase Date
              </label>
              <input
                id="purchaseDate"
                v-model="form.PurchaseDate"
                type="date"
                required
                class="w-full px-4 py-2 border-2 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                :class="fieldErrors.PurchaseDate ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-600'"
              />
              <p v-if="fieldErrors.PurchaseDate" class="mt-1 text-sm text-red-600">{{ fieldErrors.PurchaseDate }}</p>
            </div>
          </div>

          <!-- Cost Price -->
          <div class="mb-4">
            <label for="costPrice" class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-dollar-sign text-indigo-600 mr-2"></i>Cost Price <span class="text-red-500">*</span>
            </label>
            <input
              id="costPrice"
              name="costPrice"
              v-model.number="form.CostPrice"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full px-4 py-2 border-2 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
              :class="fieldErrors.CostPrice ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-600'"
            />
            <p v-if="fieldErrors.CostPrice" class="mt-1 text-sm text-red-600">{{ fieldErrors.CostPrice }}</p>
            <small class="text-gray-500 text-xs mt-1 block">
              <i class="fas fa-info-circle mr-1"></i>Use the unit cost from the supplier invoice (before tax).
            </small>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
          </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 px-6 pb-6">
              <button
                type="button"
                @click="close"
                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
              >
                <i class="fas fa-times mr-1"></i> Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                <i v-else class="fas fa-save mr-2"></i> Add Stock
              </button>
            </div>
          </form>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
const props = defineProps<{
  show: boolean
  gadgetId: number | null
  gadgetName: string
  suppliers: any[]
}>()

const emit = defineEmits<{
  close: []
  created: []
}>()

const { api } = useApi()

// State
const form = ref({
  SupplierID: '',
  QuantityAdded: 1,
  PurchaseDate: new Date().toISOString().split('T')[0],
  CostPrice: 0
})
const submitting = ref(false)
const error = ref('')
const fieldErrors = ref<{
  SupplierID?: string
  QuantityAdded?: string
  PurchaseDate?: string
  CostPrice?: string
}>({})

const validate = (): boolean => {
  const errs: typeof fieldErrors.value = {}

  if (!form.value.SupplierID) {
    errs.SupplierID = 'Please select a supplier.'
  }

  if (!form.value.QuantityAdded || form.value.QuantityAdded < 1) {
    errs.QuantityAdded = 'Quantity must be at least 1.'
  }

  if (!form.value.PurchaseDate) {
    errs.PurchaseDate = 'Please choose a purchase date.'
  }

  if (form.value.CostPrice === null || form.value.CostPrice === undefined || form.value.CostPrice < 0) {
    errs.CostPrice = 'Cost price cannot be negative.'
  }

  fieldErrors.value = errs
  return Object.keys(errs).length === 0
}

// Methods
const handleSubmit = async () => {
  if (!props.gadgetId) return
  
  // Frontend validation
  fieldErrors.value = {}
  error.value = ''
  if (!validate()) {
    return
  }

  try {
    submitting.value = true

    const submitData = {
      SupplierID: form.value.SupplierID ? Number(form.value.SupplierID) : null,
      QuantityAdded: Number(form.value.QuantityAdded),
      CostPrice: Number(form.value.CostPrice),
      PurchaseDate: form.value.PurchaseDate
    }

    await api(`/stocks/add-to-gadget/${props.gadgetId}`, {
      method: 'POST',
      body: submitData
    })

    emit('created')
    close()
  } catch (err: any) {
    console.error('Error adding stock:', err)
    if (err.data?.errors) {
      const errors = err.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else {
      error.value = err.data?.message || err.message || 'Failed to add stock. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

const close = () => {
  form.value = {
    SupplierID: '',
    QuantityAdded: 1,
    PurchaseDate: new Date().toISOString().split('T')[0],
    CostPrice: 0
  }
  error.value = ''
  fieldErrors.value = {}
  emit('close')
}

// Lifecycle
watch(() => props.show, (newVal) => {
  if (newVal) {
    // Prevent body scroll when modal is open
    if (typeof window !== 'undefined') {
      document.body.style.overflow = 'hidden'
    }
  } else {
    // Restore body scroll when modal is closed
    if (typeof window !== 'undefined') {
      document.body.style.overflow = ''
    }
  }
})

// Cleanup on unmount
onUnmounted(() => {
  if (typeof window !== 'undefined') {
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
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

