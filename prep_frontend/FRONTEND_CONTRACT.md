# TEL-U SHOP Frontend Contract (Backend Frozen)

## Base URL
- {{API_BASE_URL}}/api
- Example: https://api.example.com/api

## Authentication flow
- POST /register or /login returns access_token (Sanctum).
- Store the token and send Authorization: Bearer <token> on all authenticated requests.
- GET /me to hydrate user + role.
- POST /logout revokes all tokens for the user.

## Global request headers
- Accept: application/json
- Content-Type: application/json
- Authorization: Bearer <token> (required for authenticated routes)
- File uploads: multipart/form-data (avatar, image)

## Global response envelope
All JSON responses are normalized by TransformApiResponse middleware:

Success
```json
{
  "success": true,
  "message": "optional",
  "data": ..., 
  "errors": null
}
```

Error
```json
{
  "success": false,
  "message": "error message",
  "data": null,
  "errors": { "field": ["message"] } | "string" | null
}
```

Notes:
- Validation errors (422) include an errors map.
- Some endpoints return only a message; data may be an empty array/object.
- The middleware converts controller "status" fields into a boolean "success" field.

## Role definitions and access boundaries
- admin (role_id=1)
- merchant (role_id=2)
- customer (role_id=3)
- Roles must exist in the roles table (seeded on fresh DBs) or registration will return 422.
- Merchant routes require role=merchant AND merchant_status=approved.
- All authenticated routes require is_banned=false (not-banned middleware).

## Pagination and sorting standard
- per_page: default 20 unless noted, max 100
- page: page number
- sort_by: endpoint specific allowlist
- sort_order: asc|desc
- search: string (where supported)
- Response is a Laravel paginator object (data array + pagination meta).

## Common data shapes
- Timestamps are strings in the default Laravel format (e.g., 2025-12-20 19:30:00).
- Paginator<T>: { data: T[], current_page, per_page, total, last_page, from, to, links }
- User: id, name, username, email, nim, kelas, phone, avatar_url, wallet_balance, role_id, merchant_status, is_banned, banned_at, created_at, updated_at
- UserLite (login): id, name, email, role_id
- Role: id, name, display_name, created_at, updated_at
- CustomerProduct (customer catalog): id, name, description, price, formatted_price, stock, stock_status, prep_time, image_url, category, merchant {id,name}|null, created_at, updated_at? (detail only)
- Product (raw model): id, merchant_id, user_id, name, description, price, stock, image_url, category, category_id, prep_time, is_available, created_at, updated_at
- CartItemView: id, product_id, qty, price_snapshot, subtotal, product (Product raw)
- Order: id, user_id, total_amount, payment_method, status, placed_at, created_at, updated_at, items[], user?
- OrderItem: id, order_id, product_id, qty, price, subtotal, product?
- Review: id, user_id, product_id, rating, comment, created_at, updated_at, user?, product?
- WishlistItem: id, user_id, product_id, created_at, updated_at, product?
- WalletTransaction: id, user_id, amount, title, order_id, type, description, created_at, updated_at
- LoyaltyPoint: id, user_id, points, type (earned|redeemed|expired), description, order_id, created_at, updated_at
- Notification: id, user_id, type, title, message, data, is_read, read_at, created_at, updated_at
- Reward (loyalty): id, name, points_required, description, type, can_redeem

## Endpoint list (grouped by role)

### Public (no auth)
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| POST | /register | public | - | name, username, email, password, password_confirmation, nim, kelas, phone, role (2 or 3) | { user: User, access_token, token_type } | 422, 500. role must exist in roles table. role=2 creates merchant with merchant_status=pending. role=3 creates customer with merchant_status=approved. |
| POST | /login | public | - | email, password | { user: UserLite, access_token, token_type } | 401 invalid credentials, 403 banned, 422 validation. |

### Authenticated (any role)
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /me | any-authenticated | - | - | User with role | 401, 403 |
| PUT | /profile | any-authenticated | - | name?, email?, avatar? (multipart/form-data; jpeg/png/jpg; max 2MB) | User | 401, 403, 422 |
| PUT | /change-password | any-authenticated | - | current_password, new_password, new_password_confirmation | empty data | 401, 403, 422 (current password invalid also returns 422) |
| POST | /logout | any-authenticated | - | - | empty data | 401, 403 |

### Customer - Products
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /products | customer | category, search, per_page, page, sort_by (name|price|created_at), sort_order | - | Paginator<CustomerProduct> | 401, 403. Only available products with approved merchants. |
| GET | /products/{id} | customer | - | - | CustomerProduct | 401, 403, 404 |
| GET | /products/categories | customer | - | - | string[] | 401, 403 |

