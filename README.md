# GadgetHub Inventory Management System

A comprehensive inventory management system built with Laravel (backend) and Nuxt.js (frontend) for managing suppliers, gadgets, stock inventory, purchase orders, and transactions.

## 🚀 Features

### Authentication & User Management
- **Admin Registration**: Self-service admin account creation with secure password validation
- **Admin Login**: Token-based authentication using Laravel Sanctum
- **Role-Based Access**: Support for Admin and Staff roles (defaults to Staff for new registrations)
- **Session Management**: Secure token storage and automatic authentication

### Supplier Management
- Create, view, edit, and manage supplier information
- Stock entries tracking per supplier
- Total stock and stock entries display
- Supplier performance analytics

### Product Management
- **Gadget Catalog**: Manage gadget/product catalog with detailed information
- **Categories Management**: Organize products by categories with full CRUD operations
- **Brands Management**: Manage product brands with complete functionality
- Product details with category and brand associations

### Stock Inventory
- Track stock levels, adjustments, and inventory movements
- **Stock Adjustments**: Increase, decrease, or set stock quantities with reason tracking
- Stock history and movement tracking
- Low stock and out-of-stock alerts
- Stock value calculations

### Purchase Orders
- Create and manage purchase orders with multiple items
- **Advanced Search & Filters**: Date ranges, amount ranges, supplier/status filters, saved presets
- Status management (DRAFT, PENDING, RECEIVED, CANCELLED)
- Enhanced form validation with inline errors
- Purchase order items management

### Transaction History
- View and track all purchase transactions
- Transaction details with admin tracking
- Filter and search capabilities

### Reports & Analytics
- **Supplier Performance Reports**: Charts and animated statistics
- **Purchase Reports**: Monthly trends and category breakdowns
- **Activity Log**: Track recent inventory actions (who, what, when)
- Auto-refresh functionality
- Interactive charts and visualizations

### Dashboard
- Real-time overview of inventory status and low stock alerts
- Animated statistics cards
- Recent transactions overview
- Optimized for mobile devices

### Additional Features
- **Enhanced Validation**: Strong frontend validation with inline errors and helpful tooltips
- **Mobile-First Design**: Comprehensive responsive design optimizations
- **Animated Statistics**: Smooth number animations in all statistics cards
- **Soft Deletes**: Safe deletion with recovery capability for gadgets
- **Performance Optimizations**: Database indexes and query optimizations

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - PHP Framework
- **MySQL** - Database
- **Laravel Sanctum** - API Authentication

### Frontend
- **Nuxt.js 3** - Vue.js Framework
- **Tailwind CSS** - Styling
- **Chart.js** - Data Visualization
- **Pinia** - State Management
- **Axios** - HTTP Client

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL Database

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Gadgethub-IMS
   ```

2. **Backend Setup**
   ```bash
   # Install PHP dependencies
   composer install
   
   # Copy environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   
   # Configure database in .env file
   # Then run migrations
   php artisan migrate
   ```

3. **Frontend Setup**
   ```bash
   cd frontend
   
   # Install dependencies
   npm install
   ```

4. **Configure Environment**
   - Update `.env` file with your database credentials
   - Set `API_BASE_URL` in `frontend/.env` if needed (defaults to `http://localhost:8000/api`)

## 📚 Documentation

This README provides comprehensive documentation for setting up and running the GadgetHub Inventory Management System. The documentation includes:

- **Installation Instructions**: Step-by-step guide for installing dependencies and configuring the application
- **Running the Application**: Detailed instructions for both development and production environments
- **Project Structure**: Overview of the codebase organization
- **API Endpoints**: Complete list of available API endpoints with descriptions
- **Features Overview**: Detailed explanation of all system features and functionalities
- **Tech Stack**: Information about technologies and frameworks used

### Quick Start Guide

For a quick start, follow these essential steps:

1. **Backend Setup**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```

2. **Frontend Setup**
   ```bash
   cd frontend
   npm install
   ```

3. **Run the Application**
   ```bash
   # Terminal 1 - Backend
   php artisan serve
   
   # Terminal 2 - Frontend
   cd frontend
   npm run dev
   ```

4. **Access the Application**
   - Frontend: `http://localhost:3000`
   - Backend API: `http://localhost:8000/api`

