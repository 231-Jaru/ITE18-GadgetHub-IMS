<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <Transition name="fade">
      <div 
        v-if="show" 
        class="fixed inset-0 bg-black bg-opacity-50 z-[10000]"
        @click="handleCancel"
      ></div>
    </Transition>

    <!-- Modal -->
    <Transition name="modal">
      <div 
        v-if="show"
        class="fixed inset-0 z-[10001] overflow-y-auto"
        @click.self="handleCancel"
      >
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
          <div 
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="bg-red-600 text-white rounded-t-2xl p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-red-700 rounded-full flex items-center justify-center mr-4">
                  <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                  <h3 class="text-xl font-bold">Confirm Deletion</h3>
                  <p class="text-red-100 text-sm mt-1">This action cannot be undone</p>
                </div>
              </div>
            </div>

            <!-- Body -->
            <div class="p-6">
              <p class="text-gray-700 mb-4">
                {{ message || `Are you sure you want to delete ${itemName ? `"${itemName}"` : `this ${itemType || 'item'}`}?` }}
              </p>
              <p v-if="warning" class="text-sm text-gray-500 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <i class="fas fa-info-circle mr-2 text-yellow-600"></i>{{ warning }}
              </p>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 rounded-b-2xl px-6 py-4 flex justify-end gap-3">
              <button 
                type="button" 
                class="px-5 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-semibold"
                @click="handleCancel"
                :disabled="deleting"
              >
                Cancel
              </button>
              <button 
                type="button" 
                class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                @click="handleConfirm"
                :disabled="deleting"
              >
                <span v-if="deleting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                <i v-else class="fas fa-trash mr-2"></i>
                {{ deleting ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

interface Props {
  show: boolean
  message?: string
  itemType?: string
  itemName?: string
  warning?: string
  deleting?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  show: false,
  message: '',
  itemType: 'item',
  itemName: '',
  warning: '',
  deleting: false
})

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()

const handleConfirm = () => {
  if (!props.deleting) {
    emit('confirm')
  }
}

const handleCancel = () => {
  if (!props.deleting) {
    emit('cancel')
  }
}
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

.modal-enter-from {
  opacity: 0;
  transform: scale(0.9);
}

.modal-enter-to {
  opacity: 1;
  transform: scale(1);
}

.modal-leave-from {
  opacity: 1;
  transform: scale(1);
}

.modal-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>

