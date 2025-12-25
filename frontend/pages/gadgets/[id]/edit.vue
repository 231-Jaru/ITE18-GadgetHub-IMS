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
      <p class="mt-4 text-gray-600">Loading gadget...</p>
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

    <!-- Edit Form -->
    <div v-else-if="gadget" class="flex justify-center">
      <div class="w-full max-w-3xl">
        <div class="form-container">
          <div class="text-center mb-6">
            <h1 class="text-3xl font-bold mb-2">
              <i class="fas fa-edit mr-2"></i> Edit Gadget
            </h1>
            <p class="opacity-90">Update gadget information</p>
          </div>

          <!-- Current Gadget Info -->
          <div class="gadget-info mb-6">
            <h5 class="text-xl font-semibold mb-4">
              <i class="fas fa-mobile-alt mr-2"></i> {{ gadget.GadgetName }}
            </h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="mb-2"><strong>Category:</strong> {{ gadget.category?.CategoryName || 'No Category' }}</p>
                <p class="mb-0"><strong>Brand:</strong> {{ gadget.brand?.BrandName || 'No Brand' }}</p>
              </div>
              <div>
                <p class="mb-2"><strong>Total Stock:</strong> {{ totalStock }}</p>
                <p class="mb-0"><strong>Stock Entries:</strong> {{ stockEntries }}</p>
              </div>
            </div>
          </div>

          <form @submit.prevent="handleSubmit">
            <div class="mb-4">
              <label for="GadgetName" class="block text-sm font-semibold text-white mb-2">
                <i class="fas fa-mobile-alt mr-2"></i>Gadget Name
              </label>
              <input 
                type="text" 
                class="form-input w-full"
                :class="{ 'border-red-500': errors.GadgetName }"
                id="GadgetName"
                v-model="form.GadgetName"
                placeholder="Enter gadget name" 
                required
              >
              <div v-if="errors.GadgetName" class="text-red-200 text-sm mt-1">
                {{ errors.GadgetName[0] }}
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label for="CategoryID" class="block text-sm font-semibold text-white mb-2">
                  <i class="fas fa-tags mr-2"></i>Category
                </label>
                <select 
                  class="form-select w-full"
                  :class="{ 'border-red-500': errors.CategoryID }"
                  id="CategoryID"
                  v-model="form.CategoryID" 
                  required
                >
                  <option value="">Select Category</option>
                  <option v-for="category in categories" :key="category.CategoryID" :value="category.CategoryID">
                    {{ category.CategoryName }}
                  </option>
                </select>
                <div v-if="errors.CategoryID" class="text-red-200 text-sm mt-1">
                  {{ errors.CategoryID[0] }}
                </div>
              </div>

              <div>
                <label for="BrandID" class="block text-sm font-semibold text-white mb-2">
                  <i class="fas fa-star mr-2"></i>Brand
                </label>
                <select 
                  class="form-select w-full"
                  :class="{ 'border-red-500': errors.BrandID }"
                  id="BrandID"
                  v-model="form.BrandID" 
                  required
                >
                  <option value="">Select Brand</option>
                  <option v-for="brand in brands" :key="brand.BrandID" :value="brand.BrandID">
                    {{ brand.BrandName }}
                  </option>
                </select>
                <div v-if="errors.BrandID" class="text-red-200 text-sm mt-1">
                  {{ errors.BrandID[0] }}
                </div>
              </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="mb-4">
              <label for="ReorderPoint" class="block text-sm font-semibold text-white mb-2">
                <i class="fas fa-bell mr-2"></i>Low Stock Alert Level
              </label>
              <input 
                type="number" 
                class="form-input w-full"
                :class="{ 'border-red-500': errors.ReorderPoint }"
                id="ReorderPoint"
                v-model.number="form.ReorderPoint"
                min="0" 
                placeholder="Alert me when stock goes below this number"
              >
              <small class="text-white text-opacity-75 text-sm mt-1 block">
                <i class="fas fa-info-circle mr-1"></i> You'll get an alert when stock falls below this number (default: 10)
              </small>
              <div v-if="errors.ReorderPoint" class="text-red-200 text-sm mt-1">
                {{ errors.ReorderPoint[0] }}
              </div>
            </div>

            <div v-if="error" class="mb-4 p-4 bg-red-500 bg-opacity-20 border border-red-300 text-white rounded-lg">
              <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
            </div>

            <div class="flex flex-wrap gap-3 justify-end pt-4 border-t border-white border-opacity-25">
              <NuxtLink 
                :to="`/gadgets/${gadget.GadgetID}`" 
                class="inline-flex items-center px-6 py-2 bg-gradient-to-b from-indigo-500 to-indigo-700 text-white rounded-full hover:from-indigo-600 hover:to-indigo-800 transition-colors font-semibold shadow-md"
              >
                <i class="fas fa-eye mr-2"></i>View
              </NuxtLink>
              <NuxtLink 
                to="/gadgets" 
                class="inline-flex items-center px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors font-semibold"
              >
                <i class="fas fa-arrow-left mr-2"></i>Cancel
              </NuxtLink>
              <button 
                type="submit" 
                class="inline-flex items-center px-6 py-2 bg-white border border-indigo-400 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed disabled:border-gray-300 disabled:text-gray-400"
                :disabled="submitting"
              >
                <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600 mr-2"></span>
                <i v-else class="fas fa-save mr-2"></i>Update Gadget
              </button>
            </div>
          </form>

          <!-- Delete Form -->
          <div class="mt-6 pt-6 border-t border-white border-opacity-25">
            <button 
              type="button" 
              class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
              @click="handleDelete"
              :disabled="deleting"
            >
              <span v-if="deleting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
              <i v-else class="fas fa-trash mr-1"></i> Delete Gadget
            </button>
          </div>
        </div>
      </div>
    </div>
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
const categories = ref<any[]>([])
const brands = ref<any[]>([])
const loading = ref(true)
const submitting = ref(false)
const deleting = ref(false)
const error = ref('')
const errors = ref<any>({})
const showSuccess = ref(false)
const showDeleteModal = ref(false)
const showDeleteSuccess = ref(false)

