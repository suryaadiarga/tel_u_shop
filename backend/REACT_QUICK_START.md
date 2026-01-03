# 🚀 Quick Start Guide untuk React Developer

## Backend Server

### Start Server
```bash
cd tel_u_shop
php artisan serve
```

API akan tersedia di: `http://127.0.0.1:8000/api`

---

## React Setup (5 Minutes)

### 1. Create React Project
```bash
npm create vite@latest tel-u-shop-frontend -- --template react
cd tel-u-shop-frontend
npm install
npm install axios react-query react-router-dom zustand
```

### 2. Create `.env`
```
VITE_API_URL=http://127.0.0.1:8000/api
```

### 3. Create API Client (`src/api/client.js`)
```javascript
import axios from 'axios';

const client = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add token interceptor
client.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default client;
```

### 4. Example: Login Component
```jsx
import { useState } from 'react';
import client from '../api/client';

export function LoginForm() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleLogin = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    
    try {
      const response = await client.post('/login', { email, password });
      
      // Save token
      localStorage.setItem('auth_token', response.data.data.access_token);
      localStorage.setItem('user', JSON.stringify(response.data.data.user));
      
      // Redirect or update app state
      window.location.href = '/dashboard';
    } catch (err) {
      setError(err.response?.data?.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleLogin}>
      {error && <div className="error">{error}</div>}
      <input
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="Email"
        required
      />
      <input
        type="password"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        placeholder="Password"
        required
      />
      <button type="submit" disabled={loading}>
        {loading ? 'Loading...' : 'Login'}
      </button>
    </form>
  );
}
```

### 5. Example: Cart Component
```jsx
import { useEffect, useState } from 'react';
import client from '../api/client';

export function Cart() {
  const [cart, setCart] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    client.get('/cart')
      .then(res => setCart(res.data.data))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <div>Loading...</div>;
  if (!cart) return <div>Cart is empty</div>;

  return (
    <div>
      <h2>Shopping Cart</h2>
      {cart.items.map(item => (
        <div key={item.id}>
          <h3>{item.product.name}</h3>
          <p>Qty: {item.qty} × Rp{item.price_snapshot}</p>
          <button onClick={() => removeItem(item.id)}>Remove</button>
        </div>
      ))}
      <h3>Total: Rp{cart.total}</h3>
      <button onClick={handleCheckout}>Checkout</button>
    </div>
  );
}
```

---

## Available API Endpoints

### Auth
```
POST   /register
POST   /login
GET    /me
POST   /logout
```

### Customer
```
GET    /cart
POST   /cart/add/{id}
PUT    /cart/update/{id}
DELETE /cart/remove/{id}
DELETE /cart/clear
POST   /checkout
GET    /wallet/balance
POST   /wallet/topup
GET    /wallet/transactions
GET    /activities
GET    /activities/{id}
GET    /activities/{id}/track
```

### Merchant
```
GET    /merchant/products
POST   /merchant/products
PUT    /merchant/products/{id}
DELETE /merchant/products/{id}
GET    /merchant/orders
PUT    /merchant/orders/{id}/status
```

### Admin
```
GET    /admin/users
GET    /admin/users/{id}
PUT    /admin/users/{id}/role
GET    /admin/orders
PUT    /admin/orders/{id}/status
```

---

## Test Data

### Register Test User
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": 3
  }'
```

### Login Test User
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

---

## Common Tasks

### Add to Cart
```javascript
const response = await client.post('/cart/add/5', { qty: 2 });
```

### Get Current User
```javascript
const user = await client.get('/me');
console.log(user.data.data);
```

### Top Up Wallet
```javascript
const result = await client.post('/wallet/topup', { amount: 500000 });
```

### Checkout
```javascript
const order = await client.post('/checkout');
console.log(order.data.data.order_id);
```

---

## Error Handling

All API errors follow this format:
```json
{
  "status": "error",
  "message": "Error description",
  "error": "Detailed message"
}
```

Example error handler:
```javascript
try {
  await client.post('/checkout');
} catch (error) {
  if (error.response?.status === 401) {
    // Token expired, redirect to login
    localStorage.removeItem('auth_token');
    window.location.href = '/login';
  } else {
    console.error(error.response?.data?.message);
  }
}
```

---

## Useful Commands

```bash
# Start dev server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Check Vite config
cat vite.config.js
```

---

## Full Documentation

See `API_DOCUMENTATION.md` in the backend for complete API reference with all endpoints, parameters, and examples.

---

**Ready to build? Let's go! 🎉**
