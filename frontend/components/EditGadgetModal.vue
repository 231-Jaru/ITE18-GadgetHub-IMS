<template>
  <Teleport to="body">
    <!-- Delete Success Notification -->
    <DeleteSuccessNotification 
      :show="showDeleteSuccess" 
      message="Gadget deleted successfully!"
    />

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal
      :show="showDeleteModal"
      :deleting="deleting"
      item-type="gadget"
      message="Are you sure you want to delete this gadget?"
      warning="This action cannot be undone. The gadget will be moved to the deleted gadgets page where you can restore it later."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />

    <!-- Backdrop -->
    <Transition name="fade">
      <div 
        v-if="show" 
        class="fixed inset-0 bg-black bg-opacity-50 z-[9998]"
        @click="close"
      ></div>
    </Transition>

    <!-- Modal -->
    <Transition name="modal">
      <div 
        v-if="show"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        @click.self="close"
      >
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
          <div 
            class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="modal-header">
              <div class="flex-1">
                <p class="text-white text-sm opacity-90 mb-2">Update gadget information</p>
                <h5 class="text-2xl font-bold text-white flex items-center">
                  <i class="fas fa-edit mr-2"></i> Edit Gadget
                </h5>
              </div>
              <button 
                type="button" 
                class="text-white hover:text-gray-200 transition-colors flex-shrink-0"
                @click="close"
                aria-label="Close"
              >
                <i class="fas fa-times text-xl"></i>
              </button>
            </div>

            <!-- Body -->
            <div class="p-6">
              <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                <p class="mt-3 text-gray-600">Loading gadget data...</p>
              </div>

              <form v-else @submit.prevent="handleSubmit" class="space-y-6">
                  <div class="flex flex-col">
                    <label for="editGadgetName" class="block text-sm font-semibold text-gray-700 mb-2.5">
                      <i class="fas fa-mobile-alt mr-2 text-indigo-600"></i>Gadget Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                      type="text" 
                      class="form-input w-full"
                      id="editGadgetName"
                      name="editGadgetName"
                      v-model="form.GadgetName"
                      placeholder="Enter gadget name (e.g., iPhone 15 Pro, MacBook Air M2)" 
                      required
                    >
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col">
                      <label for="editCategoryID" class="block text-sm font-semibold text-gray-700 mb-2.5">
                        <i class="fas fa-tags mr-2 text-indigo-600"></i>Category <span class="text-red-500">*</span>
                      </label>
                      <div class="flex items-stretch gap-2">
                        <select 
                          class="form-select flex-1 min-w-0"
                          id="editCategoryID"
                          name="editCategoryID"
                          v-model="form.CategoryID" 
                          required
                        >
                          <option value="">Select Category</option>
                          <option v-for="category in categories" :key="category.CategoryID" :value="Number(category.CategoryID)">
                            {{ category.CategoryName }}
                          </option>
                        </select>
                        <button 
                          type="button" 
                          class="w-12 h-12 flex items-center justify-center bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors relative z-10 flex-shrink-0"
                          :class="{ 'opacity-50 pointer-events-none': showCategoryModal }"
                          @click.stop="showCategoryModal = true"
                        >
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div>

                    <div class="flex flex-col">
                      <label for="editBrandID" class="block text-sm font-semibold text-gray-700 mb-2.5">
                        <i class="fas fa-award mr-2 text-indigo-600"></i>Brand <span class="text-red-500">*</span>
                      </label>
                      <div class="flex items-stretch gap-2">
                        <select 
                          class="form-select flex-1 min-w-0"
                          id="editBrandID"
                          name="editBrandID"
                          v-model="form.BrandID" 
                          required
                        >
                          <option value="">Select Brand</option>
                          <option v-for="brand in brands" :key="brand.BrandID" :value="Number(brand.BrandID)">
                            {{ brand.BrandName }}
                          </option>
                        </select>
                        <button 
                          type="button" 
                          class="w-12 h-12 flex items-center justify-center bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors relative z-10 flex-shrink-0"
                          :class="{ 'opacity-50 pointer-events-none': showBrandModal }"
                          @click.stop="showBrandModal = true"
                        >
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="flex flex-col">
                    <label for="editReorderPoint" class="block text-sm font-semibold text-gray-700 mb-2.5">
                      <i class="fas fa-bell mr-2 text-indigo-600"></i>Low Stock Alert Level
                    </label>
                    <input 
                      type="number" 
                      class="form-input w-full"
                      id="editReorderPoint"
                      name="editReorderPoint"
                      v-model.number="form.ReorderPoint"
                      min="0" 
                      placeholder="10"
                    >
                    <small class="text-gray-500 text-sm mt-2.5 block leading-relaxed">
                      <i class="fas fa-info-circle mr-1.5"></i> You'll get an alert when stock falls below this number (default: 10)
                    </small>
                  </div>

                  <div v-if="error" class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
                  </div>

                  <!-- Footer -->
                  <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
                    <button 
                      type="button" 
                      class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold text-sm flex items-center"
                      @click="handleDelete"
                    >
                      <i class="fas fa-trash mr-1.5"></i> Delete Gadget
                    </button>
                    <div class="flex gap-3 items-center">
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
                        <i v-else class="fas fa-save mr-2"></i> Update Gadget
                      </button>
                    </div>
                  </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Add Category Modal -->
    <AddCategoryModal
      :show="showCategoryModal"
      @close="showCategoryModal = false"
      @created="handleCategoryCreated"
    />

    <!-- Add Brand Modal -->
    <AddBrandModal
      :show="showBrandModal"
      @close="showBrandModal = false"
      @created="handleBrandCreated"
    />
  </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  show: boolean
  gadgetId: number | null
}>()

