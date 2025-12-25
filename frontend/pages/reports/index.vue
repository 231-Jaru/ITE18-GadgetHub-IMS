<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-6 shadow-lg">
      <h1 class="text-4xl font-bold mb-2">
        <i class="fas fa-chart-bar mr-2"></i> Reports & Analytics
      </h1>
      <p class="text-lg opacity-90">Comprehensive insights into your inventory management</p>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Inventory Purchases Report -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-col">
        <div class="bg-indigo-600 text-white text-center py-6">
          <i class="fas fa-chart-line text-4xl mb-3"></i>
          <h5 class="text-xl font-semibold">Inventory Purchases</h5>
        </div>
        <div class="p-6 text-center flex flex-col flex-grow">
          <p class="text-gray-600 mb-4">Monthly inventory purchases and procurement trends</p>
          <ul class="text-left text-sm text-gray-700 mb-4 space-y-2 flex-grow">
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              Monthly purchase analysis
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              Top purchased products
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              Inventory value tracking
            </li>
          </ul>
          <NuxtLink 
            to="/reports/purchases" 
            class="inline-flex items-center justify-center w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
          >
            <i class="fas fa-eye mr-2"></i> View Purchase Report
          </NuxtLink>
        </div>
      </div>

      <!-- Supplier Report -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-col">
        <div class="bg-purple-600 text-white text-center py-6">
          <i class="fas fa-truck text-4xl mb-3"></i>
          <h5 class="text-xl font-semibold">Supplier Reports</h5>
        </div>
        <div class="p-6 text-center flex flex-col flex-grow">
          <p class="text-gray-600 mb-4">Supplier performance and procurement analytics</p>
          <ul class="text-left text-sm text-gray-700 mb-4 space-y-2 flex-grow">
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              Performance metrics
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              Procurement analysis
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              Cost effectiveness
            </li>
          </ul>
          <NuxtLink 
            to="/reports/suppliers" 
            class="inline-flex items-center justify-center w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-semibold"
          >
            <i class="fas fa-eye mr-2"></i> View Supplier Report
          </NuxtLink>
        </div>
      </div>
    </div>

      <!-- Activity Log -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-col">
        <div class="bg-gray-800 text-white text-center py-6">
          <i class="fas fa-history text-4xl mb-3"></i>
          <h5 class="text-xl font-semibold">Activity Log</h5>
        </div>
        <div class="p-6 text-center flex flex-col flex-grow">
          <p class="text-gray-600 mb-4">
            Recent inventory activity based on stock restock transactions.
          </p>
          <ul class="text-left text-sm text-gray-700 mb-4 space-y-2 flex-grow">
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              Who restocked which gadget
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              Quantity and total restock value
            </li>
            <li class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              When each action happened
            </li>
          </ul>
          <NuxtLink 
            to="/reports/activity-log" 
            class="inline-flex items-center justify-center w-full px-4 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors font-semibold"
          >
            <i class="fas fa-eye mr-2"></i> View Activity Log
          </NuxtLink>
        </div>
      </div>
  </div>
</template>

<script setup lang="ts">
// @ts-ignore - Nuxt auto-imports
definePageMeta({
  middleware: 'auth'
})
</script>
