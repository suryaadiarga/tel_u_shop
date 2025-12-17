# Tel-U Shop API - Complete Documentation

## 🚀 Aplikasi Sudah Diperbaiki dan Siap Digunakan

Berikut adalah dokumentasi lengkap untuk mengintegrasikan React frontend dengan Laravel API backend.

---

## 📋 Table of Contents

1. [Setup & Installation](#setup--installation)
2. [Authentication API](#authentication-api)
3. [Customer APIs](#customer-apis)
4. [Merchant APIs](#merchant-apis)
5. [Admin APIs](#admin-apis)
6. [Error Handling](#error-handling)
7. [React Integration Guide](#react-integration-guide)

---

## Setup & Installation

### Backend Requirements
- PHP 8.1+
- MySQL 8.0+
- Laravel 11
- Composer

### Starting the Server

```bash
cd tel_u_shop
php artisan serve
```

Server akan berjalan di `http://127.0.0.1:8000`

### Database Setup

```bash
# Run migrations
php artisan migrate

# Optional: Seed demo data
php artisan db:seed
```

---

## Authentication API

### 1. Register (Public)

**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": 3
}
```

**Role Values:**
- `1` = Admin
- `2` = Merchant  
- `3` = Customer (default)

**Response (201 Created):**
```json
{
    "status": "success",
    "message": "Pendaftaran berhasil.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role_id": 3,
            "wallet_balance": 0,
            "created_at": "2025-12-17T10:30:00Z"
        },
        "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "token_type": "Bearer"
    }
}
```

### 2. Login (Public)

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200 OK):**
```json
{
    "status": "success",
    "message": "Login berhasil.",
    "data": {
        "user": {...},
        "access_token": "...",
        "token_type": "Bearer"
    }
}
```

### 3. Get Current User (Protected)

**Endpoint:** `GET /api/me`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200 OK):**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": {
            "id": 3,
            "name": "customer"
        },
        "wallet_balance": 1000000
    }
}
```

### 4. Logout (Protected)

**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200 OK):**
```json
{
    "status": "success",
    "message": "Logout berhasil."
}
```

---

## Customer APIs

### Shopping Cart

#### Get Cart

**Endpoint:** `GET /api/cart`

**Response:**
```json
{
    "status": "success",
    "data": {
        "items": [
            {
                "id": 1,
                "product_id": 5,
                "product": {
                    "id": 5,
                    "name": "Nasi Kuning",
                    "price": 15000,
                    "image_url": "/images/products/1.jpg"
                },
                "qty": 2,
                "price_snapshot": 15000,
                "subtotal": 30000
            }
        ],
        "total": 30000
    }
}
```

#### Add to Cart

**Endpoint:** `POST /api/cart/add/{product_id}`

**Request Body:**
```json
{
    "qty": 2
}
```

**Response (201 Created):**
```json
{
    "status": "success",
    "message": "Produk berhasil ditambahkan ke keranjang.",
    "data": {
        "id": 1,
        "product_id": 5,
        "qty": 2,
        "price_snapshot": 15000,
        "subtotal": 30000
    }
}
```

#### Update Cart Item

**Endpoint:** `PUT /api/cart/update/{item_id}`

**Request Body:**
```json
{
    "qty": 3
}
```

#### Remove Item from Cart

**Endpoint:** `DELETE /api/cart/remove/{item_id}`

#### Clear Cart

**Endpoint:** `DELETE /api/cart/clear`

---

### Checkout

#### Process Checkout

**Endpoint:** `POST /api/checkout`

**Response (201 Created):**
```json
{
    "status": "success",
    "message": "Checkout berhasil dilakukan.",
    "data": {
        "order_id": 10,
        "total_paid": 150000,
        "order": {
            "id": 10,
            "user_id": 1,
            "total": 150000,
            "status": "pending",
            "placed_at": "2025-12-17T10:30:00Z",
            "items": [
                {
                    "id": 1,
                    "product_id": 5,
                    "qty": 2,
                    "price": 15000,
                    "subtotal": 30000,
                    "product": {...}
                }
            ]
        }
    }
}
```

---

### Wallet

#### Check Balance

**Endpoint:** `GET /api/wallet/balance`

**Response:**
```json
{
    "status": "success",
    "data": {
        "balance": 1000000,
        "formatted": "Rp 1.000.000"
    }
}
```

#### Top Up Wallet

**Endpoint:** `POST /api/wallet/topup`

**Request Body:**
```json
{
    "amount": 500000
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Top up berhasil.",
    "data": {
        "current_balance": 1500000,
        "formatted": "Rp 1.500.000"
    }
}
```

#### Get Transaction History

**Endpoint:** `GET /api/wallet/transactions`

**Query Parameters:**
- `page` (optional, default: 1)
- `per_page` (optional, default: 20)

**Response:**
```json
{
    "status": "success",
    "data": {
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "amount": 500000,
                "title": "Top Up Saldo",
                "type": "topup",
                "created_at": "2025-12-17T10:00:00Z"
            }
        ],
        "pagination": {...}
    }
}
```

---

### Activities / Orders

#### Get Customer Orders

**Endpoint:** `GET /api/activities`

**Query Parameters:**
- `status` (optional) - pending, paid, shipped, completed, cancelled
- `search` (optional) - search by product name or order ID
- `start_date` (optional) - filter by date range
- `end_date` (optional)

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 10,
            "user_id": 1,
            "total": 150000,
            "status": "pending",
            "placed_at": "2025-12-17T10:30:00Z",
            "items": [
                {
                    "id": 1,
                    "product": {
                        "id": 5,
                        "name": "Nasi Kuning",
                        "price": 15000
                    },
                    "qty": 2,
                    "price": 15000
                }
            ]
        }
    ]
}
```

#### Get Order Details

**Endpoint:** `GET /api/activities/{order_id}`

**Response:**
```json
{
    "status": "success",
    "data": {...}
}
```

#### Get Order Tracking

**Endpoint:** `GET /api/activities/{order_id}/track`

**Response:**
```json
{
    "status": "success",
    "data": {
        "tracking": [
            {
                "status": "pending",
                "label": "Pesanan Dibuat",
                "completed": true,
                "time": "2025-12-17T10:30:00Z"
            },
            {
                "status": "paid",
                "label": "Pembayaran Berhasil",
                "completed": false,
                "time": null
            }
        ]
    }
}
```

---

## Merchant APIs

### Merchant Products

#### Get My Products

**Endpoint:** `GET /api/merchant/products`

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 5,
            "merchant_id": 2,
            "name": "Nasi Kuning",
            "description": "Nasi kuning lezat dengan bumbu rempah",
            "price": 15000,
            "stock": 100,
            "image_url": "/images/products/1.jpg",
            "category": "Food",
            "prep_time": 15,
            "is_available": true,
            "created_at": "2025-12-17T09:00:00Z"
        }
    ]
}
```

