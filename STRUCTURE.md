# 🏛️ Professional Application Structure

## Application Architecture

```
app/Http/
├── Controllers/
│   ├── Controller.php (base)
│   ├── Auth/
│   │   └── AuthController.php ✓
│   ├── Customer/
│   │   ├── CartController.php ✓
│   │   ├── CheckoutController.php ✓
│   │   ├── WalletController.php ✓
│   │   └── ActivityController.php ✓
│   ├── Merchant/
│   │   ├── ProductController.php ✓
│   │   └── OrderController.php ✓
│   └── Admin/
│       ├── OrderController.php ✓
│       └── UserController.php ✓
│
├── Middleware/
│   ├── Authenticate.php ✓
│   ├── RoleMiddleware.php ✓ (supports both name & id)
│   ├── TrustProxies.php ✓
│   ├── TrimStrings.php ✓
│   ├── EncryptCookies.php ✓
│   ├── VerifyCsrfToken.php ✓
│   ├── PreventRequestsDuringMaintenance.php ✓
│   └── ValidateSignature.php ✓
│
├── Kernel.php ✓ (updated)
├── Requests/ (validation)
└── Resources/ (response formatting)

routes/
├── api.php ✓ (single source - 30+ endpoints)
├── web.php ✓ (landing page)
└── console.php ✓ (commands)

app/Models/
├── User.php ✓
├── Role.php ✓
├── Product.php ✓
├── Cart.php ✓
├── CartItem.php ✓
├── Order.php ✓
├── OrderItem.php ✓
├── WalletTransaction.php ✓
├── Activity.php ✓
└── * (all complete with relationships)
```

## Endpoint Organization

### Public Endpoints
```
POST   /api/register              - User registration
POST   /api/login                 - User login (get token)
```

### Customer Endpoints (auth:sanctum)
```
GET    /api/me                    - Get current user
POST   /api/logout                - Logout

Cart Management
GET    /api/cart                  - View cart
POST   /api/cart/add/{id}         - Add to cart
PUT    /api/cart/update/{id}      - Update quantity
DELETE /api/cart/remove/{id}      - Remove item
DELETE /api/cart/clear            - Clear cart

Checkout & Orders
POST   /api/checkout              - Place order
GET    /api/activities            - Order history
GET    /api/activities/{id}       - View order details
GET    /api/activities/{id}/track - Track order

Wallet
GET    /api/wallet/balance        - Check balance
GET    /api/wallet/transactions   - Transaction history
POST   /api/wallet/topup          - Top up balance
```

### Merchant Endpoints (auth:sanctum + role:merchant)
```
Products
GET    /api/merchant/products     - List products
POST   /api/merchant/products     - Create product
PUT    /api/merchant/products/{id}- Update product
DELETE /api/merchant/products/{id}- Delete product

Orders
GET    /api/merchant/orders       - List orders
PUT    /api/merchant/orders/{id}/status - Update status
```

### Admin Endpoints (auth:sanctum + role:admin)
```
Orders
GET    /api/admin/orders          - All orders
GET    /api/admin/orders/{id}     - Order details
PUT    /api/admin/orders/{id}/status - Update status

Users
GET    /api/admin/users           - List users
GET    /api/admin/users/{id}      - User details
PUT    /api/admin/users/{id}/role - Change role
PUT    /api/admin/users/{id}/deactivate - Deactivate
PUT    /api/admin/users/{id}/activate   - Activate
```

## Code Quality Standards

### ✅ Enforced
- [ ] Single responsibility per controller
- [ ] Route organization by role
- [ ] Middleware for authentication & authorization
- [ ] Model relationships defined
- [ ] Exception handling implemented
- [ ] Type hints in all methods
- [ ] Consistent naming conventions
- [ ] No unused code or imports
- [ ] Professional error responses

### Database Tables
```sql
users                    -- User accounts
roles                    -- User roles (admin, merchant, customer)
products                 -- Product catalog
carts                    -- Shopping carts
cart_items               -- Items in carts
orders                   -- Placed orders
order_items              -- Items in orders
wallet_transactions      -- Wallet history
activities               -- Order tracking
personal_access_tokens   -- Sanctum API tokens
sessions                 -- Session management
```

## Technologies

- **Framework:** Laravel 11
- **Authentication:** Laravel Sanctum (token-based API)
- **Database:** MySQL
- **Frontend:** Vue.js 3 / React (Inertia.js)
- **API Style:** RESTful
- **Rate Limiting:** Built-in (throttle:api)

## Deployment Ready

✅ No unused files  
✅ No code duplication  
✅ Professional structure  
✅ Clear separation of concerns  
✅ Scalable architecture  
✅ All endpoints documented  
✅ Error handling in place  

**Status: Production Ready** 🚀
