<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <div 
      v-if="show" 
      class="fixed inset-0 bg-black bg-opacity-50 z-[10098] transition-opacity"
      @click="close"
    ></div>

    <!-- Modal -->
    <div 
      v-if="show"
      class="fixed inset-0 z-[10099] overflow-y-auto"
      @click.self="close"
    >
      <div class="flex items-center justify-center min-h-screen px-4">
        <div 
          class="bg-white rounded-lg shadow-2xl w-full max-w-md transform transition-all"
          @click.stop
        >
          <!-- Header -->
          <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-lg">
            <div class="flex justify-between items-center">
              <h5 class="text-lg font-semibold">
                <i class="fas fa-plus-circle mr-2"></i>Add New Brand
              </h5>
              <button 
                type="button" 
                class="text-white hover:text-gray-200 transition-colors"
                @click="close"
                aria-label="Close"
              >
                <i class="fas fa-times text-xl"></i>
              </button>
            </div>
          </div>

          <!-- Body -->
          <div class="p-6">
            <form @submit.prevent="handleSubmit">
              <div class="mb-4">
                <label for="brandName" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-award mr-2 text-indigo-600"></i>Brand Name
                </label>
                <input 
                  type="text" 
                  class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                  id="brandName"
                  name="brandName"
                  v-model="brandName"
                  placeholder="Enter brand name (e.g., Apple, Samsung, Sony)" 
                  required
                >
              </div>

              <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ error }}
              </div>

              <!-- Footer -->
              <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button 
                  type="button" 
                  class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
                  @click="close"
                >
                  Cancel
                </button>
                <button 
                  type="submit" 
                  class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="submitting"
                >
                  <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                  <i v-else class="fas fa-save mr-2"></i>Add Brand
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits<{
  close: []
  created: [brand: any]
}>()

const { api } = useApi()

// State
const brandName = ref('')
const submitting = ref(false)
const error = ref('')

// Methods
const handleSubmit = async () => {
  try {
    submitting.value = true
    error.value = ''

    const brand = await api('/brands', {
      method: 'POST',
      body: {
        BrandName: brandName.value
      }
    })

    emit('created', brand)
    close()
  } catch (err: any) {
    console.error('Error creating brand:', err)
    if (err.data?.errors) {
      const errors = err.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else {
      error.value = err.data?.message || err.message || 'Failed to create brand. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

const close = () => {
  brandName.value = ''
  error.value = ''
  emit('close')
}
</script>
