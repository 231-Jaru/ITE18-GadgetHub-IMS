<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="gadgets-hero mb-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="lg:w-2/3 mb-4 lg:mb-0">
          <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-plus-circle mr-3"></i>
            Add New Gadget
          </h1>
          <p class="text-lg opacity-90">Create a new gadget and connect it to categories, brands, and inventory</p>
        </div>
        <div class="lg:w-1/3 lg:text-right">
          <NuxtLink to="/gadgets" class="inline-block px-6 py-3 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-50 transition-colors shadow-md">
            <i class="fas fa-arrow-left mr-2"></i> Back to Gadgets
          </NuxtLink>
        </div>
      </div>
    </div>

    <div class="flex justify-center">
      <div class="w-full max-w-4xl">
        <div class="form-container">
          <form @submit.prevent="handleSubmit" novalidate>
            <!-- Basic Information Section -->
            <div class="form-section">
              <h5 class="text-xl font-semibold mb-4"><i class="fas fa-info-circle mr-2"></i>Basic Information</h5>
              <div class="mb-4">
                <label for="GadgetName" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-mobile-alt mr-2 text-indigo-600"></i>Gadget Name
                  <span class="text-red-500">*</span>
                </label>
                <input 
                  type="text" 
                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                  :class="{ 'border-red-500': errors.GadgetName }"
                  id="GadgetName"
                  v-model="form.GadgetName"
                  placeholder="Enter gadget name (e.g., iPhone 15 Pro, MacBook Air M2)" 
                  required
                >
                <div v-if="errors.GadgetName" class="mt-1 text-sm text-red-600">
                  {{ errors.GadgetName[0] }}
                </div>
                <small class="text-gray-500 text-xs mt-1 block">
                  <i class="fas fa-info-circle mr-1"></i>Use a clear name so it’s easy to search and recognize in reports.
                </small>
              </div>
            </div>

            <!-- Category and Brand Section -->
            <div class="form-section">
              <h5 class="text-xl font-semibold mb-4"><i class="fas fa-layer-group mr-2"></i>Category & Brand</h5>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="CategoryID" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tags mr-2 text-indigo-600"></i>Category
                    <span class="text-red-500">*</span>
                  </label>
                  <div class="flex items-stretch gap-2">
                    <select 
                      class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
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
                    <button 
                      type="button" 
                      class="w-12 h-12 flex items-center justify-center bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex-shrink-0"
                      :class="{ 'opacity-50 pointer-events-none': showCategoryModal }"
                      @click="showCategoryModal = true"
                    >
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                  <div v-if="errors.CategoryID" class="mt-1 text-sm text-red-600">
                    {{ errors.CategoryID[0] }}
                  </div>
                  <small class="text-gray-500 text-xs mt-1 block">
                    <i class="fas fa-info-circle mr-1"></i>Categories group similar gadgets (for example Laptops, Phones, Accessories).
                  </small>
                </div>

                <div>
                  <label for="BrandID" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-award mr-2 text-indigo-600"></i>Brand
                    <span class="text-red-500">*</span>
                  </label>
                  <div class="flex items-stretch gap-2">
                    <select 
                      class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
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
                    <button 
                      type="button" 
                      class="w-12 h-12 flex items-center justify-center bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex-shrink-0"
                      :class="{ 'opacity-50 pointer-events-none': showBrandModal }"
                      @click="showBrandModal = true"
                    >
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                  <div v-if="errors.BrandID" class="mt-1 text-sm text-red-600">
                    {{ errors.BrandID[0] }}
                  </div>
                  <small class="text-gray-500 text-xs mt-1 block">
                    <i class="fas fa-info-circle mr-1"></i>Choose the manufacturer or brand for this gadget (for example Apple, Samsung).
                  </small>
                </div>
              </div>
            </div>

            <!-- Low Stock Alert Section -->
            <div class="form-section">
              <h5 class="text-xl font-semibold mb-4"><i class="fas fa-bell mr-2"></i>Low Stock Alert</h5>
              <div>
                <label for="ReorderPoint" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-bell mr-2 text-indigo-600"></i>Alert Me When Stock Goes Below
                </label>
                <input 
                  type="number" 
                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                  :class="{ 'border-red-500': errors.ReorderPoint }"
                  id="ReorderPoint"
                  v-model.number="form.ReorderPoint"
                  min="0" 
                  placeholder="Enter number (e.g., 10)"
                >
                <p class="mt-2 text-sm text-gray-600">
                  <i class="fas fa-info-circle mr-1"></i> You'll get an alert when stock falls below this number (default: 10)
                </p>
                <div v-if="errors.ReorderPoint" class="mt-1 text-sm text-red-600">
                  {{ errors.ReorderPoint[0] }}
                </div>
              </div>
            </div>

            <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
              <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end mt-6">
              <NuxtLink to="/gadgets" class="btn-cancel">
                <i class="fas fa-times mr-2"></i> Cancel
              </NuxtLink>
              <button type="submit" class="btn-submit" :disabled="submitting">
                <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                <i v-else class="fas fa-save mr-2"></i> Create Gadget
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

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
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})

