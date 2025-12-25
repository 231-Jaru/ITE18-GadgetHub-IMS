# Nuxt.js Frontend for Inventory Management System

This is the Vue.js/Nuxt.js frontend application for the Laravel Inventory Management System.

## Prerequisites

- Node.js (v18 or higher)
- npm or yarn
- Laravel backend running on `http://localhost:8000`

## Installation

1. **Navigate to the frontend directory:**
   ```bash
   cd frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Create environment file:**
   ```bash
   cp .env.example .env
   ```

4. **Update `.env` file with your Laravel API URL:**
   ```
   API_BASE_URL=http://localhost:8000/api
   ```

## Development

Start the development server:

```bash
npm run dev
```

The application will be available at `http://localhost:3000`

## Building for Production

Build the application:

```bash
npm run build
```

Preview the production build:

```bash
npm run preview
```

## Project Structure

```
frontend/
├── assets/          # CSS and other static assets
├── components/      # Vue components
├── composables/     # Composable functions (useApi, useAuth)
├── layouts/         # Layout components
├── middleware/      # Route middleware
├── pages/           # Page components (auto-routing)
├── stores/          # Pinia stores (state management)
├── app.vue          # Root component
├── nuxt.config.ts   # Nuxt configuration
└── package.json     # Dependencies
```

## Features

- ✅ Authentication (Login/Logout)
- ✅ Dashboard with statistics
- ✅ Protected routes with middleware
- ✅ API integration with Laravel backend
- ✅ Token-based authentication (Sanctum)
- ✅ Responsive design with Tailwind CSS

## API Configuration

The frontend connects to the Laravel API. Make sure:

1. Laravel backend is running on `http://localhost:8000`
2. CORS is properly configured in Laravel
3. Sanctum is configured to allow your frontend domain

## Troubleshooting

### CORS Errors

If you encounter CORS errors:

1. Check `config/sanctum.php` in Laravel - ensure your frontend URL is in `stateful` domains
2. Check Laravel CORS configuration
3. Ensure API_BASE_URL in `.env` matches your Laravel API URL

### Authentication Issues

- Clear browser localStorage if tokens are corrupted
- Check that Laravel Sanctum is properly configured
- Verify API endpoints are returning correct responses

## Next Steps

To convert more pages from Blade to Vue:

1. Create a new `.vue` file in `pages/` directory
2. Use the `useApi()` composable to fetch data
3. Use the `useAuth()` composable for authentication
4. Add route protection with `middleware: 'auth'` in `definePageMeta`