### Customer - Cart
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /cart | customer | - | - | { items: CartItemView[], total } | 401, 403 |
| POST | /cart/add/{product} | customer | - | qty (int, min 1, max stock) | { id, product_id, qty, price_snapshot, subtotal } | 401, 403, 404 (product not found), 422 (stock/availability) |
| PUT | /cart/update/{item} | customer | - | qty (int, min 1, max stock) | { id, qty, subtotal } | 401, 403 (not owner), 404, 422 (stock/merchant status) |
| DELETE | /cart/remove/{item} | customer | - | - | empty data | 401, 403 (not owner), 404 |
| DELETE | /cart/clear | customer | - | - | empty data | 401, 403 |

### Customer - Checkout
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| POST | /checkout | customer | - | payment_method (wallet|cash), notes? | { order: Order with items.product, points_earned } | 401, 403, 422 (empty cart, insufficient balance, stock or merchant issues). notes is accepted but not stored. |

### Customer - Wallet
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /wallet/balance | customer | - | - | { balance, formatted } | 401, 403 |
| GET | /wallet/transactions | customer | type, per_page, page, sort_by (created_at|amount|type), sort_order | - | Paginator<WalletTransaction> | 401, 403 |
| POST | /wallet/topup | customer | - | amount (>=1000) | { current_balance, formatted } | 401, 403, 422 |

### Customer - Activities (Orders)
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /activities | customer | status, start_date, end_date, search, per_page (default 10), page, sort_by (created_at|total_amount|status), sort_order | - | { stats: { total_orders, pending, completed, total_spent }, data: Paginator<Order> } | 401, 403 |
| GET | /activities/{id} | customer | - | - | Order with items.product.merchant | 401, 403, 404 |
| GET | /activities/{id}/track | customer | - | - | { order_id, current_status, timeline: [{ status, label, time, completed }] } | 401, 403, 404. timeline time fields may be null. |

### Customer - Wishlist
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /wishlist | customer | search, per_page, page, sort_by (created_at), sort_order | - | Paginator<WishlistItem> | 401, 403 |
| POST | /wishlist/add/{product} | customer | - | - | WishlistItem with product | 401, 403, 404, 422 (already in wishlist or product unavailable) |
| DELETE | /wishlist/remove/{product} | customer | - | - | empty data | 401, 403, 404 (not in wishlist) |
| GET | /wishlist/check/{product} | customer | - | - | { in_wishlist: boolean } | 401, 403 |

### Customer - Reviews
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /products/{product}/reviews | customer | per_page, page, sort_by (created_at|rating), sort_order | - | Paginator<Review> (user id+name) | 401, 403 |
| POST | /products/{product}/reviews | customer | - | rating (1-5), comment? | Review (with user id+name) | 401, 403, 422 (already reviewed or validation) |
| GET | /reviews/{review} | customer | - | - | Review (user id+name, product id+name) | 401, 403 (not owner), 404 |
| PUT | /reviews/{review} | customer | - | rating?, comment? | Review (user id+name) | 401, 403 (not owner), 404, 422 |
| DELETE | /reviews/{review} | customer | - | - | empty data | 401, 403 (not owner), 404 |
| GET | /my-reviews | customer | per_page, page, sort_by (created_at|rating), sort_order | - | Paginator<Review> (product id+name) | 401, 403 |

### Customer - Loyalty
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /loyalty/balance | customer | - | - | { balance } | 401, 403 |
| GET | /loyalty/history | customer | type, per_page, page, sort_by (created_at|points|type), sort_order | - | Paginator<LoyaltyPoint> | 401, 403 |
| POST | /loyalty/redeem | customer | - | points (>=100), description | { redeemed_points, remaining_balance } | 401, 403, 400 (insufficient), 422 |
| GET | /loyalty/rewards | customer | - | - | Reward[] | 401, 403 |

### Customer - Notifications
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /notifications | customer | type, is_read, per_page, page, sort_by (created_at|type), sort_order | - | Paginator<Notification> | 401, 403 |
| GET | /notifications/stats | customer | - | - | { total, unread } | 401, 403 |
| GET | /notifications/{id} | customer | - | - | Notification | 401, 403, 404 |
| PUT | /notifications/{id}/read | customer | - | - | empty data | 401, 403, 404 |
| PUT | /notifications/mark-read | customer | - | notification_ids (int[]) | empty data | 401, 403, 422 |
| PUT | /notifications/mark-all-read | customer | - | - | empty data | 401, 403 |
| DELETE | /notifications/{id} | customer | - | - | empty data | 401, 403, 404 |

