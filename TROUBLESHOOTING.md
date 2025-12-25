# Troubleshooting Guide

## 405 Error: "Method Not Allowed"

If you see this error:
```json
{
  "response_code": 405,
  "status": "error",
  "message": "Method Not Allowed. The login endpoint only accepts POST requests."
}
```

### This is NORMAL if:
- You're accessing `/api/login` or `/api/register` directly in your browser (GET request)
- You're testing the API endpoint with a GET request

### This is NOT normal if:
- You're using the web login form at `/login`
- You're using the web registration form at `/register`

### Solution:

**For Web Users:**
- Use `/login` for login (NOT `/api/login`)
- Use `/register` for registration (NOT `/api/register`)

**For API Users:**
- Use POST method with proper headers:
  ```bash
  curl -X POST "http://127.0.0.1:8000/api/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email": "user@example.com", "password": "password123"}'
  ```

## Common Issues

### 1. Login Form Not Working
- **Check:** Make sure you're using `/login` (web route), not `/api/login`
- **Check:** Ensure email and password fields are filled
- **Check:** Password must be at least 8 characters
- **Check:** User must exist in the `users` table

### 2. Registration Form Not Working
- **Check:** Make sure you're using `/register` (web route), not `/api/register`
- **Check:** All fields must be filled
- **Check:** Name must be at least 4 characters
- **Check:** Password must be at least 8 characters
- **Check:** Passwords must match

### 3. "Email field is required" Error
- **Check:** Make sure you're using the email field (not username)
- **Check:** Email must be in valid format (e.g., user@example.com)

### 4. "Unauthorized" Error
- **Check:** Email and password are correct
- **Check:** User exists in the database
- **Check:** Password is correct

## Testing Your Setup

### 1. Test Web Login:
1. Go to `http://127.0.0.1:8000/login`
2. Enter email and password
3. Click "Login"

### 2. Test Web Registration:
1. Go to `http://127.0.0.1:8000/register`
2. Fill in all fields
3. Click "Register"

### 3. Test API Login:
```bash
curl -X POST "http://127.0.0.1:8000/api/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### 4. Create a Test User:
```bash
php artisan tinker
```
Then:
```php
\App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123', // The 'hashed' cast will automatically hash it
]);
```

## Routes Summary

### Web Routes (for browser/forms):
- `GET /login` - Show login form
- `POST /login` - Process login
- `GET /register` - Show registration form
- `POST /register` - Process registration
- `POST /logout` - Logout user

### API Routes (for API clients):
- `POST /api/login` - Login via API
- `POST /api/register` - Register via API
- `POST /api/logout` - Logout via API (requires auth)
- `GET /api/get-user` - Get user info (requires auth)

**Important:** Web forms use web routes (`/login`, `/register`), NOT API routes (`/api/login`, `/api/register`).

