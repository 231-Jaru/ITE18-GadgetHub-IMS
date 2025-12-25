<template>
  <div v-if="pagination && pagination.last_page > 1" class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-sm text-gray-600">
      Showing {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} to 
      {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} 
      of {{ pagination.total }} results
    </div>
    <div class="flex items-center gap-2">
      <button 
        @click="$emit('page-change', pagination.current_page - 1)"
        :disabled="pagination.current_page === 1"
        :class="`px-4 py-2 rounded-lg transition-colors ${
          pagination.current_page === 1
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
        }`"
      >
        <i class="fas fa-chevron-left"></i> Previous
      </button>
      
      <div class="flex gap-1">
        <button 
          v-for="page in getPageNumbers()" 
          :key="page"
          @click="typeof page === 'number' && $emit('page-change', page)"
          :disabled="page === '...'"
          :class="`px-4 py-2 rounded-lg transition-colors ${
            pagination.current_page === page 
              ? 'bg-indigo-600 text-white' 
              : page === '...'
              ? 'bg-transparent text-gray-500 cursor-default'
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          }`"
        >
          {{ page }}
        </button>
      </div>
      
      <button 
        @click="$emit('page-change', pagination.current_page + 1)"
        :disabled="pagination.current_page === pagination.last_page"
        :class="`px-4 py-2 rounded-lg transition-colors ${
          pagination.current_page === pagination.last_page
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
        }`"
      >
        Next <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface PaginationData {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

const props = defineProps<{
  pagination: PaginationData | null
}>()

defineEmits<{
  'page-change': [page: number]
}>()

const getPageNumbers = (): (number | string)[] => {
  if (!props.pagination) return []
  
  const current = props.pagination.current_page
  const last = props.pagination.last_page
  const pages: (number | string)[] = []
  
  if (last <= 7) {
    // Show all pages if 7 or fewer
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    // Show first page
    pages.push(1)
    
    if (current > 3) {
      pages.push('...')
    }
    
    // Show pages around current
    const start = Math.max(2, current - 1)
    const end = Math.min(last - 1, current + 1)
    
    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
    
    if (current < last - 2) {
      pages.push('...')
    }
    
    // Show last page
    pages.push(last)
  }
  
  return pages
}
</script>