const emit = defineEmits<{
  close: []
  updated: []
}>()

const { api } = useApi()

// State
const form = ref({
  GadgetName: '',
  CategoryID: '',
  BrandID: '',
  ReorderPoint: 10
})
const categories = ref<any[]>([])
const brands = ref<any[]>([])
const gadgetData = ref<any>(null)
const loading = ref(false)
const submitting = ref(false)
const deleting = ref(false)
const error = ref('')
const showCategoryModal = ref(false)
const showBrandModal = ref(false)
const showDeleteModal = ref(false)
const showDeleteSuccess = ref(false)

// Methods
const fetchGadgetData = async () => {
  if (!props.gadgetId) return
  
  try {
    loading.value = true
    error.value = ''
    
    const [gadgetRes, categoriesRes, brandsRes] = await Promise.all([
      api(`/gadgets/${props.gadgetId}`),
      api('/categories'),
      api('/brands')
    ])
    
    // Store full gadget data for summary card
    gadgetData.value = gadgetRes
    
    // Populate form with gadget data - ensure IDs are numbers
    form.value = {
      GadgetName: gadgetRes.GadgetName || '',
      CategoryID: gadgetRes.CategoryID ? Number(gadgetRes.CategoryID) : '',
      BrandID: gadgetRes.BrandID ? Number(gadgetRes.BrandID) : '',
      ReorderPoint: gadgetRes.ReorderPoint ? Number(gadgetRes.ReorderPoint) : 10
    }
    
    // Handle different response structures
    categories.value = Array.isArray(categoriesRes) 
      ? categoriesRes 
      : (categoriesRes.data || categoriesRes.categories || [])
    brands.value = Array.isArray(brandsRes) 
      ? brandsRes 
      : (brandsRes.data || brandsRes.brands || [])
  } catch (err: any) {
    console.error('Error fetching gadget data:', err)
    error.value = err.data?.message || err.message || 'Failed to load gadget data.'
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  if (!props.gadgetId) return
  
  try {
    submitting.value = true
    error.value = ''

    // Ensure CategoryID and BrandID are numbers
    const submitData = {
      ...form.value,
      CategoryID: form.value.CategoryID ? Number(form.value.CategoryID) : null,
      BrandID: form.value.BrandID ? Number(form.value.BrandID) : null,
      ReorderPoint: form.value.ReorderPoint ? Number(form.value.ReorderPoint) : 10
    }

    await api(`/gadgets/${props.gadgetId}`, {
      method: 'PUT',
      body: submitData
    })

    emit('updated')
    close()
  } catch (err: any) {
    console.error('Error updating gadget:', err)
    if (err.data?.errors) {
      const errors = err.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else {
      error.value = err.data?.message || err.message || 'Failed to update gadget. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

const handleCategoryCreated = (category: any) => {
  // Check if category already exists to avoid duplicates
  const exists = categories.value.find((c: any) => c.CategoryID === category.CategoryID)
  if (!exists) {
    categories.value.push(category)
  }
  form.value.CategoryID = category.CategoryID
  showCategoryModal.value = false
}

const handleBrandCreated = (brand: any) => {
  // Check if brand already exists to avoid duplicates
  const exists = brands.value.find((b: any) => b.BrandID === brand.BrandID)
  if (!exists) {
    brands.value.push(brand)
  }
  form.value.BrandID = brand.BrandID
  showBrandModal.value = false
}

const totalStock = computed(() => {
  if (!gadgetData.value?.stocks) return 0
  return gadgetData.value.stocks.reduce((sum: number, stock: any) => {
    return sum + (parseInt(stock.QuantityAdded) || 0)
  }, 0)
})

const stockEntries = computed(() => {
  return gadgetData.value?.stocks?.length || 0
})

const handleCancel = () => {
  close()
  if (props.gadgetId) {
    // @ts-ignore - Nuxt auto-import
    navigateTo(`/gadgets/${props.gadgetId}`)
  }
}

const handleDelete = () => {
  if (!props.gadgetId) return
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!props.gadgetId) return
  
  try {
    deleting.value = true
    await api(`/gadgets/${props.gadgetId}`, {
      method: 'DELETE'
    })

    showDeleteModal.value = false
    showDeleteSuccess.value = true
    
    // Close modal and emit updated event after showing notification
    setTimeout(() => {
      emit('updated')
      close()
    }, 1500)
  } catch (err: any) {
    console.error('Error deleting gadget:', err)
    error.value = err.data?.message || err.message || 'Failed to delete gadget. Please try again.'
    showDeleteModal.value = false
  } finally {
    deleting.value = false
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
}

const close = () => {
  form.value = {
    GadgetName: '',
    CategoryID: '',
    BrandID: '',
    ReorderPoint: 10
  }
  gadgetData.value = null
  error.value = ''
  emit('close')
}

// Lifecycle
watch(() => props.show, (newVal) => {
  if (newVal && props.gadgetId) {
    fetchGadgetData()
    // Prevent body scroll when modal is open
    if (typeof window !== 'undefined') {
      document.body.style.overflow = 'hidden'
    }
  } else {
    // Restore body scroll when modal is closed
    if (typeof window !== 'undefined') {
      document.body.style.overflow = ''
    }
    // Reset form when modal closes
    if (!newVal) {
      form.value = {
        GadgetName: '',
        CategoryID: '',
        BrandID: '',
        ReorderPoint: 10
      }
      gadgetData.value = null
      error.value = ''
      loading.value = false
    }
  }
})

// Also watch gadgetId in case it changes
watch(() => props.gadgetId, (newId) => {
  if (props.show && newId) {
    fetchGadgetData()
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
.modal-header {
  background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
  color: white;
  padding: 1.5rem;
  border-radius: 1rem 1rem 0 0;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

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
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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