#### Create Product

**Endpoint:** `POST /api/merchant/products`

**Request Body (Form Data):**
```
name: "Nasi Kuning"
description: "Nasi kuning lezat dengan bumbu rempah"
price: 15000
stock: 100
category: "Food"
image: (file upload, optional)
```

**Response (201 Created):**
```json
{
    "status": "success",
    "message": "Produk berhasil ditambahkan.",
    "data": {
        "id": 5,
        "merchant_id": 2,
        "name": "Nasi Kuning",
        ...
    }
}
```

#### Update Product

**Endpoint:** `PUT /api/merchant/products/{product_id}`

#### Delete Product

**Endpoint:** `DELETE /api/merchant/products/{product_id}`

---

### Merchant Orders

#### Get My Orders

**Endpoint:** `GET /api/merchant/orders`

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 10,
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "total": 150000,
            "status": "pending",
            "items": [
                {
                    "product_id": 5,
                    "qty": 2,
                    "price": 15000,
                    "product": {
                        "id": 5,
                        "merchant_id": 2,
                        "name": "Nasi Kuning"
                    }
                }
            ]
        }
    ]
}
```

#### Update Order Status

**Endpoint:** `PUT /api/merchant/orders/{order_id}/status`

**Request Body:**
```json
{
    "status": "paid"
}
```

**Status Values:** `pending`, `paid`, `shipped`, `completed`, `cancelled`

---

## Admin APIs

### Admin Orders

#### Get All Orders

**Endpoint:** `GET /api/admin/orders`

**Query Parameters:**
- `status` (optional)

#### Get Order Details

**Endpoint:** `GET /api/admin/orders/{order_id}`

#### Update Order Status

**Endpoint:** `PUT /api/admin/orders/{order_id}/status`

---

### Admin Users

#### Get All Users

**Endpoint:** `GET /api/admin/users`

**Query Parameters:**
- `role` (optional) - admin, merchant, customer

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": {
                "id": 3,
                "name": "customer"
            },
            "wallet_balance": 1000000,
            "created_at": "2025-12-17T09:00:00Z"
        }
    ]
}
```