const form = ref({
  GadgetName: '',
  CategoryID: '',
  BrandID: '',
  ReorderPoint: 10
})

// Computed
const totalStock = computed(() => {
  if (!gadget.value?.stocks) return 0
  return gadget.value.stocks.reduce((sum: number, stock: any) => {
    return sum + (parseInt(stock.QuantityAdded) || 0)
  }, 0)
})

const stockEntries = computed(() => {
  return gadget.value?.stocks?.length || 0
})

// Methods
const fetchData = async () => {
  const id = route.params.id as string
  
  try {
    loading.value = true
    error.value = ''
    
    const [gadgetRes, categoriesRes, brandsRes] = await Promise.all([
      api(`/gadgets/${id}`),
      api('/categories'),
      api('/brands')
    ])
    
    gadget.value = gadgetRes
    categories.value = categoriesRes.data || categoriesRes || []
    brands.value = brandsRes.data || brandsRes || []
    
    // Populate form
    form.value = {
      GadgetName: gadget.value.GadgetName || '',
      CategoryID: gadget.value.CategoryID || '',
      BrandID: gadget.value.BrandID || '',
      ReorderPoint: gadget.value.ReorderPoint || 10
    }
  } catch (err: any) {
    console.error('Error fetching data:', err)
    if (err.status === 404 || err.statusCode === 404) {
      error.value = 'Gadget not found.'
    } else {
      error.value = err.data?.message || err.message || 'Failed to load gadget.'
    }
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  const id = route.params.id as string
  
  try {
    submitting.value = true
    error.value = ''
    errors.value = {}

    await api(`/gadgets/${id}`, {
      method: 'PUT',
      body: form.value
    })

    // Show success notification
    showSuccess.value = true
    
    // Navigate after a short delay to show the notification
    setTimeout(() => {
      router.push(`/gadgets/${id}`)
    }, 1500)
  } catch (err: any) {
    console.error('Error updating gadget:', err)
    if (err.data?.errors) {
      errors.value = err.data.errors
    } else {
      error.value = err.data?.message || err.message || 'Failed to update gadget. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

const handleDelete = () => {
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  const id = route.params.id as string
  
  try {
    deleting.value = true
    error.value = ''

    await api(`/gadgets/${id}`, {
      method: 'DELETE'
    })

    showDeleteModal.value = false
    showDeleteSuccess.value = true
    
    // Navigate after showing notification
    setTimeout(() => {
      router.push('/gadgets')
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

// Lifecycle
onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.form-container {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 20px;
  padding: 2rem;
  color: white;
}

.form-input,
.form-select {
  border-radius: 10px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  padding: 12px 15px;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.95);
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  border-color: white;
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
  outline: none;
  background: white;
}

.form-select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
  appearance: none;
}

.gadget-info {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 15px;
  padding: 1.5rem;
}
</style>