// @ts-ignore - Nuxt auto-imports
const { api } = useApi()
// @ts-ignore - Nuxt auto-imports
const router = useRouter()

// State
const form = ref({
  GadgetName: '',
  CategoryID: '',
  BrandID: '',
  ReorderPoint: 10
})
const categories = ref<any[]>([])
const brands = ref<any[]>([])
const submitting = ref(false)
const error = ref('')
const errors = ref<any>({})
const showCategoryModal = ref(false)
const showBrandModal = ref(false)

// Methods
const fetchCategoriesAndBrands = async () => {
  try {
    const [categoriesRes, brandsRes] = await Promise.all([
      api('/categories'),
      api('/brands')
    ])
    categories.value = categoriesRes.data || categoriesRes || []
    brands.value = brandsRes.data || brandsRes || []
  } catch (err) {
    console.error('Error fetching categories/brands:', err)
  }
}

const validate = (): boolean => {
  const newErrors: any = {}

  if (!form.value.GadgetName || !form.value.GadgetName.trim()) {
    newErrors.GadgetName = ['Gadget name is required.']
  }

  if (!form.value.CategoryID) {
    newErrors.CategoryID = ['Category is required.']
  }

  if (!form.value.BrandID) {
    newErrors.BrandID = ['Brand is required.']
  }

  if (form.value.ReorderPoint !== null && form.value.ReorderPoint !== undefined && form.value.ReorderPoint < 0) {
    newErrors.ReorderPoint = ['Reorder point cannot be negative.']
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const handleSubmit = async () => {
  try {
    submitting.value = true
    error.value = ''
    errors.value = {}

    // Frontend validation first
    if (!validate()) {
      submitting.value = false
      return
    }

    await api('/gadgets', {
      method: 'POST',
      body: form.value
    })

    router.push('/gadgets')
  } catch (err: any) {
    console.error('Error creating gadget:', err)
    if (err.data?.errors) {
      errors.value = err.data.errors
    } else {
      error.value = err.data?.message || err.message || 'Failed to create gadget. Please try again.'
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

// Lifecycle
onMounted(() => {
  fetchCategoriesAndBrands()
})
</script>

<style scoped>
.gadgets-hero {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white;
  border-radius: 16px;
  padding: 3rem 2rem;
  position: relative;
  overflow: hidden;
}

.form-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  padding: 2.5rem;
  margin-bottom: 2rem;
}

.form-section {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border-left: 4px solid #4f46e5;
}

.form-section h5 {
  color: #4f46e5;
  font-weight: 600;
  margin-bottom: 1rem;
}


.btn-add-new {
  background: #4f46e5;
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.3s;
  margin-left: 0.5rem;
}

.btn-add-new:hover {
  background: #4338ca;
  transform: translateY(-1px);
  color: white;
}

.btn-submit {
  background: linear-gradient(135deg, #4f46e5, #3730a3);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.75rem 2rem;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
  color: white;
}

.btn-cancel {
  background: #6b7280;
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.75rem 2rem;
  font-weight: 600;
  transition: all 0.3s;
  text-decoration: none;
}

.btn-cancel:hover {
  background: #4b5563;
  transform: translateY(-1px);
  color: white;
}
</style>