#### Get User Details

**Endpoint:** `GET /api/admin/users/{user_id}`

#### Update User Role

**Endpoint:** `PUT /api/admin/users/{user_id}/role`

**Request Body:**
```json
{
    "role": "merchant"
}
```

#### Deactivate User

**Endpoint:** `PUT /api/admin/users/{user_id}/deactivate`

#### Activate User

**Endpoint:** `PUT /api/admin/users/{user_id}/activate`

---

## Error Handling

### Standard Error Response

```json
{
    "status": "error",
    "message": "Error description",
    "error": "Detailed error message (optional)"
}
```

### HTTP Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request parameters
- `401 Unauthorized` - Missing or invalid token
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation errors
- `500 Internal Server Error` - Server error

### Common Validation Errors

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["Email sudah terdaftar"],
        "password": ["Password minimal 8 karakter"]
    }
}
```

---

## React Integration Guide

### 1. Setup API Client

**`src/api/client.ts`:**
```typescript
import axios from 'axios';

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://127.0.0.1:8000/api';

const client = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Add token to requests
client.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Handle errors
client.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Clear token and redirect to login
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default client;
```

### 2. Authentication Service

**`src/services/authService.ts`:**
```typescript
import client from '../api/client';

export interface LoginPayload {
    email: string;
    password: string;
}

export interface RegisterPayload extends LoginPayload {
    name: string;
    password_confirmation: string;
    role?: number;
}

export const authService = {
    register: (data: RegisterPayload) =>
        client.post('/register', data),
    
    login: (data: LoginPayload) =>
        client.post('/login', data),
    
    me: () =>
        client.get('/me'),
    
    logout: () =>
        client.post('/logout'),
};
```

### 3. Cart Service

**`src/services/cartService.ts`:**
```typescript
import client from '../api/client';

export const cartService = {
    getCart: () =>
        client.get('/cart'),
    
    addToCart: (productId: number, qty: number) =>
        client.post(`/cart/add/${productId}`, { qty }),
    
    updateQty: (itemId: number, qty: number) =>
        client.put(`/cart/update/${itemId}`, { qty }),
    
    removeItem: (itemId: number) =>
        client.delete(`/cart/remove/${itemId}`),
    
    clearCart: () =>
        client.delete('/cart/clear'),
};
```

### 4. React Hook Example

**`src/hooks/useCart.ts`:**
```typescript
import { useState, useEffect } from 'react';
import { cartService } from '../services/cartService';

export const useCart = () => {
    const [cart, setCart] = useState(null);
    const [loading, setLoading] = useState(false);

    const fetchCart = async () => {
        setLoading(true);
        try {
            const response = await cartService.getCart();
            setCart(response.data.data);
        } catch (error) {
            console.error('Failed to fetch cart:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchCart();
    }, []);

    return { cart, loading, refetch: fetchCart };
};
```

### 5. Environment Configuration

**`.env`:**
```
REACT_APP_API_URL=http://127.0.0.1:8000/api
```

---

## Notes

✅ **API Siap Digunakan untuk React Frontend**

- Semua endpoints sudah diperbaiki dan tested
- Error handling sudah implemented
- Authentication dengan Sanctum tokens
- CORS ready untuk React development
- Database structure sudah optimal

### Next Steps

1. Setup React project dengan Vite/Create React App
2. Install dependencies: `npm install axios react-query`
3. Create API client dan services seperti contoh di atas
4. Build UI components untuk konsumsi API
5. Test semua endpoints dengan React apps

---

## Support

Untuk issues atau pertanyaan:
- Check database migrations: `php artisan migrate:status`
- Check routes: `php artisan route:list`
- Check logs: `storage/logs/laravel.log`

Selamat mengembangkan! 🚀
