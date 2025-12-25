<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <Transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        @click="handleClose"
      ></div>
    </Transition>

    <!-- Modal -->
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="handleClose"
      >
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
          <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-2xl flex justify-between items-center">
              <h5 class="text-lg font-semibold flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>
                Add New Gadget
              </h5>
              <button @click="handleClose" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
              </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="handleSubmit" class="px-6 pt-6 pb-4" novalidate>
              <!-- Gadget Name -->
              <div class="mb-5">
                <label for="gadgetName" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-mobile-alt text-indigo-600 mr-2"></i>Gadget Name
                  <span class="text-red-500">*</span>
                </label>
                <input
                  id="gadgetName"
                  name="GadgetName"
                  type="text"
                  v-model="form.GadgetName"
                  class="w-full px-4 py-3 border-2 rounded-lg focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                  :class="fieldErrors.GadgetName ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500'"
                  placeholder="e.g., iPhone 15 Pro, MacBook Air M2"
                  required
                />
                <p v-if="fieldErrors.GadgetName" class="mt-1 text-sm text-red-600">{{ fieldErrors.GadgetName }}</p>
                <small class="text-gray-500 text-xs mt-1 block">
                  <i class="fas fa-info-circle mr-1"></i>Use a clear name so its easy to find in search and reports.
                </small>
              </div>

              <!-- Category & Brand -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <!-- Category -->
                <div>
                  <label for="CategoryID" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tags text-indigo-600 mr-2"></i>Category
                    <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="CategoryID"
                    name="CategoryID"
                    v-model="form.CategoryID"
                    class="w-full px-4 py-3 border-2 rounded-lg focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                    :class="fieldErrors.CategoryID ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500'"
                    required
                  >
                    <option value="">Select Category</option>
                    <option v-for="category in categories" :key="category.CategoryID" :value="category.CategoryID">
                      {{ category.CategoryName }}
                    </option>
                  </select>
                  <p v-if="fieldErrors.CategoryID" class="mt-1 text-sm text-red-600">{{ fieldErrors.CategoryID }}</p>
                  <small class="text-gray-500 text-xs mt-1 block">
                    <i class="fas fa-info-circle mr-1"></i>Categories group similar gadgets (e.g., Laptops, Phones).
                  </small>
                </div>

                <!-- Brand -->
                <div>
                  <label for="BrandID" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-award text-indigo-600 mr-2"></i>Brand
                    <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="BrandID"
                    name="BrandID"
                    v-model="form.BrandID"
                    class="w-full px-4 py-3 border-2 rounded-lg focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                    :class="fieldErrors.BrandID ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500'"
                    required
                  >
                    <option value="">Select Brand</option>
                    <option v-for="brand in brands" :key="brand.BrandID" :value="brand.BrandID">
                      {{ brand.BrandName }}
                    </option>
                  </select>
                  <p v-if="fieldErrors.BrandID" class="mt-1 text-sm text-red-600">{{ fieldErrors.BrandID }}</p>
                  <small class="text-gray-500 text-xs mt-1 block">
                    <i class="fas fa-info-circle mr-1"></i>Choose the manufacturer/brand for this gadget.
                  </small>
                </div>
              </div>

              <!-- Reorder Point -->
              <div class="mb-5">
                <label for="ReorderPoint" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-bell text-indigo-600 mr-2"></i>Low Stock Alert Level
                </label>
                <input
                  id="ReorderPoint"
                  name="ReorderPoint"
                  type="number"
                  v-model.number="form.ReorderPoint"
                  min="0"
                  class="w-full px-4 py-3 border-2 rounded-lg focus:ring-4 focus:ring-indigo-100 outline-none transition-all"
                  :class="fieldErrors.ReorderPoint ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500'"
                  placeholder="e.g., 10"
                />
                <p v-if="fieldErrors.ReorderPoint" class="mt-1 text-sm text-red-600">{{ fieldErrors.ReorderPoint }}</p>
                <small class="text-gray-500 text-xs mt-1 block">
                  <i class="fas fa-info-circle mr-1"></i>When stock falls below this number, the gadget will be marked as low stock.
                </small>
              </div>

              <!-- Global Error -->
              <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
              </div>

              <!-- Footer Buttons -->
              <div class="flex justify-end gap-3 pt-2 pb-4">
                <button
                  type="button"
                  @click="handleClose"
                  class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
                >
                  <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button
                  type="submit"
                  :disabled="submitting"
                  class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span
                    v-if="submitting"
                    class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"
                  ></span>
                  <i v-else class="fas fa-save mr-2"></i> Save Gadget
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
import { ref, onMounted, watch, onUnmounted } from 'vue'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits<{
  close: []
  created: []
}>()

// @ts-ignore - Nuxt auto-imports
const { api } = useApi()

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
const fieldErrors = ref<{
  GadgetName?: string
  CategoryID?: string
  BrandID?: string
  ReorderPoint?: string
}>({})

const fetchCategoriesAndBrands = async () => {
  try {
    const [categoriesRes, brandsRes] = await Promise.all([
      api('/categories'),
      api('/brands')
    ])

    categories.value = categoriesRes?.data || categoriesRes || []
    brands.value = brandsRes?.data || brandsRes || []
  } catch (err) {
    console.error('Error fetching categories/brands:', err)
  }
}

const resetForm = () => {
  form.value = {
    GadgetName: '',
    CategoryID: '',
    BrandID: '',
    ReorderPoint: 10
  }
  fieldErrors.value = {}
  error.value = ''
}

const validate = (): boolean => {
  const errs: typeof fieldErrors.value = {}

  if (!form.value.GadgetName || !form.value.GadgetName.trim()) {
    errs.GadgetName = 'Gadget name is required.'
  }

  if (!form.value.CategoryID) {
    errs.CategoryID = 'Please select a category.'
  }

  if (!form.value.BrandID) {
    errs.BrandID = 'Please select a brand.'
  }

  if (form.value.ReorderPoint !== null && form.value.ReorderPoint !== undefined && form.value.ReorderPoint < 0) {
    errs.ReorderPoint = 'Reorder level cannot be negative.'
  }

  fieldErrors.value = errs
  return Object.keys(errs).length === 0
}

const handleSubmit = async () => {
  fieldErrors.value = {}
  error.value = ''

  if (!validate()) {
    return
  }

  try {
    submitting.value = true

    await api('/gadgets', {
      method: 'POST',
      body: form.value
    })

    emit('created')
    handleClose()
  } catch (err: any) {
    console.error('Error creating gadget:', err)
    if (err.data?.errors) {
      const errors = err.data.errors
      // Map backend errors to first message per field
      const mapped: typeof fieldErrors.value = {}
      Object.keys(errors).forEach((key) => {
        const first = Array.isArray(errors[key]) ? errors[key][0] : String(errors[key])
        // @ts-ignore dynamic index
        mapped[key as keyof typeof mapped] = first
      })
      fieldErrors.value = mapped
    } else {
      error.value = err.data?.message || err.message || 'Failed to create gadget. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

const handleClose = () => {
  resetForm()
  emit('close')
}

watch(
  () => props.show,
  (val) => {
    if (typeof window !== 'undefined') {
      document.body.style.overflow = val ? 'hidden' : ''
    }
    if (val) {
      fetchCategoriesAndBrands()
    } else {
      resetForm()
    }
  }
)

onMounted(() => {
  if (props.show) {
    fetchCategoriesAndBrands()
    if (typeof window !== 'undefined') {
      document.body.style.overflow = 'hidden'
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
  transform: scale(0.95);
}
</style>