For detailed setup instructions, refer to the [Installation](#-installation) and [Running the Application](#-running-the-application) sections below.

## 🚀 Running the Application

### Development Mode

**Option 1: Run separately**
```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend
cd frontend
npm run dev
```

**Option 2: Run together (if configured)**
```bash
composer run dev
```

### Production Build

```bash
# Build frontend
cd frontend
npm run build

# Serve Laravel (configure your web server)
php artisan serve
```

## 📁 Project Structure

```
Gadgethub-IMS/
├── app/                    # Laravel application code
│   ├── Console/
│   │   └── Commands/       # Artisan commands
│   ├── Http/
│   │   ├── Controllers/    # API Controllers
│   │   │   └── API/        # Authentication controller
│   │   └── Middleware/     # Custom middleware
│   ├── Models/             # Eloquent Models
│   ├── Repositories/       # Data repositories
│   └── Helpers/            # Helper classes
├── frontend/               # Nuxt.js frontend
│   ├── pages/              # Vue pages/routes
│   │   ├── dashboard.vue
│   │   ├── login.vue
│   │   ├── register.vue
│   │   ├── gadgets/
│   │   ├── stocks/
│   │   ├── suppliers/
│   │   ├── purchase-orders/
│   │   ├── stock-adjustments/
│   │   ├── transactions/
│   │   └── reports/
│   ├── components/        # Vue components
│   ├── composables/        # Vue composables (useAuth, useApi)
│   ├── stores/             # Pinia stores
│   └── layouts/            # Layout components
├── routes/
│   ├── api.php            # API routes
│   └── web.php            # Web routes
└── database/
    ├── migrations/        # Database migrations
    └── seeders/          # Database seeders
```

## 🔐 API Endpoints

### Authentication (Public)
- `POST /api/register` - Register a new admin account
- `POST /api/login` - Login and receive authentication token
- `POST /api/logout` - Logout and revoke token (protected)
- `GET /api/get-user` - Get current authenticated user info (protected)

### Resource Management (Protected - requires authentication)
- `/api/admins` - Admin user management (CRUD)
- `/api/suppliers` - Supplier management (CRUD)
- `/api/categories` - Category management (CRUD)
- `/api/brands` - Brand management (CRUD)
- `/api/gadgets` - Gadget management (CRUD)
- `/api/stocks` - Stock inventory management
- `/api/purchase-orders` - Purchase order management (CRUD)
- `/api/stock-adjustments` - Stock adjustment management (CRUD)
- `/api/transactions` - Transaction management (CRUD)

### Analytics & Reports (Protected)
- `/api/dashboard/analytics` - Dashboard statistics
- `/api/dashboard/summary` - Dashboard summary data
- `/api/stocks/low-stock` - Get low stock items
- `/api/stocks/out-of-stock` - Get out of stock items
- `/api/stocks/stock-value` - Calculate total stock value
- `/api/stocks/stock-movement` - Get stock movement history
- `/api/suppliers/report-data` - Supplier performance reports
- `/api/suppliers/performance` - Supplier performance metrics
- `/api/transactions/purchase-report-data` - Purchase reports
- `/api/transactions/purchases/monthly` - Monthly purchase data
- `/api/gadgets/top-selling` - Top selling gadgets
- `/api/categories/{id}/gadgets` - Get gadgets by category

## 📊 Features Overview

### Authentication

#### Login Page
The Login Page allows authorized users to securely access the system by entering their username and password. It validates user credentials and redirects successful logins to the dashboard while displaying error messages for invalid attempts. The page also includes a "Don't have an account? Register here" link that enables new users to navigate to the registration page and create their admin accounts. Features include:
- Username and password authentication
- Password visibility toggle
- Real-time validation with inline error messages
- Secure token-based authentication using Laravel Sanctum
- Automatic redirect to dashboard upon successful login
- "Don't have an account? Register here" link for new user registration

#### Registration Page
The Registration Page enables new administrators to create their accounts with secure password validation. Upon successful registration, users are automatically logged in and redirected to the dashboard. Features include:
- Username creation with uniqueness validation
- Password creation with minimum length requirements (6+ characters)
- Password confirmation matching
- Automatic role assignment (defaults to Staff)
- Real-time form validation with helpful error messages
- Password visibility toggles for both password fields
- Automatic login after successful registration
- Link back to login page for existing users

### Dashboard
- Real-time inventory statistics with animated counters
- Low stock alerts with quick actions
- Recent activity overview
- Total gadgets, suppliers, stock value, and transactions count

### Supplier Management
- Complete CRUD operations
- Supplier performance tracking
- Stock value by supplier analytics
- Contact information management

### Category & Brand Management
- Full CRUD operations for categories
- Full CRUD operations for brands
- Association with gadgets
- Category and brand filtering

### Gadget Management
- Complete product catalog management
- Category and brand associations
- Soft delete functionality
- Stock tracking per gadget
- Search and filter capabilities

### Stock Management
- Inventory tracking with real-time updates
- **Stock Adjustments**: Create adjustments with reasons (INCREASE, DECREASE, SET)
- Stock history and movement tracking
- Low stock and out-of-stock monitoring
- Stock value calculations

### Purchase Orders
- Create purchase orders with multiple items
- Status workflow (DRAFT → PENDING → RECEIVED)
- Advanced filtering and search
- Supplier association
- Date range filtering

### Stock Adjustments
- Create stock adjustments with type selection
- Reason tracking for audit purposes
- Admin tracking (who made the adjustment)
- Quantity before/after tracking

### Reports
- Interactive charts and graphs using Chart.js
- Supplier performance metrics
- Purchase trend analysis
- Category-wise purchase breakdown
- Activity log with admin tracking
- Monthly purchase trends

## 🎨 UI/UX

- **Modern Design**: Clean, professional interface with gradient headers
- **Responsive Layout**: Mobile-first approach with full tablet and desktop support
- **Color Scheme**: Purple/Indigo gradient theme throughout
- **Interactive Elements**: 
  - Interactive charts and visualizations (Chart.js)
  - Animated statistics with smooth number transitions
  - Hover effects and transitions
- **User Experience**:
  - Intuitive navigation with clear menu structure
  - Accessible components with proper ARIA labels
  - Enhanced form validation with inline errors
  - Advanced filtering and search capabilities
  - Activity logging for transparency and audit trails
  - Loading states and error handling
  - Success notifications and confirmations
- **Forms**: 
  - Password visibility toggles
  - Real-time validation
  - Helpful tooltips and hints
  - Confirmation modals for destructive actions

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📧 Support

For support, please open an issue in the repository.
