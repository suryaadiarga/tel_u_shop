TEL-U SHOP API Contract (Final)

Base URL:
- /api

Auth:
- Bearer token (Sanctum)
- Header: Authorization: Bearer {token}

Response Contract (Global)
- Success:
  {
    "status": "success",
    "message": "optional",
    "data": ...,
    "errors": null
  }
- Error:
  {
    "status": "error",
    "message": "error message",
    "data": null,
    "errors": ... // validation map or null
  }

Standard Errors (Examples)
- 401 Unauthorized
  {
    "status": "error",
    "message": "Unauthorized.",
    "data": null,
    "errors": null
  }
- 403 Forbidden
  {
    "status": "error",
    "message": "Forbidden.",
    "data": null,
    "errors": null
  }
- 422 Validation error
  {
    "status": "error",
    "message": "Validation error.",
    "data": null,
    "errors": {
      "email": ["The email field is required."]
    }
  }
- 404 Not found
  {
    "status": "error",
    "message": "Not found.",
    "data": null,
    "errors": null
  }

Standard List Query Params
- per_page: int (default 20, max 100)
- page: int
- sort_by: allowed fields per endpoint
- sort_order: asc|desc
- search: string (where applicable)

Public (no auth)
- POST /register
- POST /login

Authenticated (all roles)
- GET /me (auth: Bearer, any role)
- PUT /profile (auth: Bearer, any role)
- PUT /change-password (auth: Bearer, any role)
- POST /logout (auth: Bearer, any role)

Role: Customer (auth required, role=customer)

Products
- GET /products
  - Filters: category, search
  - Sort: name, price, created_at
- GET /products/{id}
- GET /products/categories

Cart
- GET /cart
- POST /cart/add/{product}
  - Body: { "qty": 1 }
- PUT /cart/update/{item}
  - Body: { "qty": 1 }
- DELETE /cart/remove/{item}
- DELETE /cart/clear

Checkout
- POST /checkout
  - Body: { "payment_method": "wallet|cash", "notes": "optional" }

Wishlist
- GET /wishlist
  - Filters: search
  - Sort: created_at
- POST /wishlist/add/{product}
- DELETE /wishlist/remove/{product}
- GET /wishlist/check/{product}

Reviews
- GET /products/{product}/reviews
  - Sort: created_at, rating
- POST /products/{product}/reviews
  - Body: { "rating": 1-5, "comment": "optional" }
- GET /reviews/{review}
- PUT /reviews/{review}
  - Body: { "rating": 1-5, "comment": "optional" }
- DELETE /reviews/{review}
- GET /my-reviews
  - Sort: created_at, rating

Wallet
- GET /wallet/balance
- GET /wallet/transactions
  - Filters: type
  - Sort: created_at, amount, type
- POST /wallet/topup
  - Body: { "amount": 1000 }

Loyalty
- GET /loyalty/balance
- GET /loyalty/history
  - Filters: type
  - Sort: created_at, points, type
- POST /loyalty/redeem
  - Body: { "points": 100, "description": "..." }
- GET /loyalty/rewards

Activities (Orders)
- GET /activities
  - Filters: status, start_date, end_date, search
  - Sort: created_at, total_amount, status
- GET /activities/{id}
- GET /activities/{id}/track
- POST /activities/{id}/confirm
- POST /activities/{id}/cancel
- POST /activities/{id}/review/{orderItemId}
- POST /activities/{id}/reorder
- GET /activities/{id}/invoice

Notifications
- GET /notifications
  - Filters: type, is_read
  - Sort: created_at, type
- GET /notifications/{id}
- PUT /notifications/{id}/read
- PUT /notifications/mark-read
  - Body: { "notification_ids": [1,2] }
- PUT /notifications/mark-all-read
- DELETE /notifications/{id}

Role: Merchant (auth required, role=merchant, approved)

Products
- GET /merchant/products
  - Filters: category, search, is_available
  - Sort: name, price, stock, created_at
- POST /merchant/products
- PUT /merchant/products/{id}
- DELETE /merchant/products/{id}

Orders
- GET /merchant/orders
  - Filters: status, start_date, end_date, search
  - Sort: created_at, status, total_amount
- PUT /merchant/orders/{id}/status
  - Body: { "status": "pending|processing|paid|shipped|completed|cancelled" }

Analytics
- GET /merchant/analytics/dashboard
- GET /merchant/analytics/sales
- GET /merchant/analytics/products
- GET /merchant/analytics/customers

Role: Admin (auth required, role=admin)

Orders
- GET /admin/orders
  - Filters: status, start_date, end_date, search
  - Sort: created_at, status, total_amount
- GET /admin/orders/{id}
- PUT /admin/orders/{id}/status
  - Body: { "status": "pending|processing|completed|cancelled" }

Users
- GET /admin/users
  - Filters: role, merchant_status, is_banned, search
  - Sort: name, email, created_at
- GET /admin/users/{id}
- PUT /admin/users/{id}/role
  - Body: { "role": "admin|merchant|customer" }
- PUT /admin/users/{id}/deactivate
- PUT /admin/users/{id}/activate
- PUT /admin/merchants/{id}/approve
- PUT /admin/users/{id}/ban
- PUT /admin/users/{id}/unban

Success Example (List)
{
  "status": "success",
  "message": "optional",
  "data": {
    "data": [
      { "id": 1, "name": "Sample" }
    ],
    "current_page": 1,
    "per_page": 20,
    "total": 1
  },
  "errors": null
}
