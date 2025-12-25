<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Success Notification -->
    <SuccessNotification 
      :show="showStatusSuccess" 
      message="Purchase order status updated successfully!"
      @close="showStatusSuccess = false"
    />
    
    <!-- Delete Success Notification -->
    <DeleteSuccessNotification
      :show="showDeleteSuccess"
      message="Purchase order deleted successfully!"
      @close="showDeleteSuccess = false"
    />
    
    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal
      :show="showDeleteModal"
      :deleting="deleting"
      item-type="purchase order"
      :item-name="purchaseOrder?.PONumber"
      message="Are you sure you want to delete this purchase order?"
      warning="Only draft purchase orders can be deleted. Once ordered or received, use 'Cancel' status instead. This action cannot be undone."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
    
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Loading purchase order...</p>
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
        to="/purchase-orders" 
        class="ml-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
      >
        <i class="fas fa-arrow-left mr-1"></i>Back to List
      </NuxtLink>
    </div>

    <!-- Purchase Order Details -->
    <div v-else-if="purchaseOrder">
      <!-- Header Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="mb-4 md:mb-0">
            <h1 class="text-4xl font-bold mb-2">
              <i class="fas fa-shopping-cart mr-2"></i>Purchase Order: {{ purchaseOrder.PONumber }}
            </h1>
            <div class="flex gap-3 items-center mt-3">
              <span :class="`px-4 py-2 rounded-full text-sm font-semibold ${
                purchaseOrder.Status === 'DRAFT' ? 'bg-gray-600' :
                purchaseOrder.Status === 'PENDING' || purchaseOrder.Status === 'ORDERED' ? 'bg-blue-600' :
                purchaseOrder.Status === 'RECEIVED' ? 'bg-green-600' :
                'bg-red-600'
              }`">
                {{ purchaseOrder.Status }}
              </span>
              <span class="opacity-90">Total: <strong>₱{{ formatPrice(purchaseOrder.TotalAmount) }}</strong></span>
            </div>
          </div>
          <NuxtLink 
            to="/purchase-orders" 
            class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md"
          >
            <i class="fas fa-arrow-left mr-2"></i>Back to List
          </NuxtLink>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="lg:col-span-1 space-y-6">
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold mb-4">
              <i class="fas fa-info-circle mr-2 text-indigo-600"></i>Order Information
            </h3>
            <div class="space-y-4">
              <div>
                <p class="text-sm text-gray-600 mb-1">Supplier</p>
                <p class="font-semibold">{{ purchaseOrder.supplier?.SupplierName || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Order Date</p>
                <p class="font-semibold">{{ formatDate(purchaseOrder.OrderDate) }}</p>
              </div>
              <div v-if="purchaseOrder.ExpectedDeliveryDate">
                <p class="text-sm text-gray-600 mb-1">Expected Delivery</p>
                <p class="font-semibold">{{ formatDate(purchaseOrder.ExpectedDeliveryDate) }}</p>
              </div>
              <div v-if="purchaseOrder.ReceivedDate">
                <p class="text-sm text-gray-600 mb-1">Received Date</p>
                <p class="font-semibold">{{ formatDate(purchaseOrder.ReceivedDate) }}</p>
              </div>
              <div v-if="purchaseOrder.Notes">
                <p class="text-sm text-gray-600 mb-1">Notes</p>
                <p class="font-semibold">{{ purchaseOrder.Notes }}</p>
              </div>
            </div>
          </div>

          <!-- Status Control -->
          <div v-if="purchaseOrder.Status !== 'RECEIVED' && purchaseOrder.Status !== 'CANCELLED'" class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold mb-4">
              <i class="fas fa-cog mr-2 text-indigo-600"></i>Update Status
            </h3>
            <form @submit.prevent="updateStatus" class="space-y-4">
              <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                  Status <span class="text-red-500">*</span>
                </label>
                <select 
                  id="status"
                  v-model="newStatus"
                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                  required
                >
                  <option value="DRAFT" :selected="purchaseOrder.Status === 'DRAFT'">Draft</option>
                  <option value="ORDERED" :selected="purchaseOrder.Status === 'ORDERED'">Ordered</option>
                  <option value="RECEIVED" :selected="purchaseOrder.Status === 'RECEIVED'">Received</option>
                  <option value="CANCELLED" :selected="purchaseOrder.Status === 'CANCELLED'">Cancelled</option>
                </select>
              </div>
              <div v-if="newStatus === 'RECEIVED' && purchaseOrder.Status !== 'RECEIVED'" class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                  <i class="fas fa-info-circle mr-2"></i>
                  Marking as "Received" will automatically add stock to inventory.
                </p>
              </div>
              <div v-if="statusError" class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ statusError }}
              </div>
              <button 
                type="submit" 
                :disabled="updatingStatus || newStatus === purchaseOrder.Status"
                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
              >
                <span v-if="updatingStatus" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                <i v-else class="fas fa-save mr-2"></i>
                {{ updatingStatus ? 'Updating...' : 'Update Status' }}
              </button>
            </form>
          </div>

          <!-- Delete Purchase Order (Only for DRAFT) -->
          <div v-if="purchaseOrder.Status === 'DRAFT'" class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold mb-4">
              <i class="fas fa-trash mr-2 text-red-600"></i>Delete Purchase Order
            </h3>
            <p class="text-sm text-gray-600 mb-4">
              Only draft purchase orders can be deleted. Once ordered or received, use "Cancel" status instead.
            </p>
            <button 
              type="button"
              @click="openDeleteModal"
              class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold flex items-center justify-center"
            >
              <i class="fas fa-trash mr-2"></i>Delete Purchase Order
            </button>
          </div>
        </div>

        <!-- Order Items -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold mb-4">
              <i class="fas fa-boxes mr-2 text-indigo-600"></i>Order Items
            </h3>
            <div v-if="purchaseOrder.items && purchaseOrder.items.length > 0">
              <div 
                v-for="item in purchaseOrder.items" 
                :key="item.POItemID"
                class="border-l-4 border-indigo-500 bg-gray-50 rounded-lg p-4 mb-4"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <h4 class="text-lg font-semibold mb-2">
                      {{ item.gadget?.GadgetName || 'Unknown Gadget' }}
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                      <div>
                        <p class="text-gray-600">Quantity</p>
                        <p class="font-semibold">{{ item.Quantity }}</p>
                      </div>
                      <div>
                        <p class="text-gray-600">Unit Cost</p>
                        <p class="font-semibold">₱{{ formatPrice(item.UnitCost) }}</p>
                      </div>
                      <div>
                        <p class="text-gray-600">Total Cost</p>
                        <p class="font-semibold">₱{{ formatPrice(item.TotalCost) }}</p>
                      </div>
                      <div v-if="item.QuantityReceived !== undefined">
                        <p class="text-gray-600">Received</p>
                        <p class="font-semibold">{{ item.QuantityReceived }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                  <span class="text-xl font-bold">Total Amount:</span>
                  <span class="text-2xl font-bold text-indigo-600">₱{{ formatPrice(purchaseOrder.TotalAmount) }}</span>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
              <h5 class="text-xl font-semibold text-gray-600 mb-2">No items in this order</h5>
            </div>
          </div>
        </div>
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
const { api } = useApi()

// State
const purchaseOrder = ref<any>(null)
const loading = ref(true)
const error = ref('')
const newStatus = ref('')
const updatingStatus = ref(false)
const statusError = ref('')
const showStatusSuccess = ref(false)
const showDeleteModal = ref(false)
const showDeleteSuccess = ref(false)
const deleting = ref(false)

// Methods
const fetchPurchaseOrder = async () => {
  try {
    loading.value = true
    error.value = ''
    const response = await api(`/purchase-orders/${route.params.id}`)
    purchaseOrder.value = response
    newStatus.value = response.Status || ''
  } catch (err: any) {
    console.error('Error fetching purchase order:', err)
    error.value = err.data?.message || err.message || 'Failed to load purchase order.'
  } finally {
    loading.value = false
  }
}

const updateStatus = async () => {
  if (!purchaseOrder.value || newStatus.value === purchaseOrder.value.Status) {
    return
  }

  try {
    updatingStatus.value = true
    statusError.value = ''

    await api(`/purchase-orders/${route.params.id}/status`, {
      method: 'PUT',
      body: {
        Status: newStatus.value
      }
    })

    showStatusSuccess.value = true
    
    // Refresh purchase order data
    await fetchPurchaseOrder()
    
    // Hide success message after delay
    setTimeout(() => {
      showStatusSuccess.value = false
    }, 3000)
  } catch (err: any) {
    console.error('Error updating status:', err)
    statusError.value = err.data?.message || err.message || 'Failed to update status. Please try again.'
  } finally {
    updatingStatus.value = false
  }
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price || 0)
}

const formatDate = (date: string | Date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const openDeleteModal = () => {
  if (purchaseOrder.value?.Status === 'DRAFT') {
    showDeleteModal.value = true
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
}

const confirmDelete = async () => {
  if (!purchaseOrder.value || purchaseOrder.value.Status !== 'DRAFT') {
    showDeleteModal.value = false
    return
  }

  try {
    deleting.value = true
    
    await api(`/purchase-orders/${route.params.id}`, {
      method: 'DELETE'
    })

    showDeleteModal.value = false
    showDeleteSuccess.value = true
    
    // Redirect to purchase orders list after a short delay
    setTimeout(() => {
      // @ts-ignore - Nuxt auto-imports
      navigateTo('/purchase-orders')
    }, 1500)
  } catch (err: any) {
    console.error('Error deleting purchase order:', err)
    error.value = err.data?.message || err.message || 'Failed to delete purchase order. Only draft purchase orders can be deleted.'
    showDeleteModal.value = false
  } finally {
    deleting.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchPurchaseOrder()
})
</script>