### Merchant - Products
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /merchant/products | merchant (approved) | search, category, is_available, per_page, page, sort_by (name|price|stock|created_at), sort_order | - | Paginator<Product> | 401, 403 (role/approval) |
| POST | /merchant/products | merchant (approved) | - | name, description, price, stock, category, prep_time, is_available, image? (multipart/form-data) | Product | 401, 403, 422 |
| PUT | /merchant/products/{id} | merchant (approved) | - | name?, description?, price?, stock?, image? (multipart/form-data) | Product | 401, 403, 404, 422. category/prep_time/is_available are not updatable here. |
| DELETE | /merchant/products/{id} | merchant (approved) | - | - | empty data | 401, 403, 404 |

### Merchant - Orders
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /merchant/orders | merchant (approved) | status, start_date, end_date, search, per_page, page, sort_by (created_at|status|total_amount), sort_order | - | Paginator<Order> (items filtered to this merchant; user included) | 401, 403 |
| PUT | /merchant/orders/{id}/status | merchant (approved) | - | status (pending|processing|paid|shipped|completed|cancelled) | Order | 401, 403 (no access), 404, 422. Updates the whole order status. |

### Merchant - Analytics
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /merchant/analytics/dashboard | merchant (approved) | period (days, default 30) | - | { overview: { total_products, total_sales, total_revenue, average_rating }, top_products: Product[], sales_trend: [{ date, total_quantity, total_revenue }], period_days } | 401, 403 |
| GET | /merchant/analytics/sales | merchant (approved) | start_date, end_date | - | { product_sales: [{ product_id, product_name, total_quantity, total_revenue, average_rating }], daily_sales: [{ date, total_quantity, total_revenue, total_orders }], order_status: [{ status, count }], date_range: { start_date, end_date } } | 401, 403 |
| GET | /merchant/analytics/products | merchant (approved) | - | - | [{ id, name, price, stock, is_available, total_sold, total_revenue, average_rating, review_count, performance_score }] | 401, 403 |
| GET | /merchant/analytics/customers | merchant (approved) | - | - | { top_customers: [{ id, name, email, total_orders, total_quantity, total_spent, last_order_date }], customer_acquisition: [{ first_purchase_date, new_customers }] } | 401, 403 |

### Admin - Orders
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /admin/orders | admin | status, start_date, end_date, search, per_page (default 15), page, sort_by (created_at|status|total_amount), sort_order | - | Paginator<Order> (user id+name+email, items.product id+name) | 401, 403 |
| GET | /admin/orders/{id} | admin | - | - | Order (user id+name+email, items.product id+name+price) | 401, 403, 404 |
| PUT | /admin/orders/{id}/status | admin | - | status (pending|processing|completed|cancelled) | Order | 401, 403, 404, 422 |

### Admin - Users
| Method | URL | Role | Query params | Body | Success data | Errors/Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GET | /admin/users | admin | role, merchant_status, is_banned, search, per_page, page, sort_by (name|email|created_at), sort_order | - | Paginator<User> (role included) | 401, 403 |
| GET | /admin/users/{id} | admin | - | - | User with role | 401, 403, 404 |
| PUT | /admin/users/{id}/role | admin | - | role (admin|merchant|customer) | User with role | 401, 403, 404, 422 |
| PUT | /admin/users/{id}/deactivate | admin | - | - | User | 401, 403, 404. No persistence (no is_active field). |
| PUT | /admin/users/{id}/activate | admin | - | - | User | 401, 403, 404. No persistence (no is_active field). |
| PUT | /admin/merchants/{id}/approve | admin | - | - | User | 401, 403, 404, 422 (user not merchant) |
| PUT | /admin/users/{id}/ban | admin | - | - | User | 401, 403, 404, 400 (cannot ban self) |
| PUT | /admin/users/{id}/unban | admin | - | - | User | 401, 403, 404 |

## Ownership rules (frontend must NOT assume)
- Do not call activity endpoints not registered in routes (confirm, cancel, review, reorder, invoice). They will 404.
- Do not assume a merchant can access routes unless merchant_status=approved.
- Do not assume /admin/users/{id}/activate or /deactivate changes server state (no is_active field).
- Do not assume cart item id equals product id; updates/removals use cart item id.
- Do not assume order is single-merchant; merchant status updates change the whole order.
- Do not assume image_url or avatar_url are absolute URLs (usually a storage path).
