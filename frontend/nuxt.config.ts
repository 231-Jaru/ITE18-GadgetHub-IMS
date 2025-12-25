// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-12-04',
  
  devtools: { enabled: true },
  
  modules: [
    '@pinia/nuxt',
    '@nuxtjs/tailwindcss'
  ],

  css: ['~/assets/css/main.css'],

  runtimeConfig: {
    // Private keys (only available on server-side)
    
    // Public keys (exposed to client-side)
    public: {
      apiBase: process.env.API_BASE_URL || 'http://localhost:8000/api'
    }
  },

  app: {
    head: {
      title: 'GadgetHub Inventory Management System',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'GadgetHub - Comprehensive Inventory Management System for Suppliers, Gadgets, Stock, and Transactions' }
      ],
      link: [
        { rel: 'stylesheet', href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' }
      ]
    }
  },

  ssr: false, // Disable SSR for now (can enable later if needed)
})

