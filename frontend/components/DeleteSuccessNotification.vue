<template>
  <Transition name="slide-down">
    <div 
      v-if="showNotification"
      class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[10002] max-w-md w-full mx-4"
    >
      <div class="bg-red-100 border border-red-200 rounded-lg shadow-lg p-4 flex items-center gap-3">
        <!-- Trash Icon -->
        <div class="flex-shrink-0 w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
          <i class="fas fa-trash text-white text-sm"></i>
        </div>
        <!-- Message -->
        <p class="text-gray-800 font-medium flex-1">{{ message }}</p>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

interface Props {
  message?: string
  show?: boolean
  duration?: number
}

const props = withDefaults(defineProps<Props>(), {
  message: 'Deleted successfully!',
  show: false,
  duration: 3000
})

const showNotification = ref(props.show)

watch(() => props.show, (newVal) => {
  showNotification.value = newVal
  if (newVal) {
    setTimeout(() => {
      showNotification.value = false
    }, props.duration)
  }
})
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease-out;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translate(-50%, -20px);
}

.slide-down-enter-to {
  opacity: 1;
  transform: translate(-50%, 0);
}

.slide-down-leave-from {
  opacity: 1;
  transform: translate(-50%, 0);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translate(-50%, -20px);
}
</style>

