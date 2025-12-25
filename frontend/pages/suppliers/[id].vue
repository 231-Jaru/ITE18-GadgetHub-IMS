<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Success Notification -->
    <SuccessNotification 
      :show="showSuccess" 
      message="Supplier updated successfully!"
    />
    
    <!-- Delete Success Notification -->
    <DeleteSuccessNotification 
      :show="showDeleteSuccess" 
      message="Supplier deleted successfully!"
    />

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal
      :show="showDeleteModal"
      :deleting="deleting"
      item-type="supplier"
      :item-name="supplier?.SupplierName || 'this supplier'"
      message="Are you sure you want to delete this supplier?"
      warning="This action cannot be undone. All associated data will be permanently removed."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
    
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading supplier...</p>
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
        to="/suppliers" 
        class="ml-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
      >
        <i class="fas fa-arrow-left mr-1"></i>Back to Suppliers
      </NuxtLink>
    </div>

    <!-- Supplier Details -->
    <div v-else-if="supplier">
      <!-- Header Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="mb-4 md:mb-0">
            <h1 class="text-4xl font-bold mb-2">
              <i class="fas fa-truck mr-2"></i>{{ supplier.SupplierName }}
            </h1>
            <p class="text-lg opacity-90">Supplier Details & Information</p>
          </div>
          <div class="flex flex-col sm:flex-row gap-3">
            <button 
              v-if="supplier && supplier.SupplierID && !isEditing"
              @click="startEdit"
              type="button"
              class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
            >
              <i class="fas fa-edit mr-2"></i> Edit Supplier
            </button>
            <NuxtLink 
              to="/suppliers" 
              class="inline-flex items-center justify-center px-6 py-3 bg-indigo-400 text-white rounded-lg hover:bg-indigo-500 transition-colors font-semibold shadow-md"
            >
              <i class="fas fa-arrow-left mr-2"></i> Back to Suppliers
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stats-card primary">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-boxes"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              <AnimatedNumber :value="totalStock || 0" />
            </h4>
            <p class="text-gray-600 font-medium">Total Stock</p>
          </div>
        </div>
        <div class="stats-card success">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-list"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              <AnimatedNumber :value="stockEntries || 0" />
            </h4>
            <p class="text-gray-600 font-medium">Stock Entries</p>
          </div>
        </div>
        <div class="stats-card warning">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              ₱<AnimatedNumber :value="totalValue || 0" :decimals="2" />
            </h4>
            <p class="text-gray-600 font-medium">Total Value</p>
          </div>
        </div>
        <div class="stats-card info">
          <div class="text-center p-6">
            <div class="icon-wrapper">
              <i class="fas fa-mobile-alt"></i>
            </div>
            <h4 class="text-3xl font-bold mb-2">
              <AnimatedNumber :value="uniqueGadgets || 0" />
            </h4>
            <p class="text-gray-600 font-medium">Unique Gadgets</p>
          </div>
        </div>
      </div>

      <!-- Supplier Information and Stock Statistics -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Supplier Information Card -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-xl font-bold mb-4">
            <i class="fas fa-info-circle mr-2 text-indigo-600"></i> Supplier Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <p class="text-sm text-gray-600 mb-1">Supplier ID</p>
              <p class="text-lg font-semibold">S{{ supplier.SupplierID }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 mb-1">Supplier Name</p>
              <p class="text-lg font-semibold">{{ supplier.SupplierName || 'N/A' }}</p>
            </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Contact Person</p>
            <p class="text-lg font-semibold">{{ supplier.ContactPerson || 'N/A' }}</p>
          </div>
          <div>
              <p class="text-sm text-gray-600 mb-1">Phone Number</p>
              <p class="text-lg font-semibold">{{ supplier.Phone || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 mb-1">Email Address</p>
            <p class="text-lg font-semibold">{{ supplier.Email || 'N/A' }}</p>
          </div>
          <div>
              <p class="text-sm text-gray-600 mb-1">Date Created</p>
              <p class="text-lg font-semibold">{{ formatDate(supplier.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Stock Statistics Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-xl font-bold mb-4">
            <i class="fas fa-chart-bar mr-2 text-indigo-600"></i> Stock Statistics
          </h3>
          <div class="space-y-4">
            <div class="bg-indigo-50 rounded-lg p-4">
              <p class="text-sm text-gray-600 mb-1">Stock Records</p>
              <p class="text-3xl font-bold text-indigo-600">
                <AnimatedNumber :value="stockEntries || 0" />
              </p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
              <p class="text-sm text-gray-600 mb-1">Total Quantity</p>
              <p class="text-3xl font-bold text-green-600">
                <AnimatedNumber :value="totalStock || 0" />
              </p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
              <p class="text-sm text-gray-600 mb-1">Average Quantity</p>
              <p class="text-3xl font-bold text-blue-600">
                <AnimatedNumber :value="stockEntries > 0 ? totalStock / stockEntries : 0" :decimals="1" />
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Form Container (shown when editing) -->
      <div v-if="isEditing" class="max-w-5xl mx-auto mb-6">
      <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">
              <i class="fas fa-edit mr-2 text-indigo-600"></i> Edit Supplier Information
        </h3>
            <p class="text-gray-600">Update the supplier details below</p>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Please fix the following errors:</strong>
            <p class="mt-2">{{ error }}</p>
          </div>

          <form @submit.prevent="handleSubmit">
            <!-- Basic Information Section -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
              <h5 class="text-lg font-semibold text-gray-800 mb-5">
                <i class="fas fa-info-circle mr-2 text-indigo-600"></i>Basic Information
              </h5>
              <div class="mb-4">
                <label for="SupplierName" class="block font-semibold text-gray-700 mb-2">
                  <i class="fas fa-truck mr-2"></i> Supplier Name <span class="text-red-500">*</span>
                </label>
                <input 
                  type="text" 
                  id="SupplierName"
                  name="SupplierName"
                  v-model="form.SupplierName"
                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                  placeholder="Enter supplier name"
                  required
          >
              </div>
            </div>

            <!-- Contact Information Section -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
              <h5 class="text-lg font-semibold text-gray-800 mb-5">
                <i class="fas fa-address-book mr-2 text-indigo-600"></i>Contact Information
              </h5>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div>
                  <label for="ContactPerson" class="block font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user mr-2"></i> Contact Person
                  </label>
                  <input 
                    type="text" 
                    id="ContactPerson"
                    name="ContactPerson"
                    v-model="form.ContactPerson"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                    placeholder="Contact person name"
                  >
                  </div>
                  <div>
                  <label for="Phone" class="block font-semibold text-gray-700 mb-2">
                    <i class="fas fa-phone mr-2"></i> Phone Number
                  </label>
                  <input 
                    type="tel" 
                    id="Phone"
                    name="Phone"
                    v-model="form.Phone"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                    placeholder="+1234567890"
                  >
                </div>
                  </div>
                  <div>
                <label for="Email" class="block font-semibold text-gray-700 mb-2">
                  <i class="fas fa-envelope mr-2"></i> Email Address
                </label>
                <input 
                  type="email" 
                  id="Email"
                  name="Email"
                  v-model="form.Email"
                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                  placeholder="supplier@example.com"
                >
                <small class="text-gray-500 mt-2 block">Optional - must be unique if provided</small>
              </div>
                  </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-200">
              <button 
                type="button"
                @click="handleDelete"
                class="px-6 py-3 border-2 border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-all font-semibold"
              >
                <i class="fas fa-trash mr-2"></i> Delete Supplier
              </button>
              <div class="flex flex-col sm:flex-row gap-3">
                <button 
                  type="button"
                  @click="cancelEdit"
                  class="text-center px-6 py-3 border-2 border-gray-500 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold"
                >
                  <i class="fas fa-times mr-2"></i> Cancel
                </button>
                <button 
                  type="submit" 
                  :disabled="submitting"
                  class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg hover:-translate-y-0.5 transition-all font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                  <i v-else class="fas fa-save mr-2"></i>
                  <span>{{ submitting ? 'Updating...' : 'Update Supplier' }}</span>
                </button>
                  </div>
            </div>
          </form>
                </div>
              </div>

      <!-- Recent Stock Records Section -->
      <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
        <h3 class="text-xl font-bold mb-4">
          <i class="fas fa-boxes mr-2 text-indigo-600"></i> Recent Stock Records
        </h3>
        <div v-if="supplier.stocks && supplier.stocks.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Gadget</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cost Price</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Purchase Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="stock in supplier.stocks" 
                :key="stock.StockID"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-3">
                  <p class="font-semibold">{{ stock.gadget?.GadgetName || 'Unknown Gadget' }}</p>
                </td>
                <td class="px-4 py-3">
                  <p class="font-semibold">{{ stock.QuantityAdded }}</p>
                </td>
                <td class="px-4 py-3">
                  <p class="font-semibold text-green-600">₱{{ formatPrice(stock.CostPrice) }}</p>
                </td>
                <td class="px-4 py-3">
                  <p class="font-semibold">{{ formatDate(stock.PurchaseDate) }}</p>
                </td>
                <td class="px-4 py-3">
              <NuxtLink 
                :to="`/stocks/${stock.StockID}/edit`"
                    class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-semibold"
                @click.stop
              >
                <i class="fas fa-edit mr-1"></i> Edit
              </NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-12">
          <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
          <h5 class="text-xl font-semibold text-gray-600 mb-2">No stock items</h5>
          <p class="text-gray-500">This supplier hasn't provided any stock yet.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'

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
const supplier = ref<any>(null)
const loading = ref(true)
const error = ref('')
const showSuccess = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const showDeleteModal = ref(false)
const showDeleteSuccess = ref(false)
const deleting = ref(false)
const form = ref({
  SupplierName: '',
  ContactPerson: '',
  Email: '',
  Phone: ''
})

// Computed
const totalStock = computed(() => {
  if (!supplier.value?.stocks) return 0
  return supplier.value.stocks.reduce((sum: number, stock: any) => sum + (parseInt(stock.QuantityAdded) || 0), 0)
})

const stockEntries = computed(() => {
  return supplier.value?.stocks?.length || 0
})

const totalValue = computed(() => {
  if (!supplier.value?.stocks) return 0
  return supplier.value.stocks.reduce((sum: number, stock: any) => {
    return sum + ((parseInt(stock.QuantityAdded) || 0) * (parseFloat(stock.CostPrice) || 0))
  }, 0)
})

const uniqueGadgets = computed(() => {
  if (!supplier.value?.stocks || !Array.isArray(supplier.value.stocks)) return 0
  const gadgetIds = supplier.value.stocks
    .map((stock: any) => stock?.GadgetID)
    .filter((id: any) => id !== null && id !== undefined && id !== '')
  return new Set(gadgetIds).size
})

// Get supplier ID from route
const getSupplierId = (): string => {
  const id = route.params.id
  if (Array.isArray(id)) {
    return id[0] as string
  }
  return String(id || '')
}

// Methods
const fetchSupplier = async () => {
  try {
    loading.value = true
    error.value = ''
    const supplierId = getSupplierId()
    if (!supplierId) {
      error.value = 'Invalid supplier ID'
      loading.value = false
      return
    }
    const response = await api(`/suppliers/${supplierId}`)
    const supplierData = response.data || response
    supplier.value = supplierData
    
    // Populate form with current supplier data
    if (supplierData) {
      form.value = {
        SupplierName: supplierData.SupplierName || '',
        ContactPerson: supplierData.ContactPerson || '',
        Email: supplierData.Email || '',
        Phone: supplierData.Phone || ''
      }
    }
  } catch (err: any) {
    console.error('Error fetching supplier:', err)
    if (err.status === 404 || err.statusCode === 404) {
      error.value = 'Supplier not found'
    } else {
    error.value = err.data?.message || err.message || 'Failed to load supplier.'
    }
  } finally {
    loading.value = false
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

const formatNumber = (num: number) => {
  return new Intl.NumberFormat('en-US').format(num || 0)
}

const formatDecimal = (num: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 1,
    maximumFractionDigits: 1
  }).format(num || 0)
}

const startEdit = () => {
  isEditing.value = true
  // Scroll to edit form
  setTimeout(() => {
    const editForm = document.querySelector('.max-w-5xl')
    if (editForm) {
      editForm.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }, 100)
}

const cancelEdit = () => {
  isEditing.value = false
  error.value = ''
  // Reset form to current supplier data
  if (supplier.value) {
    form.value = {
      SupplierName: supplier.value.SupplierName || '',
      ContactPerson: supplier.value.ContactPerson || '',
      Email: supplier.value.Email || '',
      Phone: supplier.value.Phone || ''
    }
  }
}

const handleSubmit = async () => {
  // Prevent double submission
  if (submitting.value) {
    return
  }

  try {
    submitting.value = true
    error.value = ''

    const supplierId = getSupplierId()
    if (!supplierId) {
      error.value = 'Invalid supplier ID'
      submitting.value = false
      return
    }

    // Basic validation
    if (!form.value.SupplierName || form.value.SupplierName.trim() === '') {
      error.value = 'Supplier name is required'
      submitting.value = false
      return
    }

    await api(`/suppliers/${supplierId}`, {
      method: 'PUT',
      body: form.value
    })

    showSuccess.value = true
    isEditing.value = false
    
    // Refresh supplier data to update stats and stock items
    await fetchSupplier()
    
    setTimeout(() => {
      showSuccess.value = false
    }, 2000)
  } catch (err: any) {
    console.error('Error updating supplier:', err)
    if (err.data?.errors) {
      const errors = err.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else {
      error.value = err.data?.message || err.message || 'Failed to update supplier. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

const handleDelete = () => {
  if (deleting.value) {
    return
  }
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  // Prevent double deletion
  if (deleting.value) {
    return
  }

  try {
    deleting.value = true
    const supplierId = getSupplierId()
    
    if (!supplierId) {
      error.value = 'Invalid supplier ID'
      deleting.value = false
      showDeleteModal.value = false
      return
    }
    
    await api(`/suppliers/${supplierId}`, { method: 'DELETE' })
    
    showDeleteModal.value = false
    showDeleteSuccess.value = true
    
    setTimeout(() => {
      router.push('/suppliers')
    }, 1500)
  } catch (err: any) {
    console.error('Error deleting supplier:', err)
    error.value = err.data?.message || err.message || 'Failed to delete supplier.'
    showDeleteModal.value = false
  } finally {
    deleting.value = false
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
}


// Lifecycle
onMounted(() => {
  fetchSupplier()
  
  // Check for success query parameter
  if (route.query.updated === 'true') {
    showSuccess.value = true
    // Refresh supplier data to show updates
    fetchSupplier()
    // Remove query parameter from URL
    router.replace({ query: {} })
  }
})

// Watch for route changes (when navigating from edit or direct navigation)
watch(() => route.params.id, (newId) => {
  if (newId) {
    const currentId = supplier.value?.SupplierID?.toString()
    const newIdStr = Array.isArray(newId) ? newId[0] : newId.toString()
    if (currentId !== newIdStr) {
      fetchSupplier()
    }
  }
})

// Watch for route query changes (when navigating from edit)
watch(() => route.query.updated, (newVal) => {
  if (newVal === 'true') {
    showSuccess.value = true
    fetchSupplier()
    router.replace({ query: {} })
  }
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

