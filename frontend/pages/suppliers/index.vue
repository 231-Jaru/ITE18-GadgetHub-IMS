<template>
  <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl px-4 sm:px-8 py-6 sm:py-8 mb-6 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="mb-2 md:mb-0">
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-1 sm:mb-2">
            <i class="fas fa-truck mr-2"></i> Supplier Management
          </h1>
          <p class="text-sm sm:text-base md:text-lg opacity-90">
            Manage your suppliers and track their stock contributions
          </p>
        </div>
        <NuxtLink 
          to="/suppliers/create" 
          class="w-full sm:w-auto text-center px-5 py-2.5 sm:px-6 sm:py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold shadow-md flex items-center justify-center text-sm sm:text-base"
        >
          <i class="fas fa-plus mr-2"></i> Add New Supplier
        </NuxtLink>
      </div>
    </div>

    <!-- Success Notification -->
    <SuccessNotification 
      :show="showCreateSuccess || showUpdateSuccess" 
      :message="showCreateSuccess ? 'Supplier created successfully!' : 'Supplier updated successfully!'"
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
      :item-name="supplierToDelete ? suppliers.find(s => s.SupplierID === supplierToDelete)?.SupplierName || 'this supplier' : 'this supplier'"
      message="Are you sure you want to delete this supplier?"
      warning="This action cannot be undone. All associated data will be permanently removed."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />

    <!-- Success/Error Messages -->
    <div v-if="message && !showDeleteSuccess" :class="`mb-4 p-4 rounded-lg flex items-center justify-between ${messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'}`">
      <div class="flex items-center">
        <i :class="`fas ${messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2`"></i>
        <span>{{ message }}</span>
      </div>
      <button @click="message = ''" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="stats-card primary">
        <div class="text-center p-6">
          <div class="icon-wrapper">
            <i class="fas fa-truck"></i>
          </div>
          <h4 class="text-3xl font-bold mb-2">
            <AnimatedNumber :value="stats.totalSuppliers || 0" />
          </h4>
          <p class="text-gray-600 font-medium">Total Suppliers</p>
        </div>
      </div>
      <div class="stats-card success">
        <div class="text-center p-6">
          <div class="icon-wrapper">
            <i class="fas fa-plus-circle"></i>
          </div>
          <h4 class="text-3xl font-bold mb-2">
            <AnimatedNumber :value="stats.newThisMonth || 0" />
          </h4>
          <p class="text-gray-600 font-medium">New This Month</p>
        </div>
      </div>
      <div class="stats-card info">
        <div class="text-center p-6">
          <div class="icon-wrapper">
            <i class="fas fa-envelope"></i>
          </div>
          <h4 class="text-3xl font-bold mb-2">
            <AnimatedNumber :value="stats.withEmail || 0" />
          </h4>
          <p class="text-gray-600 font-medium">With Email</p>
        </div>
      </div>
      <div class="stats-card warning">
        <div class="text-center p-6">
          <div class="icon-wrapper">
            <i class="fas fa-phone"></i>
          </div>
          <h4 class="text-3xl font-bold mb-2">
            <AnimatedNumber :value="stats.withPhone || 0" />
          </h4>
          <p class="text-gray-600 font-medium">With Phone</p>
        </div>
      </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-lg mb-6 p-6">
      <h5 class="text-lg font-semibold mb-4">
        <i class="fas fa-search mr-2 text-gray-600"></i> Search Suppliers
      </h5>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input 
              type="text" 
              id="searchTerm"
              name="searchTerm"
              v-model="searchTerm"
              placeholder="Search by name, contact person, email, phone, ID..."
              class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all"
            >
          </div>
        </div>
        <div>
          <select 
            id="sortBy"
            name="sortBy"
            v-model="sortBy"
            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all"
          >
            <option value="name">Sort by Name</option>
            <option value="stock">Sort by Stock Quantity</option>
            <option value="recent">Sort by Recent</option>
          </select>
        </div>
        <div>
          <button 
            @click="clearSearch"
            class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold"
          >
            <i class="fas fa-times mr-2"></i> Clear
          </button>
        </div>
      </div>
    </div>

    <!-- Suppliers Table -->
    <div ref="suppliersTableRef" class="bg-white rounded-xl shadow-lg">
      <div class="bg-indigo-600 text-white px-6 py-4 flex justify-between items-center rounded-t-xl">
        <h5 class="text-xl font-semibold">
          <i class="fas fa-table mr-2"></i> Supplier Directory
        </h5>
        <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-sm font-semibold">
          {{ filteredSuppliers.length }} {{ filteredSuppliers.length !== 1 ? 'suppliers' : 'supplier' }}
        </span>
      </div>
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
          <p class="mt-4 text-gray-600">Loading suppliers...</p>
        </div>

        <!-- Suppliers Table -->
        <div v-else-if="filteredSuppliers.length > 0" class="overflow-x-auto -mx-6 px-6">
          <table class="w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Contact Person</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Stock</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Stock Entries</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="supplier in sortedSuppliers" 
                :key="supplier.SupplierID"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-3">{{ supplier.SupplierID }}</td>
                <td class="px-4 py-3">
                  <div class="font-semibold">{{ supplier.SupplierName }}</div>
                </td>
                <td class="px-4 py-3">{{ supplier.ContactPerson || 'N/A' }}</td>
                <td class="px-4 py-3">{{ supplier.Email || 'N/A' }}</td>
                <td class="px-4 py-3">{{ supplier.Phone || 'N/A' }}</td>
                <td class="px-4 py-3">
                  <span
                    :class="`px-3 py-1 rounded-full text-xs sm:text-sm font-semibold ${
                      totalStock(supplier) > 0 ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-600'
                    }`"
                  >
                    {{ totalStock(supplier) }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span
                    :class="`px-3 py-1 rounded-full text-xs sm:text-sm font-semibold ${
                      stockEntries(supplier) > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
                    }`"
                  >
                    {{ stockEntries(supplier) }}
                  </span>
                </td>
                <td class="px-4 py-3" @click.stop.prevent>
                  <div class="dropdown relative" @click.stop.prevent>
                    <button 
                      @click.stop.prevent="toggleActionsMenu(supplier.SupplierID, $event)"
                      type="button"
                      class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors"
                      :aria-label="`Actions menu for ${supplier.SupplierName}`"
                      :aria-expanded="activeActionsMenu === supplier.SupplierID"
                    >
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div 
                      v-if="activeActionsMenu === supplier.SupplierID"
                      :class="[
                        'absolute right-0 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-[9999] dropdown-menu',
                        dropdownPosition === 'above' ? 'bottom-full mb-1' : 'top-full mt-1'
                      ]"
                      @click.stop
                    >
                      <button 
                        @click.stop="(e) => handleEditClick(e, supplier.SupplierID)"
                        type="button"
                        class="w-full text-left px-4 py-2 text-indigo-600 hover:bg-gray-50 transition-colors flex items-center dropdown-item"
                        role="menuitem"
                      >
                        <i class="fas fa-edit mr-2"></i> Edit
                      </button>
                      <hr class="my-1 border-gray-200">
                      <button 
                        @click.stop.prevent="deleteSupplier(supplier.SupplierID)"
                        type="button"
                        class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50 transition-colors flex items-center dropdown-item"
                        role="menuitem"
                      >
                        <i class="fas fa-trash mr-2"></i> Delete
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <i class="fas fa-truck text-6xl text-gray-300 mb-4"></i>
          <h5 class="text-xl font-semibold text-gray-600 mb-2">No suppliers found</h5>
          <p class="text-gray-500 mb-6">Start by adding your first supplier.</p>
          <NuxtLink 
            to="/suppliers/create" 
            class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
          >
            <i class="fas fa-plus mr-2"></i> Add Supplier
          </NuxtLink>
        </div>

        <!-- Pagination -->
        <div class="px-6 pb-6">
          <Pagination 
            v-if="pagination && !loading"
            :pagination="pagination"
            @page-change="goToPage"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'

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
const suppliers = ref<any[]>([])
const loading = ref(true)
const searchTerm = ref('')
const sortBy = ref('name')
const message = ref('')
const messageType = ref<'success' | 'error'>('success')
const activeActionsMenu = ref<number | null>(null)
const dropdownPosition = ref<'above' | 'below'>('below')
const showDeleteModal = ref(false)
const showDeleteSuccess = ref(false)
const showCreateSuccess = ref(false)
const showUpdateSuccess = ref(false)
const deleting = ref(false)
const supplierToDelete = ref<number | null>(null)
const stats = ref({
  totalSuppliers: 0,
  newThisMonth: 0,
  withEmail: 0,
  withPhone: 0
})
const currentPage = ref(1)
const itemsPerPage = ref(15)
const suppliersTableRef = ref<HTMLElement | null>(null)

// Computed
const filteredSuppliers = computed(() => {
  if (!searchTerm.value) {
    return suppliers.value
  }
  const term = searchTerm.value.toLowerCase().trim()
  const searchNumber = term.match(/^\d+$/) ? parseInt(term) : null
  
  return suppliers.value.filter(supplier => {
    const id = supplier.SupplierID.toString()
    const name = (supplier.SupplierName || '').toLowerCase()
    const contact = (supplier.ContactPerson || '').toLowerCase()
    const email = (supplier.Email || '').toLowerCase()
    const phone = (supplier.Phone || '').toString()
    
    // Check if it's an exact ID match
    if (searchNumber !== null && supplier.SupplierID === searchNumber) {
      return true
    }
    
    return id.includes(term) || name.includes(term) || contact.includes(term) || email.includes(term) || phone.includes(term)
  }).sort((a, b) => {
    // Prioritize exact ID matches
    if (searchNumber !== null) {
      if (a.SupplierID === searchNumber && b.SupplierID !== searchNumber) return -1
      if (b.SupplierID === searchNumber && a.SupplierID !== searchNumber) return 1
      if (a.SupplierID === searchNumber && b.SupplierID === searchNumber) return 0
    }
    return 0
  })
})

const sortedSuppliers = computed(() => {
  const filtered = [...filteredSuppliers.value]
  
  // Check if search term is a number (ID search)
  const searchNumber = searchTerm.value.trim().match(/^\d+$/) ? parseInt(searchTerm.value.trim()) : null
  
  let sorted: any[]
  switch (sortBy.value) {
    case 'name':
      sorted = filtered.sort((a, b) => {
        // If searching by exact ID, prioritize exact match
        if (searchNumber !== null) {
          if (a.SupplierID === searchNumber && b.SupplierID !== searchNumber) return -1
          if (b.SupplierID === searchNumber && a.SupplierID !== searchNumber) return 1
        }
        return a.SupplierName.localeCompare(b.SupplierName)
      })
      break
    case 'stock':
      sorted = filtered.sort((a, b) => {
        // If searching by exact ID, prioritize exact match
        if (searchNumber !== null) {
          if (a.SupplierID === searchNumber && b.SupplierID !== searchNumber) return -1
          if (b.SupplierID === searchNumber && a.SupplierID !== searchNumber) return 1
        }
        return totalStock(b) - totalStock(a)
      })
      break
    case 'recent':
      sorted = filtered.sort((a, b) => {
        // If searching by exact ID, prioritize exact match
        if (searchNumber !== null) {
          if (a.SupplierID === searchNumber && b.SupplierID !== searchNumber) return -1
          if (b.SupplierID === searchNumber && a.SupplierID !== searchNumber) return 1
        }
        const dateA = new Date(a.created_at || 0).getTime()
        const dateB = new Date(b.created_at || 0).getTime()
        return dateB - dateA
      })
      break
    default:
      sorted = filtered
      // If searching by exact ID, prioritize exact match even in default sort
      if (searchNumber !== null) {
        sorted = filtered.sort((a, b) => {
          if (a.SupplierID === searchNumber && b.SupplierID !== searchNumber) return -1
          if (b.SupplierID === searchNumber && a.SupplierID !== searchNumber) return 1
          return 0
        })
      }
  }
  
  // Apply pagination
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return sorted.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredSuppliers.value.length / itemsPerPage.value)
})

const pagination = computed(() => {
  if (totalPages.value <= 1) return null
  return {
    current_page: currentPage.value,
    last_page: totalPages.value,
    per_page: itemsPerPage.value,
    total: filteredSuppliers.value.length
  }
})

// Methods
const fetchSuppliers = async () => {
  try {
    loading.value = true
    const response = await api('/suppliers')
    
    // Handle different response structures and remove duplicates
    let fetchedSuppliers: any[] = []
    if (Array.isArray(response)) {
      fetchedSuppliers = response
    } else if (response.data) {
      fetchedSuppliers = Array.isArray(response.data) ? response.data : []
    }
    
    // Remove duplicates based on SupplierID
    const uniqueSuppliers = fetchedSuppliers.filter((supplier: any, index: number, self: any[]) => 
      index === self.findIndex((s: any) => s.SupplierID === supplier.SupplierID)
    )
    suppliers.value = uniqueSuppliers
    
    // Calculate stats
    const now = new Date()
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1)
    
    stats.value = {
      totalSuppliers: suppliers.value.length,
      newThisMonth: suppliers.value.filter(s => {
        const created = new Date(s.created_at || 0)
        return created >= startOfMonth
      }).length,
      withEmail: suppliers.value.filter(s => s.Email).length,
      withPhone: suppliers.value.filter(s => s.Phone).length
    }
  } catch (err: any) {
    console.error('Error fetching suppliers:', err)
    const errorMessage = err.data?.message || err.message || 'Failed to load suppliers.'
    message.value = errorMessage + ' Please try again or refresh the page.'
    messageType.value = 'error'
    suppliers.value = [] // Clear on error
  } finally {
    loading.value = false
  }
}

const goToPage = (page: number) => {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
  // Scroll to suppliers table section
  if (process.client) {
    setTimeout(() => {
      if (suppliersTableRef.value) {
        suppliersTableRef.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
      }
    }, 100)
  }
}

watch([searchTerm, sortBy], () => {
  currentPage.value = 1 // Reset to first page when filters change
})

const totalStock = (supplier: any) => {
  if (!supplier.stocks || !Array.isArray(supplier.stocks)) return 0
  return supplier.stocks.reduce((sum: number, stock: any) => sum + (parseInt(stock.QuantityAdded) || 0), 0)
}

const stockEntries = (supplier: any) => {
  if (!supplier.stocks || !Array.isArray(supplier.stocks)) return 0
  return supplier.stocks.length
}

// Navigate to supplier view/edit page when clicking "Edit" button in dropdown
const handleEditClick = (event: MouseEvent, id: number) => {
  event.stopPropagation()
  event.preventDefault()
  
  if (!id) {
    return
  }
  
  // Close the dropdown menu first
  activeActionsMenu.value = null
  
  // Navigate to supplier view/edit page: /suppliers/[id] (acts as both view + edit)
  window.location.href = `/suppliers/${id}`
}

const toggleActionsMenu = async (id: number, event?: Event) => {
  const wasOpen = activeActionsMenu.value === id
  activeActionsMenu.value = wasOpen ? null : id
  
  if (!wasOpen && event) {
    await nextTick()
    // Calculate optimal dropdown position based on available space
    const button = (event.target as HTMLElement).closest('button') as HTMLElement
    if (!button) return
    
    const buttonRect = button.getBoundingClientRect()
    const viewportHeight = window.innerHeight
    const viewportWidth = window.innerWidth
    const spaceBelow = viewportHeight - buttonRect.bottom
    const spaceAbove = buttonRect.top
    const spaceRight = viewportWidth - buttonRect.right
    
    // Approximate dropdown dimensions (2 buttons + padding)
    const dropdownHeight = 110 // Edit + Delete buttons + padding
    const dropdownWidth = 192 // w-48 = 12rem = 192px
    const buffer = 20 // Extra buffer space
    
    // Check if dropdown would fit below
    const fitsBelow = spaceBelow >= (dropdownHeight + buffer)
    
    // Check if dropdown would fit above
    const fitsAbove = spaceAbove >= (dropdownHeight + buffer)
    
    // Determine position:
    // 1. If fits below, show below (preferred for top/middle rows)
    // 2. If doesn't fit below but fits above, show above (bottom rows)
    // 3. If neither fits perfectly, choose the side with more space
    if (fitsBelow) {
      dropdownPosition.value = 'below'
    } else if (fitsAbove) {
      dropdownPosition.value = 'above'
    } else {
      // Choose side with more space as fallback
      dropdownPosition.value = spaceAbove > spaceBelow ? 'above' : 'below'
    }
  }
}

const deleteSupplier = (id: number) => {
  supplierToDelete.value = id
  showDeleteModal.value = true
  activeActionsMenu.value = null
}

const confirmDelete = async () => {
  if (!supplierToDelete.value) return
  
  // Prevent double deletion
  if (deleting.value) {
    return
  }
  
  try {
    deleting.value = true
    await api(`/suppliers/${supplierToDelete.value}`, { method: 'DELETE' })
    
    showDeleteModal.value = false
    showDeleteSuccess.value = true
    fetchSuppliers()
    supplierToDelete.value = null
  } catch (err: any) {
    console.error('Error deleting supplier:', err)
    const errorMessage = err.data?.message || err.message || 'Failed to delete supplier.'
    message.value = errorMessage + ' Please try again.'
    messageType.value = 'error'
    showDeleteModal.value = false
    supplierToDelete.value = null
  } finally {
    deleting.value = false
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
  supplierToDelete.value = null
}

const clearSearch = () => {
  searchTerm.value = ''
  sortBy.value = 'name'
}

// Close actions menu when clicking outside
onMounted(() => {
  fetchSuppliers()
  
  // Close menu when clicking outside
  const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement
    // Don't close if clicking inside the dropdown menu or buttons
    if (
      target.closest('.dropdown') || 
      target.closest('.dropdown-menu') ||
      target.closest('.dropdown-item') ||
      target.closest('button')
    ) {
      return
    }
    activeActionsMenu.value = null
  }
  
  // Close menu when scrolling (dropdown position might change)
  const handleScroll = () => {
    if (activeActionsMenu.value !== null) {
      activeActionsMenu.value = null
    }
  }
  
  // Close menu on window resize (viewport might change)
  const handleResize = () => {
    if (activeActionsMenu.value !== null) {
      activeActionsMenu.value = null
    }
  }
  
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('scroll', handleScroll, true)
  window.addEventListener('resize', handleResize)
  
  // Cleanup on unmount
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    window.removeEventListener('scroll', handleScroll, true)
    window.removeEventListener('resize', handleResize)
  })
  
  // Check for success query parameters
  if (route.query.created === 'true') {
    showCreateSuccess.value = true
    // Remove query parameter from URL
    router.replace({ query: {} })
  } else if (route.query.updated === 'true') {
    showUpdateSuccess.value = true
    // Remove query parameter from URL
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

